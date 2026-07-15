<?php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use App\Models\CapituloVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CapituloVersionController extends Controller
{
    public function store(Request $request, Capitulo $capitulo)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);

        $request->validate([
            // Restringido a .docx: necesario para poder combinar los capítulos
            // en un solo documento desde "Descargar tesis completa".
            'documento' => ['required', 'file', 'mimes:docx', 'max:10240'],
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

        if ($capitulo->estado === 'pendiente') {
            $capitulo->update(['estado' => 'en_progreso']);
        }

        return back()->with('status', 'Nueva versión subida correctamente.');
    }

    public function download(Capitulo $capitulo, CapituloVersion $version)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);
        abort_unless($version->capitulo_id === $capitulo->id, 404);

        return Storage::disk('local')->download($version->ruta_archivo, $version->nombre_archivo);
    }

    public function destroy(Capitulo $capitulo, CapituloVersion $version)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);
        abort_unless($version->capitulo_id === $capitulo->id, 404);

        if ($capitulo->versiones()->count() <= 1) {
            return back()->with('error', 'No puedes eliminar la única versión de un capítulo. Sube una nueva primero, o elimina el capítulo completo.');
        }

        Storage::disk('local')->delete($version->ruta_archivo);
        $version->delete();

        return back()->with('status', 'Versión eliminada. La versión más reciente restante queda como la actual.');
    }
}
