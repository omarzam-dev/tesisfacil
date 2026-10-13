<?php

namespace App\Http\Controllers;

use App\Models\MatrizConsistencia;
use App\Models\UsoIa;
use App\Services\RevisorMatriz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class MatrizConsistenciaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $revisiones = MatrizConsistencia::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('matriz.index', compact('user', 'revisiones'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // "Corregir y revisar de nuevo": precarga los datos de una revisión anterior
        $previa = null;
        if ($request->filled('desde')) {
            $previa = MatrizConsistencia::where('user_id', $user->id)->find($request->integer('desde'));
        }

        return view('matriz.create', compact('user', 'previa'));
    }

    public function store(Request $request, RevisorMatriz $revisor)
    {
        $user = auth()->user();

        $datos = $request->validate([
            'titulo'                => 'nullable|string|max:200',
            'enfoque'               => 'required|in:' . implode(',', array_keys(MatrizConsistencia::ENFOQUES)),
            'problema_general'      => 'required|string|max:1500',
            'problemas_especificos' => 'required|string|max:4000',
            'objetivo_general'      => 'required|string|max:1500',
            'objetivos_especificos' => 'required|string|max:4000',
            'hipotesis_general'     => 'nullable|string|max:1500',
            'hipotesis_especificas' => 'nullable|string|max:4000',
            'variables'             => 'nullable|string|max:2000',
        ], [], [
            'problema_general'      => 'problema general',
            'problemas_especificos' => 'problemas específicos',
            'objetivo_general'      => 'objetivo general',
            'objetivos_especificos' => 'objetivos específicos',
        ]);

        if (! $user->puedeUsarIa()) {
            return back()->withInput()->withErrors([
                'ia' => "Ya usaste tus {$user->limiteIa()} usos de IA de este mes. Se renuevan el día 1, o puedes mejorar tu plan.",
            ]);
        }

        try {
            $respuesta = $revisor->revisar($datos);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'ia' => 'No se pudo completar la revisión. ' . $e->getMessage(),
            ]);
        }

        $resultado = $respuesta['datos'];

        $matriz = DB::transaction(function () use ($user, $datos, $resultado, $respuesta) {
            UsoIa::create([
                'user_id'        => $user->id,
                'tipo'           => 'matriz',
                'modelo'         => $respuesta['modelo'],
                'tokens_entrada' => $respuesta['tokens_entrada'],
                'tokens_salida'  => $respuesta['tokens_salida'],
                'costo_usd'      => $respuesta['costo_usd'],
            ]);

            return MatrizConsistencia::create($datos + [
                'user_id'   => $user->id,
                'puntaje'   => isset($resultado['puntaje']) ? max(0, min(100, (int) $resultado['puntaje'])) : null,
                'veredicto' => $resultado['veredicto'] ?? null,
                'resultado' => $resultado,
            ]);
        });

        return redirect()->route('matriz.show', $matriz);
    }

    public function show(MatrizConsistencia $matriz)
    {
        abort_unless((int) $matriz->user_id === (int) auth()->id(), 404);

        return view('matriz.show', compact('matriz'));
    }
}
