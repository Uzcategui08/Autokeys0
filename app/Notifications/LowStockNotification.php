<?php

namespace App\Notifications;

use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public $producto;
    public $cantidad;

    /**
     * Create a new notification instance.
     */
    public function __construct(Producto $producto, $cantidad)
    {
        $this->producto = $producto;
        $this->cantidad = $cantidad;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'El producto ' . $this->producto->item . ' tiene un stock bajo ('.$this->cantidad.' unidades).',
            'producto_id' => $this->producto->id_producto,
            'cantidad' => $this->cantidad,
            'url' => '/productos/' . $this->producto->id_producto,
        ];
    }
}
