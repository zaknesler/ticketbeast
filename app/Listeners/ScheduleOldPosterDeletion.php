<?php

namespace App\Listeners;

use App\Jobs\Concerts\DeleteOldPoster;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScheduleOldPosterDeletion
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->oldImagePath === null) {
            return;
        }

        DeleteOldPoster::dispatch($event->concert, $event->oldImagePath);
    }
}
