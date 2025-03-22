<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrestataireRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $prestataire;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $prestataire)
    {
        $this->prestataire = $prestataire;
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
        $url = url('/admin/verifications');

        return (new MailMessage)
            ->subject('Nouvelle demande d\'inscription prestataire')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Un nouveau prestataire a demandé à rejoindre la plateforme TourismConnect.')
            ->line('Nom du prestataire: ' . $this->prestataire->name)
            ->line('Email: ' . $this->prestataire->email)
            ->line('Date d\'inscription: ' . $this->prestataire->created_at->format('d/m/Y H:i'))
            ->action('Examiner la demande', $url)
            ->line('Merci d\'utiliser TourismConnect!');
    }
}