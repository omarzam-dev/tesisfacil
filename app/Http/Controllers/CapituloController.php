<?php

namespace App\Http\Controllers;

use App\Models\Capitulo;
use App\Models\Tesis;
use Illuminate\Http\Request;

class CapituloController extends Controller
{
    public function store(Request $request, Tesis $tesis)
    {
        abort_unless($tesis->user_id === auth()->id(), 403);

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $orden = $tesis->capitulos()->max('orden') + 1;

        $tesis->capitulos()->create([
            'nombre' => $data['nombre'],
            'orden' => $orden,
            'estado' => 'pendiente',
        ]);

        return back();
    }

    public function actualizarEstado(Request $request, Capitulo $capitulo)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);

        $data = $request->validate([
            'estado' => ['required', 'in:pendiente,en_progreso,en_revision,aprobado'],
        ]);

        $capitulo->update(['estado' => $data['estado']]);

        return back();
    }

    public function destroy(Capitulo $capitulo)
    {
        abort_unless($capitulo->tesis->user_id === auth()->id(), 403);

        $capitulo->delete();

        return back();
    }
}
