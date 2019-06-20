<?php

namespace App\Reservations;

class Reservation
{
    /**
     * The tickets that are reserved.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $tickets;

    /**
     * Create a reservation for a number of tickets.
     *
     * @param \Illuminate\Support\Collection  $tickets
     */
    public function __construct($tickets)
    {
        $this->tickets = $tickets;
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
}
