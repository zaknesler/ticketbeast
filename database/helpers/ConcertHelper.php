<?php

namespace App\Database\Helpers;

use App\Models\Concert;

class ConcertHelper
{
    /**
     * Create a published concert.
     *
     * @param  array  $overrides
     * @return \App\Models\Concert
     */
    public static function createPublished($overrides = [])
    {
        $concert = factory(Concert::class)->create($overrides);
        $concert->publish();

        return $concert;
    }

    /**
     * Create an unpublished concert.
     *
     * @param  array  $overrides
     * @return \App\Models\Concert
     */
    public static function createUnpublished($overrides = [])
    {
        $concert = factory(Concert::class)->create($overrides);

        return $concert;
    }
}
