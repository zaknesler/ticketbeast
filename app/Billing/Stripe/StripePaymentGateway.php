<?php

namespace App\Billing\Stripe;

use Stripe\Charge;
use App\Billing\PaymentGateway;
use Stripe\Error\InvalidRequest;
use App\Billing\Exceptions\PaymentFailedException;

class StripePaymentGateway implements PaymentGateway
{
    /**
     * Get a valid token that can be used for testing.
     *
     * @return string
     */
    public function getValidTestToken()
    {
        return 'tok_visa';
    }

    /**
     * Process a charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @return void
     */
    public function charge($amount, $token)
    {
        try {
            $charge = Charge::create([
                'currency' => 'usd',
                'amount' => $amount,
                'source' => $token,
            ]);
        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }
}
