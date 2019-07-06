<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\Concerts\DeleteOldPoster;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteOldPosterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_deletes_the_old_poster_from_storage()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/old-poster.png',
            file_get_contents(base_path('tests/__stubs__/full-size-poster.png'))
        );
        Storage::disk('public')->put(
            'posters/new-poster.png',
            file_get_contents(base_path('tests/__stubs__/full-size-poster.png'))
        );
        Storage::disk('public')->assertExists('posters/old-poster.png');
        Storage::disk('public')->assertExists('posters/new-poster.png');

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/new-poster.png',
        ]);

        DeleteOldPoster::dispatch($concert, 'posters/old-poster.png');

        Storage::disk('public')->assertMissing('posters/old-poster.png');
        Storage::disk('public')->assertExists('posters/new-poster.png');
    }
}
