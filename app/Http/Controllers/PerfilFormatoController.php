<?php

namespace App\Http\Controllers;

use App\Models\PerfilFormato;
use Illuminate\Http\Request;

/** Admin: formatos de página por universidad (márgenes, interlineado, fuente). */
class PerfilFormatoController extends Controller
{
    private function checkAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'No tienes permiso para acceder aquí.');
    }

    public function index()
    {
        $this->checkAdmin();

        $perfiles = PerfilFormato::orderByDesc('activo')->orderBy('nombre')->get();

        return view('admin.perfiles', compact('perfiles'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        PerfilFormato::create($this->validar($request));

        return back()->with('success', 'Perfil de formato creado.');
    }

    public function update(Request $request, PerfilFormato $perfil)
    {
        $this->checkAdmin();

        $perfil->update($this->validar($request));

        return back()->with('success', "Perfil \"{$perfil->nombre}\" actualizado.");
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'margen_superior' => ['required', 'numeric', 'between:0.5,6'],
            'margen_inferior' => ['required', 'numeric', 'between:0.5,6'],
            'margen_izquierdo' => ['required', 'numeric', 'between:0.5,6'],
            'margen_derecho' => ['required', 'numeric', 'between:0.5,6'],
            'interlineado' => ['required', 'numeric', 'between:1,3'],
            'fuentes' => ['required', 'string', 'max:255'],
            'tamano_fuente' => ['required', 'numeric', 'between:8,16'],
        ], [], [
            'margen_superior' => 'margen superior',
            'margen_inferior' => 'margen inferior',
            'margen_izquierdo' => 'margen izquierdo',
            'margen_derecho' => 'margen derecho',
            'tamano_fuente' => 'tamaño de fuente',
        ]);

        // "Arial,Times New Roman" => "Arial, Times New Roman"
        $datos['fuentes'] = implode(', ', array_filter(array_map('trim', explode(',', $datos['fuentes']))));
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
