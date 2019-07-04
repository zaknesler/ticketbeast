<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get an invitation by its code.
     *
     * @param  string  $code
     * @return \App\Models\Invitation
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->firstOrFail();
    }

    /**
     * Determine if an invitation has been used.
     *
     * @return boolean
     */
    public function hasBeenUsed()
    {
        return !is_null($this->user_id);
    }

    /**
     * An invitation belongs to a user.
     *
     * @return \App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
