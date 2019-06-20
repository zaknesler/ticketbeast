<?php

namespace App\Billing\Stripe;

use App\Billing\PaymentGateway;

class StripePaymentGateway implements PaymentGateway
{
    /**
     * Process a charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @return void
     */
    public function charge($amount, $token)
    {
        $charge = \Stripe\Charge::create([
            'currency' => 'usd',
            'amount' => $amount,
            'source' => $token,
        ]);
    }
}
