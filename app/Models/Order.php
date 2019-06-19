<?php

namespace App\Models;

use App\Models\Ticket;
use App\Models\Concert;
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
     * Cancel an order and delete it from the database.
     *
     * @return void
     */
    public function cancel()
    {
        $this->tickets()->each(function ($ticket) {
            $ticket->release();
        });

        $this->delete();
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
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }
}
