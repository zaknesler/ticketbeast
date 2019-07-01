<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConcertOrderController extends Controller
{
    /**
     * Show all orders for a specified concert.
     *
     * @param  \App\Models\Concert  $concert
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Concert $concert, Request $request)
    {
        abort_unless($concert->user->is($request->user()), 404);
        abort_unless($concert->isPublished(), 404);

        return view('backstage.concerts.orders.show', [
            'concert' => $concert,
            'orders' => $concert->orders()->latest()->take(10)->get(),
        ]);
    }
}
