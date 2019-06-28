<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Reservations\Reservation;
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
     * Hold a certain number of tickets in reserve.
     *
     * @param  integer  $quantity
     * @param  string  $email
     * @return \App\Reservations\Reservation
     */
    public function reserveTickets($quantity, $email)
    {
        $tickets = $this->findTickets($quantity)->each(function ($ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
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

    /**
     * A concert belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
