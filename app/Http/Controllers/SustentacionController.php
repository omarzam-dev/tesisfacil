<?php

namespace App\Http\Controllers;

use App\Models\MatrizConsistencia;
use App\Models\SimulacionSustentacion;
use App\Models\UsoIa;
use App\Services\SimuladorSustentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SustentacionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $simulaciones = SimulacionSustentacion::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('sustentacion.index', compact('user', 'simulaciones'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $previa = [];

        // Repetir una simulación anterior con los mismos datos
        if ($request->filled('desde')) {
            $s = SimulacionSustentacion::where('user_id', $user->id)->find($request->integer('desde'));
            if ($s) {
                $previa = $s->only(['titulo', 'carrera', 'tono', 'planteamiento', 'metodologia', 'resultados', 'conclusiones']);
            }
        }

        // Tomar el planteamiento desde una matriz ya revisada
        if ($request->filled('matriz')) {
            $m = MatrizConsistencia::where('user_id', $user->id)->find($request->integer('matriz'));
            if ($m) {
                $previa['titulo'] = $previa['titulo'] ?? $m->titulo;
                $previa['planteamiento'] = $this->planteamientoDesdeMatriz($m);
            }
        }

        $matrices = MatrizConsistencia::where('user_id', $user->id)->latest()->take(10)->get(['id', 'titulo', 'created_at']);

        return view('sustentacion.create', compact('user', 'previa', 'matrices'));
    }

    public function store(Request $request, SimuladorSustentacion $simulador)
    {
        $user = auth()->user();

        $datos = $request->validate([
            'titulo'        => 'required|string|max:255',
            'carrera'       => 'nullable|string|max:120',
            'tono'          => 'required|in:' . implode(',', array_keys(SimulacionSustentacion::TONOS)),
            'planteamiento' => 'required|string|max:5000',
            'metodologia'   => 'required|string|max:3000',
            'resultados'    => 'required|string|max:3000',
            'conclusiones'  => 'nullable|string|max:2000',
        ], [], [
            'titulo'        => 'título',
            'planteamiento' => 'planteamiento',
            'metodologia'   => 'metodología',
            'resultados'    => 'resultados',
        ]);

        if (! $user->puedeUsarIa()) {
            return back()->withInput()->withErrors([
                'ia' => "Ya usaste tus {$user->limiteIa()} usos de IA de este mes. Se renuevan el día 1, o puedes mejorar tu plan.",
            ]);
        }

        // Se arma en memoria para generar las preguntas; se guarda solo si la IA responde bien
        $simulacion = new SimulacionSustentacion($datos + ['user_id' => $user->id]);

        try {
            $r = $simulador->generarPreguntas($simulacion);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['ia' => 'No se pudo preparar la simulación. ' . $e->getMessage()]);
        }

        $preguntas = collect($r['datos']['preguntas'] ?? [])
            ->filter(fn ($p) => ! empty($p['pregunta']))
            ->take(5)
            ->values()
            ->all();

        if (count($preguntas) === 0) {
            return back()->withInput()->withErrors(['ia' => 'La IA no generó preguntas. Intenta de nuevo.']);
        }

        DB::transaction(function () use ($user, $r, $simulacion, $preguntas) {
            $this->registrarUso($user->id, $r, true);
            $simulacion->preguntas = $preguntas;
            $simulacion->respuestas = [];
            $simulacion->estado = 'en_curso';
            $simulacion->save();
        });

        return redirect()->route('sustentacion.show', $simulacion);
    }

    public function show(SimulacionSustentacion $simulacion)
    {
        $this->autorizar($simulacion);

        return view('sustentacion.show', compact('simulacion'));
    }

    public function responder(Request $request, SimulacionSustentacion $simulacion, SimuladorSustentacion $simulador)
    {
        $this->autorizar($simulacion);

        $indice = $simulacion->indiceActual();
        if ($indice === null || $simulacion->finalizada()) {
            return redirect()->route('sustentacion.show', $simulacion);
        }

        $datos = $request->validate([
            'respuesta' => 'required|string|max:3000',
        ], [], ['respuesta' => 'respuesta']);

        try {
            $r = $simulador->evaluarRespuesta($simulacion, $indice, $datos['respuesta']);
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['ia' => 'No se pudo evaluar tu respuesta. ' . $e->getMessage()]);
        }

        $ev = $r['datos'];

        DB::transaction(function () use ($simulacion, $r, $ev, $datos) {
            // Evaluar respuestas no descuenta usos: la simulación ya contó 1 al crearse
            $this->registrarUso($simulacion->user_id, $r, false);

            $respuestas = $simulacion->respuestas ?? [];
            $respuestas[] = [
                'respuesta'  => $datos['respuesta'],
                'nota'       => max(0, min(20, (int) ($ev['nota'] ?? 0))),
                'comentario' => $ev['comentario'] ?? '',
                'que_falto'  => array_values(array_filter((array) ($ev['que_falto'] ?? []))),
                'consejo'    => $ev['consejo'] ?? '',
            ];
            $simulacion->respuestas = $respuestas;

            if (count($respuestas) >= $simulacion->totalPreguntas()) {
                $simulacion->estado = 'finalizada';
                $simulacion->nota_final = round(collect($respuestas)->avg('nota'), 1);
            }

            $simulacion->save();
        });

        return redirect()->route('sustentacion.show', $simulacion)->withFragment('pregunta-actual');
    }

    private function autorizar(SimulacionSustentacion $s): void
    {
        abort_unless((int) $s->user_id === (int) auth()->id(), 404);
    }

    private function registrarUso(int $userId, array $r, bool $consume): void
    {
        UsoIa::create([
            'user_id'        => $userId,
            'tipo'           => 'sustentacion',
            'modelo'         => $r['modelo'],
            'tokens_entrada' => $r['tokens_entrada'],
            'tokens_salida'  => $r['tokens_salida'],
            'costo_usd'      => $r['costo_usd'],
            'consume_uso'    => $consume,
        ]);
    }

    private function planteamientoDesdeMatriz(MatrizConsistencia $m): string
    {
        $partes = [
            'Problema general: ' . $m->problema_general,
            "Problemas específicos:\n" . $m->problemas_especificos,
            'Objetivo general: ' . $m->objetivo_general,
            "Objetivos específicos:\n" . $m->objetivos_especificos,
        ];
        if ($m->hipotesis_general) {
            $partes[] = 'Hipótesis general: ' . $m->hipotesis_general;
        }
        if ($m->hipotesis_especificas) {
            $partes[] = "Hipótesis específicas:\n" . $m->hipotesis_especificas;
        }
        if ($m->variables) {
            $partes[] = 'Variables: ' . $m->variables;
        }

        return implode("\n\n", $partes);
    }
}
