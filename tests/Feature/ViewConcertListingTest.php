<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // Save for Dusk
    // function user_can_view_a_published_concert_listing()
    // {
    //     $concert = factory(Concert::class)->states('published')->create([
    //         'title' => 'The Red Chord',
    //         'subtitle' => 'with Animosity and Lethargy',
    //         'date' => Carbon::parse('December 13, 2016 8:00pm'),
    //         'ticket_price' => 3250,
    //         'venue' => 'The Mosh Pit',
    //         'venue_address' => '123 Example Ln.',
    //         'city' => 'Laraville',
    //         'state' => 'ON',
    //         'zip' => '17916',
    //         'additional_information' => 'For tickets, call (555) 555-5555.',
    //     ]);

    //     $this->get('/concerts/' . $concert->id);

    //     $this->see('The Red Chord');
    //     $this->see('with Animosity and Lethargy');
    //     $this->see('December 13, 2016');
    //     $this->see('8:00pm');
    //     $this->see('32.50');
    //     $this->see('The Mosh Pit');
    //     $this->see('123 Example Ln.');
    //     $this->see('Laraville, ON 17916');
    //     $this->see('For tickets, call (555) 555-5555.');
    // }

    /** @test */
    function user_cannot_view_an_unpublished_concert_listing()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->get('/concerts/' . $concert->id)->assertStatus(404);
    }
}
