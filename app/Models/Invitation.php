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
        return self::where('code', $code)->first();
    }
}
