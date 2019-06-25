<?php

namespace App\Billing\Stripe;

use App\Billing\Charge;
use App\Billing\PaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4000056655665556';

    /**
     * Get a valid token that can be used for testing.
     *
     * @param  string|null  $cardNumber
     * @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        switch ($cardNumber) {
            case '4242424242424242':
                return 'tok_visa';
            case '4000056655665556':
                return 'tok_visa_debit';
            default:
                return null;
        }
    }

    /**
     * Get the charges made during a callback.
     *
     * @param  callback  $callback
     * @return array
     */
    public function newChargesDuring($callback)
    {
        $lastCharge = array_first(\Stripe\Charge::all(['limit' => 1])['data']);

        $callback($this);

        return collect(
            \Stripe\Charge::all(['ending_before' => $lastCharge])['data']
        )->map(function ($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge->amount,
                'card_last_four' => $stripeCharge->source->last4,
            ]);
        });
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
            $charge = \Stripe\Charge::create([
                'currency' => 'usd',
                'amount' => $amount,
                'source' => $token,
            ]);

            return new Charge([
                'amount' => $charge->amount,
                'card_last_four' => $charge->source->last4,
            ]);
        } catch (\Stripe\Error\InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }
}
