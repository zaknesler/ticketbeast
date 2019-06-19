<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Reservations\Reservation;
use App\Http\Requests\Concerts\Orders\StoreOrder;
use App\Billing\Exceptions\PaymentFailedException;
use App\Billing\Exceptions\NotEnoughTicketsException;

class ConcertOrderController extends Controller
{
    /**
     * The payment gateway.
     *
     * @var App\Billing\PaymentGateway
     */
    protected $paymentGateway;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * Store a concert order in the database.
     *
     * @param  App\Http\Requests\Concerts\Orders\StoreOrder $request
     * @param  App\Models\Concert  $concert
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrder $request, Concert $concert)
    {
        if (is_null($concert->published_at)) {
            return abort(404);
        }

        try {
            $reservation = new Reservation($tickets = $concert->reserveTickets(request('ticket_quantity')));

            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            $order = Order::forTickets($tickets, request('email'), $reservation->totalCost());

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
