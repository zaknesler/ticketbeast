<?php

namespace App\Orders;

interface ConfirmationNumberGenerator
{
    /**
     * Generate an order confirmation number.
     *
     * @return string
     */
    public function generate();
}
