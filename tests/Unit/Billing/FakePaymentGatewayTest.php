<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Unit\Billing\PaymentGatewayContractTests;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /**
     * Get the implementation of the payment gateway.
     *
     * @return \App\Billing\PaymentGateway
     */
    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
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
