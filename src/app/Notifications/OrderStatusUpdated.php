<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public TravelOrder $travel_order;

    public function __construct(TravelOrder $travel_order)
    {
        $this->travel_order = $travel_order;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Status do Pedido Atualizado")
            ->line("Seu pedido de viagem para {$this->travel_order->destination} foi {$this->travel_order->status}.")
            ->action('Ver Detalhes', url('/travel-orders/' . $this->travel_order->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->travel_order->id,
            'status' => $this->travel_order->status,
            'message' => "Pedido #{$this->travel_order->id} atualizado para {$this->travel_order->status}"
        ];
    }
}
