<?php

namespace App\Generators;

use App\Models\Ticket;

interface TicketCodeGenerator
{
    /**
     * Generate a random ticket code for a ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return string
     */
    public function generateFor(Ticket $ticket);
}
