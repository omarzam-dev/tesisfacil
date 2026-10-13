<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'price', 'duration_days', 'max_tesis', 'max_revisiones',
        'orden', 'destacado', 'activo',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'duration_days' => 'integer',
        'max_tesis'      => 'integer',
        'max_revisiones' => 'integer',
        'orden'         => 'integer',
        'destacado'     => 'boolean',
        'activo'        => 'boolean',
    ];

    // Periodos que se pueden elegir en el panel de administración
    const PERIODOS = [
        30  => 'Mensual (30 días)',
        180 => 'Semestral (180 días)',
        365 => 'Anual (365 días)',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /** Planes que se muestran al público, en el orden definido en el panel. */
    public function scopeVisibles(Builder $query): Builder
    {
        return $query->where('activo', true)->orderBy('orden')->orderBy('price');
    }

    /** Texto que va junto al precio: "/mes", "/6 meses" o "/año". */
    public function periodoTexto(): string
    {
        return match (true) {
            $this->duration_days >= 360 => '/año',
            $this->duration_days >= 170 => '/6 meses',
            $this->duration_days <= 31  => '/mes',
            default                     => '/' . $this->duration_days . ' días',
        };
    }

    /**
     * Cuánto se ahorra este plan frente a pagar su equivalente mensual.
     * Busca en $planes un plan mensual activo con los mismos límites.
     * Devuelve null si no es un plan largo o si no hay ahorro.
     */
    public function ahorro($planes): ?float
    {
        if ($this->duration_days <= 31) {
            return null;
        }

        $mensual = collect($planes)->first(fn ($p) =>
            $p->id !== $this->id
            && $p->activo
            && $p->duration_days <= 31
            && $p->max_tesis === $this->max_tesis
            && $p->max_revisiones === $this->max_revisiones
        );

        if (! $mensual) {
            return null;
        }

        $meses  = round($this->duration_days / 30);
        $ahorro = round((float) $mensual->price * $meses - (float) $this->price, 2);

        return $ahorro > 0 ? $ahorro : null;
    }

    /** Nota bajo el precio: el mensual paga cada mes, los demás son pago único. */
    public function notaPago(): string
    {
        return $this->duration_days <= 31
            ? 'Pago vía Yape o BCP'
            : 'Pago único vía Yape o BCP';
    }
}
