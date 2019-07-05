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
            $gateway->charge(2500, $gateway->getValidTestToken(), 'fake_acct_1234');
            $timesCallbackRan++;

            $this->assertEquals(2500, $gateway->totalCharges());
        });

        $gateway->charge(2500, $gateway->getValidTestToken(), 'fake_acct_1234');
        $this->assertEquals(1, $timesCallbackRan, 'Callback ran more than once.');
        $this->assertEquals(5000, $gateway->totalCharges());
    }

    /** @test */
    function can_get_total_charges_for_a_specific_account()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(1000, $paymentGateway->getValidTestToken(), 'fake_acct_0000');
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'fake_acct_1234');
        $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), 'fake_acct_1234');

        $this->assertEquals(6500, $paymentGateway->totalChargesFor('fake_acct_1234'));
    }
}
