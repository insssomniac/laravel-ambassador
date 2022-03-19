<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAmbassadorListener
{
    public function handle($event)
    {
        $order = $event->order;

        \Mail::send('ambassador-notify', ['order' => $order], function (Message $message) use ($order) {
            $message->subject('An order has been completed');
            $message->to($order->ambassador_email);
        });
    }
}
