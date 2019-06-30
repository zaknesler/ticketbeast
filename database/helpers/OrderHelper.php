<?php

namespace App\Database\Helpers;

use App\Models\Order;
use App\Models\Ticket;

class OrderHelper
{
    /**
     * Create an order that is attached to a concert and has tickets.
     *
     * @param  \App\Models\Concert  $concert
     * @param  array|null  $overrides
     * @param  integer|null  $ticketQuantity
     * @return \App\Models\Order
     */
    public static function createForConcert($concert, $overrides = [], $ticketQuantity = 1)
    {
        $order = factory(Order::class)->create($overrides);
        $tickets = factory(Ticket::class, $ticketQuantity)->create(['concert_id' => $concert->id]);
        $order->tickets()->saveMany($tickets);

        return $order;
    }
}
