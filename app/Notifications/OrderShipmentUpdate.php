<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OrderShipmentUpdate extends Notification
{
    use Queueable;

    public $reference;
    public $status;
    public $updateDate;
    public $url;
    public $updateMessage;
    public $updateComment;
    public $updateETA;
    public $locationName;
    public function __construct($reference, $status, $updateDate, $updateMessage, $updateComment, $updateETA, $locationName, $url)
    {
        $this->reference = $reference;
        $this->status = $status;
        $this->updateDate = $updateDate;
        $this->updateMessage = $updateMessage;
        $this->updateComment = $updateComment;
        $this->updateETA = $updateETA;
        $this->locationName = $locationName;
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
            ->subject('Actualización de embarque GLS Group')
            ->greeting("Hola, {$notifiable->name}!")
            ->line($this->updateMessage)
            ->line(new HtmlString('<p><strong>' . $this->status . '</strong></p>'))
            ->line(new HtmlString('<p><strong>' . $this->locationName . '</strong></p>'))
            ->line(new HtmlString('<p><strong>' . $this->updateDate . '</strong></p>'))
            ->line($this->updateComment)
            ->action('Ver embarque', $this->url)
            ->line(new HtmlString('<p><strong>REFERENCIA DEL CLIENTE: <span style="color: #E61D15;">' . $this->reference . '</span></strong></p>'))
            ->line(new HtmlString('<p><strong>' . $this->updateETA . '</strong></p>'))
            ->salutation('Saludos cordiales,');
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
