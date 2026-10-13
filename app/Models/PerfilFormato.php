<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Formato de página de una universidad: márgenes, interlineado y fuente.
 * Se usa con ISO 690 (que no define formato) o con APA cuando la universidad
 * cambia los márgenes o la fuente.
 */
class PerfilFormato extends Model
{
    protected $table = 'perfiles_formato';

    protected $fillable = [
        'nombre',
        'margen_superior',
        'margen_inferior',
        'margen_izquierdo',
        'margen_derecho',
        'interlineado',
        'fuentes',
        'tamano_fuente',
        'activo',
    ];

    protected $casts = [
        'margen_superior' => 'float',
        'margen_inferior' => 'float',
        'margen_izquierdo' => 'float',
        'margen_derecho' => 'float',
        'interlineado' => 'float',
        'tamano_fuente' => 'float',
        'activo' => 'boolean',
    ];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('nombre');
    }

    public function listaFuentes(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->fuentes))));
    }

    /** Reglas en el formato que entiende ApaStructureValidator. */
    public function reglas(): array
    {
        return [
            'codigo' => 'perfil-' . $this->id,
            'nombre' => 'el formato de ' . $this->nombre,
            'margenes' => [
                'superior' => $this->margen_superior,
                'inferior' => $this->margen_inferior,
                'izquierdo' => $this->margen_izquierdo,
                'derecho' => $this->margen_derecho,
            ],
            'margen_tolerancia_cm' => 0.15,
            'interlineado' => $this->interlineado,
            'interlineado_tolerancia' => 0.1,
            'fuentes_permitidas' => array_map(
                fn ($f) => ['nombre' => $f, 'tamano' => $this->tamano_fuente],
                $this->listaFuentes()
            ),
            'requiere_running_head' => false,
        ];
    }

    /** Texto corto para mostrar: "Sup 2.5 · Inf 2.5 · Izq 3 · Der 2.5 cm · 1.5 · Arial 12" */
    public function resumen(): string
    {
        $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');

        return "Márgenes: sup {$n($this->margen_superior)}, inf {$n($this->margen_inferior)}, izq {$n($this->margen_izquierdo)}, der {$n($this->margen_derecho)} cm"
            . " · Interlineado {$n($this->interlineado)}"
            . ' · ' . implode(' o ', $this->listaFuentes()) . ' ' . $n($this->tamano_fuente) . ' pt';
    }
}
