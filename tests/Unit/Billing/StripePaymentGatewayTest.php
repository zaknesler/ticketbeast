<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\Stripe\StripePaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StripePaymentGatewayTest extends TestCase
{
    /**
     * Get the last charge from Stripe.
     *
     * @return \Stripe\Charge
     */
    private function lastCharge()
    {
        return \Stripe\Charge::all(['limit' => 1])['data'][0];
    }

    /**
     * Get a valid test token to use with Stripe.
     *
     * @return string
     */
    private function validToken()
    {
        return 'tok_visa';
    }

    /**
     * @test
     * @group hits-stripe
    */
    function stripe_charges_with_a_valid_payment_token_are_successful()
    {
        $lastCharge = $this->lastCharge();

        $paymentGateway = new StripePaymentGateway;
        $paymentGateway->charge(2500, $this->validToken());

        $newCharge = \Stripe\Charge::all([
            'limit' => 1,
            'ending_before' => $lastCharge->id
        ])['data'][0];

        $this->assertEquals(2500, $newCharge->amount);
        $this->assertEquals('succeeded', $newCharge->status);
    }
}
