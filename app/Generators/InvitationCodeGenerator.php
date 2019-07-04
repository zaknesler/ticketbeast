<?php

namespace App\Generators;

use App\Models\Ticket;

interface InvitationCodeGenerator
{
    /**
     * Generate an invitation code.
     *
     * @return string
     */
    public function generate();
}
