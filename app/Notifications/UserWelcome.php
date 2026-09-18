<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcome extends Notification
{
    use Queueable;

    /**
     * The action url of the email.
     *
     * @var string
     */
    public $url;

    /**
     * Create a notification instance.
     *
     * @param  string  $updateMessage
     * @param  string  $url
     */
    public function __construct($url)
    {
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
            ->subject('Bienvenido a GLSGROUP')
            ->greeting('Estimado Cliente,')
            ->line('Nos complace darle una gran bienvenida a **GLS FORWARDING GROUP**, agradecemos su confianza para ser su socio comercial en su cadena de suministro en el área de logística y comercio exterior; nos es muy grato servirle a través de nuestro valioso equipo de profesionales, buscando siempre brindarles un servicio de calidad y de excelencia.')
            ->line('Nuestro compromiso es cumplir sus metas a través nuestros servicios para poder fortalecer nuestra alianza comercial.')
            ->line('Gracias por permitirnos crecer juntos')
            ->action('Configurar Contraseña', $this->url);
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
