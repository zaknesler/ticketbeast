<?php

namespace App\Billing;

use App\Billing\PaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    /**
     * A collection of all charges.
     *
     * @var array
     */
    private $charges;

    /**
     * Callback to run before the first charge.
     *
     * @var function
     */
    private $beforeFirstChargeCallback;

    /**
     * Create a new instance of the fake payment gateway.
     */
    public function __construct()
    {
        $this->charges = collect();
    }

    /**
     * Get a valid token that can be used for testing.
     *
     * @return string
     */
    public function getValidTestToken()
    {
        return 'valid-token';
    }

    /**
     * Process a fake charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @return void
     */
    public function charge($amount, $token)
    {
        if (!is_null($this->beforeFirstChargeCallback)) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }

    /**
     * Get the total of all charges.
     *
     * @return int
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }
}
