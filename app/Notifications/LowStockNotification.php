<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $inventory;

    public function __construct(Product $product, Inventory $inventory)
    {
        $this->product = $product;
        $this->inventory = $inventory;
    }

    public function via($notifiable)
    {
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("⚠️ Low Stock Alert: {$this->product->name}")
            ->line("Product SKU: {$this->product->sku}")
            ->line("Warehouse: {$this->inventory->warehouse->name}")
            ->line("Available stock is now at or below the reorder level.")
            ->line("Stock: {$this->inventory->stock_quantity}")
            ->line("Reserved: {$this->inventory->reserved_quantity}")
            ->line("Reorder Level: {$this->product->reorder_level}")
            ->line("Please restock soon.");
    }
}

