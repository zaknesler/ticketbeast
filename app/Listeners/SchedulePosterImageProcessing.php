<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\Concerts\ProcessPosterImage;
use Illuminate\Contracts\Queue\ShouldQueue;

class SchedulePosterImageProcessing
{
    /**
     * Handle the event.
     *
     * @param  Object  $event
     * @return void
     */
    public function handle($event)
    {
        if (!$event->concert->hasPoster()) {
            return;
        }

        ProcessPosterImage::dispatch($event->concert);
    }
}
