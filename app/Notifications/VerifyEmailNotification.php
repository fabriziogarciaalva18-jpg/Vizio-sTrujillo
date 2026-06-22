<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('¡Verifica tu correo en Vizio\'s!')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Gracias por registrarte en Vizio\'s Pastelería.')
            ->line('Para activar tu cuenta y comenzar a disfrutar de nuestros dulces, solo tienes que hacer clic en el botón de abajo:')
            ->action('Verificar Correo', $verificationUrl)
            ->line('Si no creaste esta cuenta, ignora este mensaje.')
            ->salutation('Saludos, el equipo de Vizio\'s');
    }
}
