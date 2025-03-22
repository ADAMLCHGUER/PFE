<?php

namespace App\Notifications;

use App\Models\Service;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $service;
    protected $review;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Service $service, Review $review)
    {
        $this->service = $service;
        $this->review = $review;
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
        $url = url('/prestataire/stats');
        $starRating = str_repeat('★', $this->review->rating) . str_repeat('☆', 5 - $this->review->rating);

        return (new MailMessage)
            ->subject('Nouvel avis reçu pour votre service')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Vous avez reçu un nouvel avis pour votre service « ' . $this->service->name . ' ».')
            ->line('Note: ' . $starRating . ' (' . $this->review->rating . '/5)')
            ->line('Commentaire: ' . $this->review->comment)
            ->line('De: ' . $this->review->user->name)
            ->line('Date: ' . $this->review->created_at->format('d/m/Y H:i'))
            ->action('Voir tous vos avis', $url)
            ->line('Merci d\'utiliser TourismConnect!');
    }
}