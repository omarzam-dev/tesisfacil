<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VencimientoProximo extends Notification
{
    use Queueable;

    public function __construct(
        protected string $tipo,      // 'trial' o 'plan'
        protected \Carbon\Carbon $vence
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $diasRestantes = now()->diffInDays($this->vence, false);
        $motivo = $this->tipo === 'trial' ? 'tu prueba gratuita' : 'tu plan actual';

        return (new MailMessage)
            ->subject('Tu acceso a TesisFácil vence pronto')
            ->greeting("Hola {$notifiable->name},")
            ->line("Te escribimos para avisarte que {$motivo} vence el {$this->vence->format('d/m/Y')} (en {$diasRestantes} días).")
            ->line('Si quieres seguir usando el corrector APA y el gestor de tesis sin interrupciones, elige un plan antes de esa fecha.')
            ->action('Ver planes', route('subscription.plans'))
            ->line('Si ya renovaste, puedes ignorar este mensaje.');
    }
}
