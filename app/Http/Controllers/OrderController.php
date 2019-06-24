<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display the order confirmation page.
     *
     * @param  string  $confirmationNumber
     * @return \Illuminate\Http\Response
     */
    public function show($confirmationNumber)
    {
        $order = Order::findByConfirmationNumber($confirmationNumber)
            ->load(['tickets.concert']);

        return view('orders.show')
            ->with('order', $order);
    }
}
