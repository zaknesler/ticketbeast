<?php

namespace App\Billing;

use App\Billing\Charge;
use App\Billing\PaymentGateway;
use App\Billing\Exceptions\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    /**
     * A collection of all charges.
     *
     * @var array
     */
    private $charges;

    /**
     * A collection of generated tokens.
     *
     * @var array
     */
    private $tokens;

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
        $this->tokens = collect();
    }

    /**
     * Get a valid token that can be used for testing.
     *
     * @param  string|null  $cardNumber
     * @return string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'faketok_' . str_random(24);

        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    /**
     * Get the charges made during a callback.
     *
     * @param  callback  $callback
     * @return array
     */
    public function newChargesDuring($callback)
    {
        $oldCharges = $this->charges->count();

        $callback($this);

        return $this->charges->slice($oldCharges)->reverse()->values();
    }

    /**
     * Process a fake charge on a token for a specified amount.
     *
     * @param  int  $amount
     * @param  string  $token
     * @param  string  $destinationAccountId
     * @return void
     */
    public function charge($amount, $token, $destinationAccountId)
    {
        if (!is_null($this->beforeFirstChargeCallback)) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if (!$this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
            'destination' => $destinationAccountId,
        ]);
    }

    /**
     * Execute a callback before the first charge is created.
     *
     * @param  \Closure  $callback
     * @return void
     */
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
        return $this->charges->map->amount()->sum();
    }

    /**
     * Get the total of all charges for a specific account.
     *
     * @param  string  $accountId
     * @return int
     */
    public function totalChargesFor($accountId)
    {
        return $this->charges->filter(function ($charge) use ($accountId) {
            return $charge->destination() === $accountId;
        })->map->amount()->sum();
    }
}
