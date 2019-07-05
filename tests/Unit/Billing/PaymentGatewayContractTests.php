<?php

namespace Tests\Unit\Billing;

use App\Billing\Exceptions\PaymentFailedException;

trait PaymentGatewayContractTests
{
    /**
     * Get the implementation of the payment gateway.
     *
     * @return \App\Billing\PaymentGateway
     */
    abstract protected function getPaymentGateway();

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), config('services.stripe.testing.destination_account_id'));
        });

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    /** @test */
    function can_get_details_about_a_successful_charge()
    {
        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(2500, $paymentGateway->getValidTestToken($paymentGateway::TEST_CARD_NUMBER), config('services.stripe.testing.destination_account_id'));

        $this->assertEquals(substr($paymentGateway::TEST_CARD_NUMBER, -4), $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
        $this->assertEquals(config('services.stripe.testing.destination_account_id'), $charge->destination());
    }

    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken(), config('services.stripe.testing.destination_account_id'));
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken(), config('services.stripe.testing.destination_account_id'));

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), config('services.stripe.testing.destination_account_id'));
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken(), config('services.stripe.testing.destination_account_id'));
        });

        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
    }

    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();

        $this->expectException(PaymentFailedException::class);

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, 'invalid-payment-token', config('services.stripe.testing.destination_account_id'));
        });

        $this->assertCount(0, $newCharges);
    }
}
