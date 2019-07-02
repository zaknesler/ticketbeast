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
     * Get all of the orders associated with the related concert.
     *
     * @return array
     */
    public function orders()
    {
        return $this->concert->orders();
    }

    /**
     * Fetch the recipients in chunks at a time.
     *
     * @param  int  $chunkSize
     * @param  callback  $callback
     * @return \Illuminate\Support\Collection
     */
    public function withChunkedRecipients($chunkSize, $callback)
    {
        $this->orders()->chunk($chunkSize, function ($orders) use ($callback) {
            $callback($orders->pluck('email'));
        });
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
