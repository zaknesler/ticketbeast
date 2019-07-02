<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\AttendeeMessage;
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
     * Get the number of tickets sold.
     *
     * @return integer
     */
    public function ticketsSold()
    {
        return $this->tickets()->sold()->count();
    }

    /**
     * Get the total numer of tickets.
     *
     * @return int
     */
    public function totalTickets()
    {
        return $this->tickets()->count();
    }

    /**
     * Get the percentage a concert is sold out.
     *
     * @return float
     */
    public function percentSoldOut()
    {
        return number_format(($this->ticketsSold() / $this->totalTickets()) * 100, 2);
    }

    /**
     * Get the total revenue in dollars.
     *
     * @return float
     */
    public function revenueInDollars()
    {
        return $this->orders()->sum('amount') / 100;
    }

    /**
     * Determine if a given concert is published.
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published_at !== null;
    }

    /**
     * Publish a concert.
     *
     * @return void
     */
    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);
        $this->addTickets($this->ticket_quantity);
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
     * Get the orders associated with a concert.
     *
     * @return \Illuminate\Support\Collection
     */
    public function orders()
    {
        return Order::whereIn('id', $this->tickets()->pluck('order_id'));
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
     * A concert has many attendee messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendeeMessages()
    {
        return $this->hasMany(AttendeeMessage::class);
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
