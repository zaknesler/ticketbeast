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
    function user_can_view_a_published_concert_listing()
    {
        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Ln.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '90210',
            'additional_information' => 'For support, call (555) 555-5555.',
        ]);

        $response = $this->get(route('concerts.show', $concert));

        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('December 13, 2016');
        $response->assertSee('8:00pm');
        $response->assertSee('32.50');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Ln.');
        $response->assertSee('Laraville, ON 90210');
        $response->assertSee('For support, call (555) 555-5555.');
    }

    /** @test */
    function user_cannot_view_an_unpublished_concert_listing()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);
    }
}
