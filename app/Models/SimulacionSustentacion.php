<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulacionSustentacion extends Model
{
    protected $table = 'simulaciones_sustentacion';

    protected $fillable = [
        'user_id', 'titulo', 'carrera', 'tono',
        'planteamiento', 'metodologia', 'resultados', 'conclusiones',
        'preguntas', 'respuestas', 'estado', 'nota_final',
    ];

    protected $casts = [
        'preguntas'  => 'array',
        'respuestas' => 'array',
        'nota_final' => 'float',
    ];

    const TONOS = [
        'comprensivo' => 'Comprensivo',
        'exigente'    => 'Exigente',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function totalPreguntas(): int
    {
        return count($this->preguntas ?? []);
    }

    public function respondidas(): int
    {
        return count($this->respuestas ?? []);
    }

    public function finalizada(): bool
    {
        return $this->estado === 'finalizada';
    }

    /** Índice (desde 0) de la pregunta que toca responder, o null si ya terminó. */
    public function indiceActual(): ?int
    {
        return $this->respondidas() < $this->totalPreguntas() ? $this->respondidas() : null;
    }

    /** Texto con el resumen de la tesis que se le da a la IA como contexto. */
    public function contexto(): string
    {
        return implode("\n\n", array_filter([
            'Título: ' . $this->titulo,
            $this->carrera ? 'Carrera: ' . $this->carrera : null,
            "PLANTEAMIENTO (problema, objetivos, hipótesis):\n" . $this->planteamiento,
            "METODOLOGÍA:\n" . $this->metodologia,
            "RESULTADOS PRINCIPALES:\n" . $this->resultados,
            $this->conclusiones ? "CONCLUSIONES:\n" . $this->conclusiones : null,
        ]));
    }

    /** Calificación cualitativa en escala vigesimal peruana. */
    public static function calificacion(?float $nota): string
    {
        return match (true) {
            $nota === null => '—',
            $nota >= 18    => 'Excelente',
            $nota >= 15    => 'Muy bueno',
            $nota >= 13    => 'Aprobado',
            $nota >= 11    => 'Aprobado con observaciones',
            default        => 'Necesita prepararse más',
        };
    }

    public static function colorNota(?float $nota): string
    {
        return match (true) {
            $nota === null => 'text-gray-400',
            $nota >= 15    => 'text-verde',
            $nota >= 11    => 'text-naranja',
            default        => 'text-rosa',
        };
    }
}
