<?php

namespace App\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class WelcomeNotification extends Notification
{
    use Queueable;

    /** @var User */
    protected User $user;

    /**
     * Create a new notification instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $url = 'https://suppli.cloud';

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60), // Expiry time for the verification link (e.g., 60 minutes)
            [
                'id' => $notifiable->id,
                'hash' => sha1($notifiable->getEmailForVerification()), // Generate a unique hash based on the email
            ]
        );

        return (new MailMessage)->withSymfonyMessage(function ($message) {
            $headers = [
                'X-Suppli-Redirect' => 'https://localhost:5173/',
            ];
            foreach ($headers as $name => $value) {
                $message->getHeaders()->addTextHeader($name, $value);
            }
        })
            ->subject('Welcome to Suppli.')
            ->line('Welcome to Suplli. We are excited to have you on board!')
            ->line('Thank you for sign up!')
            ->line('Verify Your email addess')
            ->line(
                'Hi,You\'re almost ready to get started. Please click on the button below to verify your email address and enjoy the service with us'
            )
            ->action('Verify Email', $verificationUrl)
            ->line('Regards,')
            ->line('Suppli Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
