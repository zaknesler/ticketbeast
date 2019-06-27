<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Reservations\Reservation;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Support\Facades\Mail;
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
            $reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));
            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            Mail::to($order->email)->send(new OrderConfirmationEmail($order));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();

            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
