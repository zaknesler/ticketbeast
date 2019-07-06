<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Events\ConcertUpdated;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Concerts\DeleteOldPoster;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleOldPosterDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_queues_a_job_to_delete_the_old_poster_if_one_exists_when_a_concert_is_updated()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ConcertUpdated::dispatch($concert, 'posters/example-poster.png');

        Queue::assertPushed(DeleteOldPoster::class, function ($job) use ($concert) {
            return $job->concert->is($concert)
                && $job->oldImagePath === 'posters/example-poster.png';
        });
    }

    /** @test */
    function a_job_is_not_queued_if_no_old_poster_was_passed_through_when_a_concert_is_updated()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => null,
        ]);

        ConcertUpdated::dispatch($concert, null);

        Queue::assertNotPushed(DeleteOldPoster::class);
    }
}
