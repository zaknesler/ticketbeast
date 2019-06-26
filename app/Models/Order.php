<?php

namespace App\Models;

use App\Models\Ticket;
use App\Models\Concert;
use App\Facades\ConfirmationNumber;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Create an order for specific tickets.
     *
     * @param  array  $tickets
     * @param  string  $email
     * @param  \App\Billing\Charge  $charge
     * @return \App\Models\Order
     */
    public static function forTickets($tickets, $email, $charge)
    {
        $order = self::create([
            'confirmation_number' => ConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour(),
        ]);

        $tickets->each->claimFor($order);

        // $order->tickets()->saveMany($tickets);

        return $order;
    }

    /**
     * Find the order by its confirmation number.
     *
     * @param  string  $confirmationNumber
     * @return \App\Models\Order
     */
    public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
    }

    /**
     * Get the amount of tickets that belong to an order.
     *
     * @return integer
     */
    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    /**
     * An order belongs to a concert.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * An order has many tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'email' => $this->email,
            'confirmation_number' => $this->confirmation_number,
            'amount' => $this->amount,
            'tickets' => $this->tickets->map(function ($ticket) {
                return ['code' => $ticket->code];
            })->all(),
        ];
    }
}
