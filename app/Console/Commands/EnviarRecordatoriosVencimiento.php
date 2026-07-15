<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\VencimientoProximo;
use Illuminate\Console\Command;

class EnviarRecordatoriosVencimiento extends Command
{
    protected $signature = 'tesisfacil:recordatorios-vencimiento';

    protected $description = 'Envía un recordatorio por correo a usuarios cuyo trial o plan vence en 3 días';

    protected int $diasAntes = 3;

    public function handle(): int
    {
        $fechaLimite = now()->addDays($this->diasAntes)->endOfDay();

        // Usuarios en trial que vencen dentro de los próximos 3 días
        $enTrial = User::where('subscription_status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), $fechaLimite])
            ->where(function ($q) {
                $q->whereNull('recordatorio_enviado_at')
                    ->orWhere('recordatorio_enviado_at', '<', now()->subDays(7));
            })
            ->get();

        foreach ($enTrial as $user) {
            $user->notify(new VencimientoProximo('trial', $user->trial_ends_at));
            $user->update(['recordatorio_enviado_at' => now()]);
            $this->info("Recordatorio de trial enviado a {$user->email}");
        }

        // Usuarios con plan activo que vence dentro de los próximos 3 días
        $conPlan = User::where('subscription_status', 'active')
            ->whereHas('subscriptions', function ($q) use ($fechaLimite) {
                $q->where('status', 'active')
                    ->whereBetween('ends_at', [now(), $fechaLimite]);
            })
            ->where(function ($q) {
                $q->whereNull('recordatorio_enviado_at')
                    ->orWhere('recordatorio_enviado_at', '<', now()->subDays(7));
            })
            ->get();

        foreach ($conPlan as $user) {
            $vence = $user->activeSubscription->ends_at;
            $user->notify(new VencimientoProximo('plan', $vence));
            $user->update(['recordatorio_enviado_at' => now()]);
            $this->info("Recordatorio de plan enviado a {$user->email}");
        }

        $this->info('Listo. Total enviados: ' . ($enTrial->count() + $conPlan->count()));

        return self::SUCCESS;
    }
}
