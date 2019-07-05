<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\Stripe\StripePaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Billing\PaymentGatewayContractTests;

/**
 * @group hits-stripe
*/
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /**
     * Get the implementation of the payment gateway.
     *
     * @return \App\Billing\PaymentGateway
     */
    protected function getPaymentGateway()
    {
        return new StripePaymentGateway;
    }

    /** @test */
    function ninety_percent_of_the_payment_is_transferred_to_the_destination_account()
    {
        $paymentGateway = new StripePaymentGateway;

        $paymentGateway->charge(
            5000,
            $paymentGateway->getValidTestToken(),
            config('services.stripe.testing.destination_account_id')
        );

        $lastStripeCharge = array_first(\Stripe\Charge::all(['limit' => 1])['data']);

        $this->assertEquals(5000, $lastStripeCharge['amount']);
        $this->assertEquals(config('services.stripe.testing.destination_account_id'), $lastStripeCharge['destination']);

        $transfer = \Stripe\Transfer::retrieve($lastStripeCharge['transfer']);
        $this->assertEquals(4500, $transfer['amount']);
    }

    /** @test */
    function charge_amount_is_rounded_properly()
    {
        $paymentGateway = new StripePaymentGateway;

        $paymentGateway->charge(
            1234,
            $paymentGateway->getValidTestToken(),
            config('services.stripe.testing.destination_account_id')
        );

        $lastStripeCharge = array_first(\Stripe\Charge::all(['limit' => 1])['data']);

        $this->assertEquals(1234, $lastStripeCharge['amount']);
        $this->assertEquals(config('services.stripe.testing.destination_account_id'), $lastStripeCharge['destination']);

        $transfer = \Stripe\Transfer::retrieve($lastStripeCharge['transfer']);
        $this->assertEquals(1111, $transfer['amount']);
    }
}
