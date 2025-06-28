<?php

namespace App\Providers;

use App\Observers\NotificationObserver;
use Illuminate\Notifications\Notification;
use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Observers\InventoryObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Product::observe(ProductObserver::class);
        Inventory::observe(InventoryObserver::class);
        User::observe(UserObserver::class);
      
    }
}
