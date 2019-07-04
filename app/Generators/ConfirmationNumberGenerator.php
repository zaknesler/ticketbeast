<?php

namespace App\Generators;

interface ConfirmationNumberGenerator
{
    /**
     * Generate an order confirmation number.
     *
     * @return string
     */
    public function generate();
}
