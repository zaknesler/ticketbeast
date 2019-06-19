<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use App\Billing\Exceptions\NotEnoughTicketsException;

class Concert extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * Determine if a concert has an order for a specified email.
     *
     * @param  string  $email
     * @return boolean
     */
    public function hasOrderFor($email)
    {
        return $this->orders()->where('email', $email)->count() > 0;
    }

    /**
     * Get all of the orders from a specified email.
     *
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor($email)
    {
        return $this->orders()->where('email', $email)->get();
    }

    /**
     * Order a specified amount of tickets for a specified email address.
     *
     * @param  string  $email
     * @param  integer  $ticketQuantity
     * @return App\Order
     */
    public function orderTickets($email, $ticketQuantity)
    {
        $tickets = $this->findTickets($ticketQuantity);

        return $this->createOrder($email, $tickets);
    }

    /**
     * Hold a certain number of tickets in reserve.
     *
     * @param  int $quantity
     * @return void
     */
    public function reserveTickets($quantity)
    {
        return $this->findTickets($quantity)
                    ->each(function ($ticket) {
                        $ticket->reserve();
                    });
    }

    /**
     * Grab a specified quantity of tickets, as long as the are available.
     *
     * @param  integer  $quantity
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotEnoughTicketsException;
        }

        return $tickets;
    }

    /**
     * Create the order for a specified email and tickets.
     *
     * @param  string  $email
     * @param  \Illuminate\Database\Eloquent\Collection  $tickets
     * @return App\Order
     */
    public function createOrder($email, $tickets)
    {
        $order = $this->orders()->create([
            'email' => $email,
            'amount' => $tickets->count() * $this->ticket_price,
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    /**
     * Add a specified quantity of tickets to a concert.
     *
     * @param  integer  $quantity
     * @return $this
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     * Get the number of available tickets remaining.
     *
     * @return integer
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    /**
     * Scope a query to only include published concerts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Format the date to a readable string.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    /**
     * Format the start time to a readable string.
     *
     * @return string
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    /**
     * Get the ticket price in a readable dollar format.
     *
     * @return string
     */
    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     * A concert has many orders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A concert has many tickets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
