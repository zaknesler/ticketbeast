<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Concert;
use App\Facades\TicketCode;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Release a ticket to make it available.
     *
     * @return void
     */
    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    /**
     * Save a ticket to an order.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function claimFor(Order $order)
    {
        $this->code = TicketCode::generateFor($this);

        $order->tickets()->save($this);
    }

    /**
     * Get the price of a ticket from the concert's ticket price.
     *
     * @return int
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }

    /**
     * Scope a query to only include available tickets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')
                     ->whereNull('reserved_at');
    }

    /**
     * Scope a query to only include tickets already sold.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSold($query)
    {
        return $query->whereNotNull('order_id');
    }

    /**
     * Mark the ticket as reserved.
     *
     * @return void
     */
    public function reserve()
    {
        $this->update(['reserved_at' => now()]);
    }

    /**
     * A ticket belongs to an order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * A ticket belongs to an concert.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }
}
