<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferExpirationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $offer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
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
        $url = url('/prestataire/offers/' . $this->offer->id . '/edit');

        return (new MailMessage)
            ->subject('Votre offre arrive bientôt à expiration')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre offre « ' . $this->offer->title . ' » pour le service « ' . $this->offer->service->name . ' » arrive bientôt à expiration.')
            ->line('Date d\'expiration: ' . $this->offer->end_date->format('d/m/Y'))
            ->line('Si vous souhaitez prolonger cette offre, veuillez la mettre à jour avant son expiration.')
            ->action('Modifier l\'offre', $url)
            ->line('Merci d\'utiliser TourismConnect!');
    }
}