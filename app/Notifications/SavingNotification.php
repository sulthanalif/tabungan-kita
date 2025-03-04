<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class SavingNotification extends Notification
{
    use Queueable;

    public $saving;
    public $saldo;

    /**
     * Create a new notification instance.
     */
    public function __construct($saving, $saldo)
    {
        $this->saving = $saving;
        $this->saldo = $saldo;
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
                    ->subject('Notif Tabungan')
                    ->greeting('Halo, ' . $notifiable->name)
                    ->line('Kami ingin memberitahukan bahwa '. $this->saving['category']['name'] . ' sebesar Rp.' . number_format($this->saving['amount'], 0, ',', '.') . ' oleh ' . $this->saving['user']['name'] . ' telah disimpan.')
                    ->line('Saldo saat ini: Rp.' . number_format($this->saldo, 0, ',', '.'))
                    ->action('Lihat Tabungan', url(Route('saving')))
                    ->line('Terima kasih,')
                    ->salutation('Tabungan Kita');
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
