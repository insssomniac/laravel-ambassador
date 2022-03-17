<?php

namespace App\Listeners;

use App\Events\ProductUpdateEvent;
use Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductUpdatedListener
{
    public function handle(ProductUpdateEvent $event)
    {
        Cache::forget('products_frontend');
        Cache::forget('products_backend');
    }
}
