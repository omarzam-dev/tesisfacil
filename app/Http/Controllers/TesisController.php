<?php

namespace App\Http\Controllers;

use App\Models\Tesis;
use Illuminate\Http\Request;

class TesisController extends Controller
{
    public function index()
    {
        $tesis = Tesis::where('user_id', auth()->id())->latest()->get();

        return view('tesis.index', compact('tesis'));
    }

    public function create()
    {
        if (! auth()->user()->puedeCrearTesis()) {
            return redirect()->route('tesis.index')
                ->with('error', "Alcanzaste el límite de {$this->limiteLegible()} tesis de tu plan actual.");
        }

        return view('tesis.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->puedeCrearTesis()) {
            return redirect()->route('tesis.index')
                ->with('error', "Alcanzaste el límite de {$this->limiteLegible()} tesis de tu plan actual.");
        }

        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'linea_investigacion' => ['nullable', 'string', 'max:255'],
        ]);

        $tesis = Tesis::create([
            'user_id' => auth()->id(),
            'titulo' => $data['titulo'],
            'linea_investigacion' => $data['linea_investigacion'] ?? null,
            'estado' => 'en_progreso',
        ]);

        $capitulosPorDefecto = [
            'Capítulo I: Planteamiento del problema',
            'Capítulo II: Marco teórico',
            'Capítulo III: Metodología',
            'Capítulo IV: Resultados',
            'Capítulo V: Discusión y conclusiones',
        ];

        foreach ($capitulosPorDefecto as $orden => $nombre) {
            $tesis->capitulos()->create([
                'nombre' => $nombre,
                'orden' => $orden,
                'estado' => 'pendiente',
            ]);
        }

        return redirect()->route('tesis.show', $tesis);
    }

    public function show(Tesis $tesis)
    {
        abort_unless($tesis->user_id === auth()->id(), 403);

        $tesis->load('capitulos.versiones');

        return view('tesis.show', compact('tesis'));
    }

    public function descargarCompleta(Tesis $tesis, \App\Services\DocxMergerService $merger)
    {
        abort_unless($tesis->user_id === auth()->id(), 403);

        $capitulos = $tesis->capitulos()
            ->with('versiones')
            ->get()
            ->map(function ($capitulo) {
                $ultima = $capitulo->versiones->first();

                if (! $ultima) {
                    return null;
                }

                return [
                    'nombre' => $capitulo->nombre,
                    'ruta_absoluta' => \Storage::disk('local')->path($ultima->ruta_archivo),
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($capitulos)) {
            return back()->with('error', 'Ningún capítulo tiene un archivo subido todavía.');
        }

        $nombreSalida = \Illuminate\Support\Str::slug($tesis->titulo) . '-completa.docx';
        $rutaTemporal = storage_path('app/temp/' . uniqid('tesis_') . '.docx');

        if (! is_dir(dirname($rutaTemporal))) {
            mkdir(dirname($rutaTemporal), 0755, true);
        }

        try {
            $resultado = $merger->combinar($capitulos, $rutaTemporal);
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo combinar los capítulos: ' . $e->getMessage());
        }

        if (! empty($resultado['omitidos'])) {
            session()->flash('warning', 'Se omitieron estos capítulos por error al combinarlos: ' . implode(', ', $resultado['omitidos']));
        }

        return response()->download($resultado['ruta'], $nombreSalida)->deleteFileAfterSend(true);
    }

    protected function limiteLegible(): int
    {
        return auth()->user()->limiteTesis();
    }
}
