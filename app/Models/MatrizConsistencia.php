<?php

namespace App\Models;

use App\Services\RevisorMatriz;
use Illuminate\Database\Eloquent\Model;

class MatrizConsistencia extends Model
{
    protected $table = 'matrices_consistencia';

    protected $fillable = [
        'user_id', 'titulo', 'enfoque',
        'problema_general', 'problemas_especificos',
        'objetivo_general', 'objetivos_especificos',
        'hipotesis_general', 'hipotesis_especificas',
        'variables', 'puntaje', 'veredicto', 'resultado',
    ];

    protected $casts = [
        'resultado' => 'array',
        'puntaje'   => 'integer',
    ];

    const ENFOQUES = [
        'cuantitativo' => 'Cuantitativo',
        'cualitativo'  => 'Cualitativo',
        'mixto'        => 'Mixto',
    ];

    const VEREDICTOS = [
        'consistente'      => ['texto' => 'Consistente',      'badge' => 'badge-verde'],
        'requiere_ajustes' => ['texto' => 'Requiere ajustes', 'badge' => 'badge-naranja'],
        'inconsistente'    => ['texto' => 'Inconsistente',    'badge' => 'badge-rosa'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function veredictoTexto(): string
    {
        return self::VEREDICTOS[$this->veredicto]['texto'] ?? 'Sin veredicto';
    }

    public function veredictoBadge(): string
    {
        return self::VEREDICTOS[$this->veredicto]['badge'] ?? 'badge-teal';
    }

    public function listaProblemas(): array
    {
        return RevisorMatriz::lineas($this->problemas_especificos);
    }

    public function listaObjetivos(): array
    {
        return RevisorMatriz::lineas($this->objetivos_especificos);
    }

    public function listaHipotesis(): array
    {
        return RevisorMatriz::lineas($this->hipotesis_especificas);
    }
}
