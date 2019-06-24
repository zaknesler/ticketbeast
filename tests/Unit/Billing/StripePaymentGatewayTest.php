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
}
