<?php

namespace App\Http\Controllers;

use App\Models\PerfilFormato;
use App\Models\RevisionApa;
use App\Services\Apa\ApaCitationValidator;
use App\Services\Apa\ApaDocxParser;
use App\Services\Apa\ApaReferenceValidator;
use App\Services\Apa\ApaStructureValidator;
use App\Services\Apa\ApaTextExtractor;
use App\Services\Apa\IsoCitationValidator;
use App\Services\Apa\IsoReferenceValidator;
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
        if (! auth()->user()->puedeRevisar()) {
            return redirect()->route('apa.index')
                ->with('error', "Alcanzaste el límite de {$this->limiteLegible()} revisiones este mes en tu plan actual.");
        }

        $normas = config('apa_rules');
        $perfiles = PerfilFormato::activos()->get();

        return view('apa.create', compact('normas', 'perfiles'));
    }

    public function store(
        Request $request,
        ApaDocxParser $parser,
        ApaStructureValidator $estructuraValidator,
        ApaTextExtractor $textExtractor,
        ApaCitationValidator $citasValidator,
        ApaReferenceValidator $referenciasValidator,
        IsoCitationValidator $isoCitasValidator,
        IsoReferenceValidator $isoReferenciasValidator
    ) {
        if (! auth()->user()->puedeRevisar()) {
            return redirect()->route('apa.index')
                ->with('error', "Alcanzaste el límite de {$this->limiteLegible()} revisiones este mes en tu plan actual.");
        }

        $request->validate([
            'documento' => ['required', 'file', 'mimes:docx', 'max:10240'],
            'edicion_apa' => ['required', 'in:' . implode(',', array_keys(config('apa_rules')))],
            'perfil_formato_id' => ['nullable', 'integer', 'exists:perfiles_formato,id'],
        ], [], [
            'edicion_apa' => 'norma',
            'perfil_formato_id' => 'formato de página',
        ]);

        $edicion = $request->edicion_apa;
        $norma = config("apa_rules.{$edicion}");
        $perfil = $request->filled('perfil_formato_id')
            ? PerfilFormato::activos()->find($request->integer('perfil_formato_id'))
            : null;

        $archivo = $request->file('documento');
        $ruta = $archivo->store('revisiones_apa/' . auth()->id(), 'local');

        $revision = RevisionApa::create([
            'user_id' => auth()->id(),
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'ruta_archivo' => $ruta,
            'edicion_apa' => $edicion,
            'perfil_formato_id' => $perfil?->id,
            'perfil_nombre' => $perfil?->nombre,
            'estado' => 'procesando',
        ]);

        try {
            $rutaAbsoluta = Storage::disk('local')->path($ruta);
            $esIso = ($norma['tipo'] ?? 'apa') === 'iso';

            // Formato de página: el perfil de la universidad manda; si no hay, el de la norma (solo APA lo define)
            $metricas = $parser->extraer($rutaAbsoluta);

            if ($perfil) {
                $reglasFormato = $perfil->reglas();
                $reglasFormato['requiere_running_head'] = (bool) ($norma['requiere_running_head'] ?? false);
                $resultadoEstructura = $estructuraValidator->validar($metricas, $reglasFormato);
            } elseif ($esIso) {
                $resultadoEstructura = $estructuraValidator->sinFormatoDefinido($edicion, $norma['nombre']);
            } else {
                $resultadoEstructura = $estructuraValidator->validar($metricas, $edicion);
            }

            $parrafos = $textExtractor->extraerParrafos($rutaAbsoluta);

            if ($esIso) {
                $resultadoCitas = $isoCitasValidator->validar($parrafos, $edicion);
                $resultadoReferencias = $isoReferenciasValidator->validar($parrafos, $resultadoCitas, $edicion);
            } else {
                $textoCompleto = implode("\n", $parrafos);
                $resultadoCitas = $citasValidator->validar($textoCompleto, $edicion);
                $resultadoReferencias = $referenciasValidator->validar($parrafos, $resultadoCitas['citas'], $edicion);
            }

            $revision->update([
                'estado' => 'completado',
                'resultado_estructura' => $resultadoEstructura,
                'resultado_citas' => $resultadoCitas,
                'resultado_referencias' => $resultadoReferencias,
            ]);
        } catch (\Throwable $e) {
            report($e);
            $revision->update([
                'estado' => 'error',
                'error_mensaje' => 'No se pudo procesar el documento: ' . $e->getMessage(),
            ]);
        }

        return redirect()->route('apa.show', $revision);
    }

    public function show(RevisionApa $revision)
    {
        abort_unless((int) $revision->user_id === (int) auth()->id(), 403);

        return view('apa.show', compact('revision'));
    }

    protected function limiteLegible(): int
    {
        return auth()->user()->limiteRevisiones();
    }
}
