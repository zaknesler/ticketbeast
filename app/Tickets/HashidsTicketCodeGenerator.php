<?php

namespace App\Tickets;

use Hashids\Hashids;
use App\Models\Ticket;
use App\Tickets\TicketCodeGenerator;

class HashidsTicketCodeGenerator implements TicketCodeGenerator
{
    /**
     * The Hashids configuration.
     *
     * @var \Hashids\Hashids
     */
    protected $hashids;

    /**
     * Create a new instance of the hashids ticket code generator.
     *
     * @param  string  $salt
     */
    public function __construct($salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789');
    }

    /**
     * Generate a random ticket code for a ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return string
     */
    public function generateFor(Ticket $ticket)
    {
        return $this->hashids->encode($ticket->id);
    }
}
