<?php

namespace App\Reservations;

use App\Models\Order;
use App\Billing\PaymentGateway;

class Reservation
{
    /**
     * The tickets that are reserved.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $tickets;

    /**
     * The email to whom the reservation belongs.
     *
     * @var string
     */
    protected $email;

    /**
     * Create a reservation for a number of tickets.
     *
     * @param \Illuminate\Support\Collection  $tickets
     * @param string  $email
     */
    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * Cancel the reservation by releasing of its tickets.
     *
     * @return void
     */
    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }

    /**
     * Complete a reservation and return the created order.
     *
     * @param  \App\Billing\PaymentGateway  $paymentGateway
     * @param  string  $paymentToken
     * @return \App\Models\Order
     */
    public function complete(PaymentGateway $paymentGateway, $paymentToken)
    {
        $charge = $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    /**
     * Get the total cost of all tickets in the reservation
     *
     * @return int
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    /**
     * Get the tickets of a reservation.
     *
     * @return \Illuminate\Support\Collection
     */
    public function tickets()
    {
        return $this->tickets;
    }

    /**
     * Get the email of a reservation.
     *
     * @return string
     */
    public function email()
    {
        return $this->email;
    }
}
