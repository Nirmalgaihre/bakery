<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Product $product)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->greeting('Inventory Alert')
            ->line("Product \"{$this->product->name}\" is running low on stock.")
            ->line("Remaining stock: " . floatval($this->product->initial_stock) . ' ' . ($this->product->inventory_unit ?? 'KG'))
            ->line('Please consider restocking soon.');
    }
}