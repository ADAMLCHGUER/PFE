<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrestataireApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/login');

        return (new MailMessage)
            ->subject('Votre compte prestataire a été approuvé')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Félicitations! Votre compte prestataire sur TourismConnect a été approuvé.')
            ->line('Vous pouvez maintenant vous connecter et commencer à gérer votre profil et vos offres.')
            ->action('Se connecter', $url)
            ->line('Merci de faire partie de TourismConnect!');
    }
}