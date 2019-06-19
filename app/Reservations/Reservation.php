<?php

namespace App\Reservations;

class Reservation
{
    /**
     * The tickets that are reserved.
     *
     * @var array
     */
    protected $tickets;

    /**
     * Create a reservation for a number of tickets.
     *
     * @param array  $tickets
     */
    public function __construct($tickets)
    {
        $this->tickets = $tickets;
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
}
