<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class NewUserPendingApproval extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::signedRoute('admin.approve.user.email', ['user' => $this->user->id]);

        return (new MailMessage)
            ->subject('Nouvelle inscription à approuver')
            ->greeting('Bonjour Administrateur,')
            ->line('Un nouvel utilisateur demande un accès :')
            ->line('Nom : ' . $this->user->name)
            ->line('Email : ' . $this->user->email)
            ->action('Approuver ce compte', $url)
            ->line('Merci d\'agir dans les plus brefs délais.')
            ->line('Ce lien d\'approbation est sécurisé et ne peut être utilisé qu\'une seule fois.')
            ->salutation('Cordialement,');
    }

    /**
     * Get the array representation for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => 'Nouvelle demande d\'approbation',
            'link' => route('admin.approve.user', $this->user->id),
            'time' => now()->toDateTimeString()
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name
        ];
    }
}