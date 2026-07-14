<?php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use Illuminate\Http\Request;

class CapituloVersionController extends Controller
{
    public function store(Request $request, Capitulo $capitulo)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);

        $request->validate([
            'documento' => ['required', 'file', 'mimes:docx,pdf', 'max:10240'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ]);

        $archivo = $request->file('documento');
        $ruta = $archivo->store('capitulos/' . $capitulo->id, 'local');

        $numeroVersion = ($capitulo->versiones()->max('numero_version') ?? 0) + 1;

        $capitulo->versiones()->create([
            'numero_version' => $numeroVersion,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'ruta_archivo' => $ruta,
            'comentario' => $request->comentario,
        ]);

        // Al subir una nueva versión, el capítulo vuelve a estado "en_revision"
        // (ya no está pendiente, pero tampoco aprobado hasta que se confirme)
        if ($capitulo->estado === 'pendiente') {
            $capitulo->update(['estado' => 'en_progreso']);
        }

        return back()->with('status', 'Nueva versión subida correctamente.');
    }

    public function download(Capitulo $capitulo, \App\Models\CapituloVersion $version)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);
        abort_unless($version->capitulo_id === $capitulo->id, 404);

        return \Storage::disk('local')->download($version->ruta_archivo, $version->nombre_archivo);
    }
}
