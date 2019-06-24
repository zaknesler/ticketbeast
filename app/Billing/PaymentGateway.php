<?php

namespace App\Billing;

interface PaymentGateway
{
    /**
     * Get a valid token that can be used for testing.
     *
     * @return string
     */
    public function getValidTestToken();

    /**
     * Process a charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @return void
     */
    public function charge($amount, $token);
}
