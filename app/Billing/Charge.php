<?php

namespace App\Billing;

class Charge
{
    /**
     * The data associated with a charge.
     *
     * @var array
     */
    private $data;

    /**
     * Create a new charge instance based on some data.
     *
     * @param  array  $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the last four digits of the charge.
     *
     * @return string
     */
    public function cardLastFour()
    {
        return $this->data['card_last_four'];
    }

    /**
     * Get the amount of the charge.
     *
     * @return int
     */
    public function amount()
    {
        return $this->data['amount'];
    }

    /**
     * Get the destination account identifier.
     *
     * @return string
     */
    public function destination()
    {
        return $this->data['destination'];
    }
}
