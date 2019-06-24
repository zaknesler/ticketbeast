<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\Stripe\StripePaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group hits-stripe
*/
class StripePaymentGatewayTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->lastCharge = $this->lastCharge();
    }

    /**
     * Get the last charge from Stripe.
     *
     * @return \Stripe\Charge
     */
    private function lastCharge()
    {
        return array_first(\Stripe\Charge::all(['limit' => 1])['data']);
    }

    /**
     * Fetch all charges that were created after another charge.
     *
     * @return array
     */
    private function newCharges()
    {
        return \Stripe\Charge::all([
            'ending_before' => $this->lastCharge ? $this->lastCharge->id : null,
        ])['data'];
    }

    /**
     * Get the implementation of the payment gateway.
     *
     * @return \App\Billing\PaymentGateway
     */
    public function getPaymentGateway()
    {
        return new StripePaymentGateway;
    }

    /** @test */
    function stripe_charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }

    /** @test */
    function stripe_charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new StripePaymentGateway;

            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail('Payment succeeded when using invalid payment token.');
    }
}
