<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Support\Facades\Storage;
use App\Jobs\Concerts\ProcessPosterImage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessPosterImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_resizes_the_poster_image_to_600_pixels_wide()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__stubs__/full-size-poster.png'))
        );

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $resizedImage = Storage::disk('public')->get('posters/example-poster.png');
        list($width, $height) = getimagesizefromstring($resizedImage);

        $this->assertEquals(600, $width);
        $this->assertEquals(776, $height);

        $resizedImageContents = Storage::disk('public')->get('posters/example-poster.png');
        $controlImageContents = file_get_contents(base_path('tests/__stubs__/optimized-poster.png'));

        $this->assertEquals($controlImageContents, $resizedImageContents, 'Failed asserting that the optimized image matches a control.');
    }

    /** @test */
    function it_optimizes_the_poster_image()
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'posters/example-poster.png',
            file_get_contents(base_path('tests/__stubs__/small-unoptimized-poster.png'))
        );

        $concert = ConcertHelper::createUnpublished([
            'poster_image_path' => 'posters/example-poster.png',
        ]);

        ProcessPosterImage::dispatch($concert);

        $originalImageSize = filesize(base_path('tests/__stubs__/small-unoptimized-poster.png'));
        $optimizedImageSize = Storage::disk('public')->size('posters/example-poster.png');

        $this->assertLessThan($originalImageSize, $optimizedImageSize);

        $optimizedImageContents = Storage::disk('public')->get('posters/example-poster.png');
        $controlImageContents = file_get_contents(base_path('tests/__stubs__/optimized-poster.png'));

        $this->assertEquals($controlImageContents, $optimizedImageContents, 'Failed asserting that the optimized image matches a control.');
    }
}
