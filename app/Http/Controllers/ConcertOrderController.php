<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
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
            $tickets = $concert->reserveTickets(request('ticket_quantity'));

            $this->paymentGateway->charge(
                request('ticket_quantity') * $concert->ticket_price,
                request('payment_token')
            );

            $order = $concert->createOrder(request('email'), $tickets);

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
