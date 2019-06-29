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

    /**
     * Get the valid parameters for a successful request.
     *
     * @param  array  $overrides
     * @return array
     */
    private function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'Example Band',
            'subtitle' => 'Example Subtitle',
            'additional_information' => "Some extra info.",
            'date' => '2050-11-18',
            'time' => '8:45pm',
            'venue' => 'Fake Venue',
            'venue_address' => '123 Fake St.',
            'city' => 'Fakeville',
            'state' => 'FK',
            'zip' => '12345',
            'ticket_price' => '59.50',
            'ticket_quantity' => '50',
        ], $overrides);
    }

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

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), [
            'title' => 'Example Band',
            'subtitle' => 'Example Subtitle',
            'additional_information' => "Some extra info.",
            'date' => '2050-11-18',
            'time' => '8:45pm',
            'venue' => 'Fake Venue',
            'venue_address' => '123 Fake St.',
            'city' => 'Fakeville',
            'state' => 'FK',
            'zip' => '12345',
            'ticket_price' => '59.50',
            'ticket_quantity' => '50',
        ]);

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertRedirect(route('concerts.show', $concert));

        $this->assertTrue($concert->user->is($user));
        $this->assertTrue($concert->isPublished());

        $this->assertEquals('Example Band', $concert->title);
        $this->assertEquals('Example Subtitle', $concert->subtitle);
        $this->assertEquals("Some extra info.", $concert->additional_information);
        $this->assertEquals(Carbon::parse('2050-11-18 8:45pm'), $concert->date);
        $this->assertEquals('Fake Venue', $concert->venue);
        $this->assertEquals('123 Fake St.', $concert->venue_address);
        $this->assertEquals('Fakeville', $concert->city);
        $this->assertEquals('FK', $concert->state);
        $this->assertEquals('12345', $concert->zip);
        $this->assertEquals(5950, $concert->ticket_price);
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function guests_cannot_add_concerts()
    {
        $response = $this->post(route('backstage.concerts.store'), $this->validParams());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    function title_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'title' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    function subtitle_is_optional()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'subtitle' => '',
        ]));

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('subtitle');
        $this->assertNull($concert->subtitle);
        $this->assertTrue($concert->user->is($user));

        $this->assertTrue($concert->isPublished());
    }

    /** @test */
    function additional_information_is_optional()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'additional_information' => '',
        ]));

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('additional_information');
        $this->assertNull($concert->additional_information);
        $this->assertTrue($concert->user->is($user));
    }

    /** @test */
    function date_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'date' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date');
    }

    /** @test */
    function date_must_be_properly_formatted()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'date' => '2019-01-',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date');
    }

    /** @test */
    function properly_formatted_dates_are_allowed()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'date' => '2019-01-02',
        ]));

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('date');
    }

    /** @test */
    function time_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'time' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('time');
    }

    /** @test */
    function time_must_be_properly_formatted()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'time' => '10:45',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('time');
    }

    /** @test */
    function properly_formatted_times_are_allowed()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'time' => '10:45pm',
        ]));

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('time');
    }

    /** @test */
    function venue_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'venue' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('venue');
    }

    /** @test */
    function venue_address_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'venue_address' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('venue_address');
    }

    /** @test */
    function city_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'city' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('city');
    }

    /** @test */
    function state_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'state' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('state');
    }

    /** @test */
    function zip_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'zip' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('zip');
    }

    /** @test */
    function ticket_price_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_price' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_price_must_be_numeric()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_price' => 'not a number',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_price_must_be_greater_than_five_dollars()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_price' => '4.99',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_quantity_is_required()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_quantity' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_an_integer()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_quantity' => 11.5,
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_greater_than_one()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('backstage.concerts.store'), $this->validParams([
            'ticket_quantity' => 0,
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('ticket_quantity');
    }
}
