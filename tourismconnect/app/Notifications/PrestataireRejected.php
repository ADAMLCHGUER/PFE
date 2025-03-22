<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrestataireRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reason = '')
    {
        $this->reason = $reason;
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
        $mailMessage = (new MailMessage)
            ->subject('Votre demande d\'inscription prestataire a été rejetée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous sommes désolés de vous informer que votre demande d\'inscription en tant que prestataire sur TourismConnect a été rejetée.');
            
        if ($this->reason) {
            $mailMessage->line('Motif du rejet: ' . $this->reason);
        }
            
        return $mailMessage
            ->line('Si vous pensez qu\'il s\'agit d\'une erreur ou si vous souhaitez soumettre une nouvelle demande avec des informations mises à jour, n\'hésitez pas à nous contacter.')
            ->line('Merci de votre intérêt pour TourismConnect.');
    }
}