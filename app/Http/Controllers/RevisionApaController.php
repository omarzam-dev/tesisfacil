<?php

namespace App\Http\Controllers;

use App\Models\RevisionApa;
use App\Services\Apa\ApaDocxParser;
use App\Services\Apa\ApaStructureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RevisionApaController extends Controller
{
    public function index()
    {
        $revisiones = RevisionApa::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('apa.index', compact('revisiones'));
    }

    public function create()
    {
        return view('apa.create');
    }

    public function store(Request $request, ApaDocxParser $parser, ApaStructureValidator $validator)
    {
        $request->validate([
            'documento' => ['required', 'file', 'mimes:docx', 'max:10240'],
            'edicion_apa' => ['required', 'in:APA6,APA7'],
        ]);

        $archivo = $request->file('documento');
        $ruta = $archivo->store('revisiones_apa/' . auth()->id(), 'local');

        $revision = RevisionApa::create([
            'user_id' => auth()->id(),
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'ruta_archivo' => $ruta,
            'edicion_apa' => $request->edicion_apa,
            'estado' => 'procesando',
        ]);

        try {
            $metricas = $parser->extraer(Storage::disk('local')->path($ruta));
            $resultado = $validator->validar($metricas, $request->edicion_apa);

            $revision->update([
                'estado' => 'completado',
                'resultado_estructura' => $resultado,
            ]);
        } catch (\Throwable $e) {
            $revision->update([
                'estado' => 'error',
                'error_mensaje' => 'No se pudo procesar el documento: ' . $e->getMessage(),
            ]);
        }

        return redirect()->route('apa.show', $revision);
    }

    public function show(RevisionApa $revision)
    {
        abort_unless($revision->user_id === auth()->id(), 403);

        return view('apa.show', compact('revision'));
    }
}
