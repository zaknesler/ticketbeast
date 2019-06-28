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
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), [
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
        $response->assertRedirect(route('concerts.show', $concert));
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

    /** @test */
    function guests_cannot_add_concerts()
    {
        $response = $this->post(route('backstage.concerts.store'), [
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

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    function invalid_form_input_returns_proper_validation_errors()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'));

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'title',
            'date',
            'time',
            'venue',
            'venue_address',
            'city',
            'state',
            'zip',
            'ticket_price',
            'ticket_quantity',
        ]);
    }

    /** @test */
    function ticket_quantity_must_be_an_integer()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), [
            'ticket_quantity' => 11.5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_greater_than_one()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), [
            'ticket_quantity' => 0,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_price_must_be_numeric()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), [
            'ticket_price' => 'not a number',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_price');
    }
}
