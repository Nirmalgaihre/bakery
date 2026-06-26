<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class LowStockAlert extends Notification
{
    public $product;

    public function __construct($product) { $this->product = $product; }

    // Channel haru define garne (mail ra onesignal duitai)
    public function via($notifiable) 
    { 
        return ['mail', OneSignalChannel::class]; 
    }

    // Email ko lagi
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Urgent: Low Stock Alert')
            ->line('Product: ' . $this->product->name)
            ->line('Remaining Stock: ' . $this->product->initial_stock)
            ->action('View Inventory', url('/admin/products'));
    }

    // OneSignal Push Notification ko lagi
    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->subject("Low Stock Alert!")
            ->body("Product {$this->product->name} is running low (Qty: {$this->product->initial_stock}).")
            ->url(url('/admin/products'));
    }
}