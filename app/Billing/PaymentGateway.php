<?php

namespace App\Billing;

interface PaymentGateway
{
    /**
     * Get a valid token that can be used for testing.
     *
     * @param  string|null  $cardNumber
     * @return string
     */
    public function getValidTestToken($cardNumber = null);

    /**
     * Get the charges made during a callback.
     *
     * @param  callback  $callback
     * @return array
     */
    public function newChargesDuring($callback);

    /**
     * Process a charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @return void
     */
    public function charge($amount, $token);
}
