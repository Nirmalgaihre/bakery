<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockAlert extends Notification
{
    public $product;

    public function __construct($product) { $this->product = $product; }

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Urgent: Low Stock Alert')
            ->line('The following product has reached a critical stock level:')
            ->line('Product: ' . $this->product->name)
            ->line('Remaining Stock: ' . $this->product->initial_stock)
            ->action('View Inventory', url('/admin/products'))
            ->line('Please replenish the stock soon.');
    }
}