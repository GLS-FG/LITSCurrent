<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderUpdate extends Notification
{
    use Queueable;

    /**
     * The action url of the email.
     *
     * @var string
     */
    public $url;
    /**
     * The message that will be displayed in the email.
     *
     * @var string
     */
    public $updateMessage;

    /**
     * Create a notification instance.
     *
     * @param  string  $updateMessage
     * @param  string  $url
     */
    public function __construct($updateMessage, $url)
    {
        $this->updateMessage = $updateMessage;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualización de servicio en LITS')
            ->line($this->updateMessage)
            ->line('Puedes ver mas sobre este movimiento dando click en el siguiente boton.')
            ->action('Ver estatus', $this->url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
