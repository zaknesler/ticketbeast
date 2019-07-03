<?php

namespace App\Listeners;

use App\Events\ConcertAdded;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\Concerts\ProcessPosterImage;
use Illuminate\Contracts\Queue\ShouldQueue;

class SchedulePosterImageProcessing
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ConcertAdded  $event
     * @return void
     */
    public function handle(ConcertAdded $event)
    {
        if (!$event->concert->hasPoster()) {
            return;
        }

        ProcessPosterImage::dispatch($event->concert);
    }
}
