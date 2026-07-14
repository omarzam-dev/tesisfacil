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
        // Límite del plan: max_tesis (compartido por el middleware ValidarLicencia)
        $limite = config('zsystem.max_tesis');
        $actuales = Tesis::where('user_id', auth()->id())->count();

        if ($limite !== null && $actuales >= (int) $limite) {
            return redirect()->route('tesis.index')
                ->with('error', "Alcanzaste el límite de {$limite} tesis de tu plan actual.");
        }

        return view('tesis.create');
    }

    public function store(Request $request)
    {
        $limite = config('zsystem.max_tesis');
        $actuales = Tesis::where('user_id', auth()->id())->count();

        if ($limite !== null && $actuales >= (int) $limite) {
            return redirect()->route('tesis.index')
                ->with('error', "Alcanzaste el límite de {$limite} tesis de tu plan actual.");
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

        // Estructura de capítulos por defecto, típica de una tesis en Perú
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
}
