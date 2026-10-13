<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'trial_ends_at', 'subscription_status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at'     => 'datetime',
        'password'          => 'hashed',
    ];

    // Límites del plan de prueba gratuita (no vive en la tabla plans,
    // es el mismo criterio que ya usaba la landing antes de tener BD real)
    const TRIAL_MAX_TESIS = 1;
    const TRIAL_MAX_REVISIONES = 10;

    public function onTrial(): bool
    {
        return $this->subscription_status === 'trial'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    public function hasAccess(): bool
    {
        return $this->isAdmin() || $this->onTrial() || $this->subscription_status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    public function paymentProofs()
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function tesis()
    {
        return $this->hasMany(Tesis::class);
    }

    // -----------------------------------------------------------------
    // Límites según el plan activo (o el trial, si aún no pagó nada)
    // -----------------------------------------------------------------

    public function limiteTesis(): int
    {
        if ($plan = $this->activeSubscription?->plan) {
            return $plan->max_tesis;
        }

        return $this->onTrial() ? self::TRIAL_MAX_TESIS : 0;
    }

    public function limiteRevisiones(): int
    {
        if ($plan = $this->activeSubscription?->plan) {
            return $plan->max_revisiones;
        }

        return $this->onTrial() ? self::TRIAL_MAX_REVISIONES : 0;
    }

    public function tesisUsadas(): int
    {
        return $this->tesis()->count();
    }

    public function revisionesUsadasEsteMes(): int
    {
        return \App\Models\RevisionApa::where('user_id', $this->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function puedeCrearTesis(): bool
    {
        return $this->tesisUsadas() < $this->limiteTesis();
    }

    public function puedeRevisar(): bool
    {
        return $this->revisionesUsadasEsteMes() < $this->limiteRevisiones();
    }

    // -----------------------------------------------------------------
    // Asistente de IA: usos incluidos por mes según el plan
    // -----------------------------------------------------------------

    public function usosIa()
    {
        return $this->hasMany(UsoIa::class);
    }

    public function limiteIa(): int
    {
        // Los administradores no tienen límite (para probar y dar soporte)
        if ($this->isAdmin()) {
            return 9999;
        }

        if ($plan = $this->activeSubscription?->plan) {
            return (int) $plan->max_ia;
        }

        return $this->onTrial() ? (int) config('ia.trial_usos', 3) : 0;
    }

    public function iaUsadaEsteMes(): int
    {
        return $this->usosIa()->esteMes()->count();
    }

    public function puedeUsarIa(): bool
    {
        return $this->iaUsadaEsteMes() < $this->limiteIa();
    }
}
