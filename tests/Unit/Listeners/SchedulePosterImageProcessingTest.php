<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use App\Events\ConcertAdded;
use App\Events\ConcertUpdated;
use Illuminate\Support\Facades\Queue;
use App\Database\Helpers\ConcertHelper;
use App\Jobs\Concerts\ProcessPosterImage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchedulePosterImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_queues_a_job_to_process_a_poster_image_if_one_is_present_when_a_concert_is_added()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });
    }

    /** @test */
    function a_job_is_not_queued_if_a_poster_is_not_present_when_a_concert_is_added()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => null,
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
    }

    /** @test */
    function it_queues_a_job_to_process_a_poster_image_if_one_is_present_when_a_concert_is_updated()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ConcertUpdated::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });
    }

    /** @test */
    function a_job_is_not_queued_if_a_poster_is_not_present_when_a_concert_is_updated()
    {
        Queue::fake();

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => null,
        ]);

        ConcertUpdated::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
    }
}
