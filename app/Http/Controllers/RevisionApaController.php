<?php

namespace App\Http\Controllers;

use App\Models\RevisionApa;
use App\Services\Apa\ApaCitationValidator;
use App\Services\Apa\ApaDocxParser;
use App\Services\Apa\ApaReferenceValidator;
use App\Services\Apa\ApaStructureValidator;
use App\Services\Apa\ApaTextExtractor;
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

    public function store(
        Request $request,
        ApaDocxParser $parser,
        ApaStructureValidator $estructuraValidator,
        ApaTextExtractor $textExtractor,
        ApaCitationValidator $citasValidator,
        ApaReferenceValidator $referenciasValidator
    ) {
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
            $rutaAbsoluta = Storage::disk('local')->path($ruta);
            $edicion = $request->edicion_apa;

            // Estructura (márgenes, interlineado, fuente, encabezado)
            $metricas = $parser->extraer($rutaAbsoluta);
            $resultadoEstructura = $estructuraValidator->validar($metricas, $edicion);

            // Citas y referencias
            $parrafos = $textExtractor->extraerParrafos($rutaAbsoluta);
            $textoCompleto = implode("\n", $parrafos);

            $resultadoCitas = $citasValidator->validar($textoCompleto, $edicion);
            $resultadoReferencias = $referenciasValidator->validar(
                $parrafos,
                $resultadoCitas['citas'],
                $edicion
            );

            $revision->update([
                'estado' => 'completado',
                'resultado_estructura' => $resultadoEstructura,
                'resultado_citas' => $resultadoCitas,
                'resultado_referencias' => $resultadoReferencias,
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
