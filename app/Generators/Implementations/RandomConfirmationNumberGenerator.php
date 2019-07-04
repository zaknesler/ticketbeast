<?php

namespace App\Generators\Implementations;

use App\Generators\ConfirmationNumberGenerator;

class RandomConfirmationNumberGenerator implements ConfirmationNumberGenerator
{
    /**
     * Generate an order confirmation number.
     *
     * @return string
     */
    public function generate()
    {
        $possible = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        return substr(str_shuffle(str_repeat($possible, 24)), 0, 24);
    }
}
