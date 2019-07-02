<?php

namespace App\Models;

use App\Models\Concert;
use Illuminate\Database\Eloquent\Model;

class AttendeeMessage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the email addresses associated with the concert's orders.
     *
     * @return array
     */
    public function recipients()
    {
        return $this->concert->orders()->pluck('email');
    }

    /**
     * An attendee message belongs to a concert.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }
}
