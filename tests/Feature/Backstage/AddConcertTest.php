<?php

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function promoters_can_view_the_add_concert_page()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertSuccessful();
    }

    /** @test */
    function guests_cannot_view_the_add_concert_form()
    {
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
    }

    /** @test */
    function promoters_can_add_a_valid_concert()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/concerts', [
            'title' => 'No Warning',
            'subtitle' => 'with Cruel Hand and Backtrack',
            'additional_information' => "You must be 19 years of age to attend this concert.",
            'date' => '2017-11-18',
            'time' => '8:00pm',
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Fake St.',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '12345',
            'ticket_price' => '32.50',
            'ticket_quantity' => '75',
        ]);

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertRedirect("/concerts/{$concert->id}");
        $this->assertEquals('No Warning', $concert->title);
        $this->assertEquals('with Cruel Hand and Backtrack', $concert->subtitle);
        $this->assertEquals("You must be 19 years of age to attend this concert.", $concert->additional_information);
        $this->assertEquals(Carbon::parse('2017-11-18 8:00pm'), $concert->date);
        $this->assertEquals('The Mosh Pit', $concert->venue);
        $this->assertEquals('123 Fake St.', $concert->venue_address);
        $this->assertEquals('Laraville', $concert->city);
        $this->assertEquals('ON', $concert->state);
        $this->assertEquals('12345', $concert->zip);
        $this->assertEquals(3250, $concert->ticket_price);
        $this->assertEquals(75, $concert->ticketsRemaining());
    }
}
