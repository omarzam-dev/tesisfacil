<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BienvenidaTesisFacil extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Bienvenido a TesisFácil!')
            ->greeting("Hola {$notifiable->name},")
            ->line('Tu cuenta ya está lista. Tienes 14 días de prueba gratuita para revisar tu tesis en formato APA y organizar tus capítulos.')
            ->line('En tu plan de prueba puedes crear 1 tesis y hacer hasta 10 revisiones APA por mes.')
            ->action('Empezar ahora', route('dashboard'))
            ->line('Cualquier duda, responde este correo.');
    }
}
