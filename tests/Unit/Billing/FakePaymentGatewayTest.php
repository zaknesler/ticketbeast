<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGatewayTest extends TestCase
{
    /**
     * Get the implementation of the payment gateway.
     *
     * @return \App\Billing\PaymentGateway
     */
    public function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test */
    function fake_charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = $this->getPaymentGateway();

            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertEquals(0, $paymentGateway->totalCharges());
            return;
        }

        $this->fail('Payment succeeded when using invalid payment token.');
    }

    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $gateway = $this->getPaymentGateway();
        $timesCallbackRan = 0;

        $gateway->beforeFirstCharge(function ($gateway) use (&$timesCallbackRan) {
            $gateway->charge(2500, $gateway->getValidTestToken());
            $timesCallbackRan++;

            $this->assertEquals(2500, $gateway->totalCharges());
        });

        $gateway->charge(2500, $gateway->getValidTestToken());
        $this->assertEquals(1, $timesCallbackRan, 'Callback ran more than once.');
        $this->assertEquals(5000, $gateway->totalCharges());
    }
}
