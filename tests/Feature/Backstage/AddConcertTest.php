<?php

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use App\Events\ConcertAdded;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
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

        $response = $this->actingAs($user)->get(route('backstage.concerts.create'));

        $response->assertSuccessful();
    }

    /** @test */
    function guests_cannot_view_the_add_concert_form()
    {
        $response = $this->get(route('backstage.concerts.create'));

        $response->assertStatus(302);
    }

    /** @test */
    function promoters_can_add_a_valid_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), [
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

        $response->assertRedirect(route('backstage.concerts.index'));

        $this->assertTrue($concert->user->is($user));
        $this->assertFalse($concert->isPublished());

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
        $this->assertEquals(50, $concert->ticket_quantity);
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function guests_cannot_add_concerts()
    {
        $response = $this->post(route('backstage.concerts.store'), $this->validParams());

        $response->assertRedirect(route('auth.login'));
        $this->assertEquals(0, Concert::count());
    }

    /** @test */
    function title_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'title' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    function subtitle_is_optional_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'subtitle' => '',
            ]));

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('subtitle');
        $this->assertNull($concert->subtitle);
        $this->assertTrue($concert->user->is($user));
    }

    /** @test */
    function additional_information_is_optional_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'additional_information' => '',
            ]));

        $concert = Concert::first();

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('additional_information');
        $this->assertNull($concert->additional_information);
        $this->assertTrue($concert->user->is($user));
    }

    /** @test */
    function date_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'date' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('date');
    }

    /** @test */
    function date_must_be_properly_formatted_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'date' => '2019-01-',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('date');
    }

    /** @test */
    function properly_formatted_dates_are_allowed_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'date' => '2019-01-02',
            ]));

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('date');
    }

    /** @test */
    function time_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'time' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('time');
    }

    /** @test */
    function time_must_be_properly_formatted_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'time' => '10:45',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('time');
    }

    /** @test */
    function properly_formatted_times_are_allowed_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'time' => '10:45pm',
            ]));

        $response->assertStatus(302);
        $response->assertSessionDoesntHaveErrors('time');
    }

    /** @test */
    function venue_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'venue' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('venue');
    }

    /** @test */
    function venue_address_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'venue_address' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('venue_address');
    }

    /** @test */
    function city_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'city' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('city');
    }

    /** @test */
    function state_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'state' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('state');
    }

    /** @test */
    function zip_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'zip' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('zip');
    }

    /** @test */
    function ticket_price_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_price' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_price_must_be_numeric_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_price' => 'not a number',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_price_must_be_greater_than_five_dollars_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_price' => '4.99',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_price');
    }

    /** @test */
    function ticket_quantity_is_required_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_quantity' => '',
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_an_integer_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_quantity' => 11.5,
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_greater_than_one_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'ticket_quantity' => 0,
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    function a_poster_image_is_uploaded_if_included_when_creating_a_concert()
    {
        Event::fake([ConcertAdded::class]);
        Storage::fake('public');
        $user = factory(User::class)->create();
        $file = File::image('concert-poster.png', 850, 1100);

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'poster_image' => $file,
            ]));

        $concert = Concert::first();
        $this->assertNotNull($concert->poster_image_path);
        Storage::disk('public')->assertExists($concert->poster_image_path);
        $this->assertFileEquals(
            $file->getPathName(),
            Storage::disk('public')->path($concert->poster_image_path)
        );
    }

    /** @test */
    function poster_image_must_be_a_valid_image_when_creating_a_concert()
    {
        Storage::fake('public');
        $user = factory(User::class)->create();
        $file = File::create('not-a-poster.pdf');

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'poster_image' => $file,
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('poster_image');
    }

    /** @test */
    function poster_image_must_be_at_least_600_pixels_wide_when_creating_a_concert()
    {
        Storage::fake('public');
        $user = factory(User::class)->create();
        $file = File::create('concert-poster.png', 599, 775);

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'poster_image' => $file,
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('poster_image');
    }

    /** @test */
    function poster_image_must_be_have_letter_aspect_ratio_when_creating_a_concert()
    {
        Storage::fake('public');
        $user = factory(User::class)->create();
        $file = File::create('concert-poster.png', 851, 1100);

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'poster_image' => $file,
            ]));

        $response->assertRedirect(route('backstage.concerts.create'));
        $response->assertSessionHasErrors('poster_image');
    }

    /** @test */
    function poster_image_is_optional_when_creating_a_concert()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams([
                'poster_image' => null,
            ]));

        $concert = Concert::first();
        $response->assertRedirect(route('backstage.concerts.index'));
        $response->assertSessionDoesntHaveErrors('poster_image');
        $this->assertTrue($concert->user->is($user));
        $this->assertNull($concert->poster_image_path);
    }

    /** @test */
    function an_event_is_fired_when_a_concert_is_added()
    {
        Event::fake([ConcertAdded::class]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.create'))
            ->post(route('backstage.concerts.store'), $this->validParams());

        Event::assertDispatched(ConcertAdded::class, function ($event) {
            return $event->concert->is(Concert::first());
        });
    }
}
