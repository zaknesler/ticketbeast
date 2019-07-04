<?php

namespace Tests\Feature\Backstage;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get the valid request parameters.
     *
     * @param  array  $overrides
     * @return array
     */
    private function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'New title',
            'subtitle' => 'New subtitle',
            'additional_information' => 'New additional information',
            'date' => '2018-12-31',
            'time' => '8:00pm',
            'venue' => 'New venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '99999',
            'ticket_price' => '72.50',
            'ticket_quantity' => '10',
        ], $overrides);
    }

    /** @test */
    function promoters_can_view_the_edit_form_for_their_own_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create(['user_id' => $user->id]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->get(route('backstage.concerts.edit', $concert));

        $response->assertStatus(200);
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_their_own_published_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create(['user_id' => $user->id]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->get(route('backstage.concerts.edit', $concert));

        $response->assertStatus(403);
    }

    /** @test */
    function promoters_cannot_view_the_edit_form_for_other_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create();

        $response = $this->actingAs($user)->get(route('backstage.concerts.edit', $concert));

        $response->assertStatus(404);
    }

    /** @test */
    function promoters_see_a_404_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('backstage.concerts.edit', 999));

        $response->assertStatus(404);
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_any_concert()
    {
        $concert = factory(Concert::class)->create();

        $response = $this->get(route('backstage.concerts.edit', $concert));

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login'));
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get(route('backstage.concerts.edit', 999));

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login'));
    }

    /** @test */
    function promoters_can_edit_their_own_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '00000',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.index'))
            ->patch(route('backstage.concerts.update', $concert), [
                'title' => 'New title',
                'subtitle' => 'New subtitle',
                'additional_information' => 'New additional information',
                'date' => '2018-12-31',
                'time' => '8:00pm',
                'venue' => 'New venue',
                'venue_address' => 'New address',
                'city' => 'New city',
                'state' => 'New state',
                'zip' => '99999',
                'ticket_price' => '72.50',
                'ticket_quantity' => '10',
            ]);

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.index'));
        $this->assertEquals('New title', $concert->title);
        $this->assertEquals('New subtitle', $concert->subtitle);
        $this->assertEquals('New additional information', $concert->additional_information);
        $this->assertEquals(Carbon::parse('2018-12-31 8:00pm'), $concert->date);
        $this->assertEquals('New venue', $concert->venue);
        $this->assertEquals('New address', $concert->venue_address);
        $this->assertEquals('New city', $concert->city);
        $this->assertEquals('New state', $concert->state);
        $this->assertEquals('99999', $concert->zip);
        $this->assertEquals(7250, $concert->ticket_price);
        $this->assertEquals(10, $concert->ticket_quantity);
    }

    /** @test */
    function promoters_cannot_other_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create();
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->patch(route('backstage.concerts.update', $concert), $this->validParams());

        $concert = $concert->fresh();
        $response->assertStatus(404);
        $this->assertNotEquals('New title', $concert->title);
        $this->assertNotEquals('New subtitle', $concert->subtitle);
        $this->assertNotEquals('New additional information', $concert->additional_information);
        $this->assertNotEquals(Carbon::parse('2018-12-31 8:00pm'), $concert->date);
        $this->assertNotEquals('New venue', $concert->venue);
        $this->assertNotEquals('New address', $concert->venue_address);
        $this->assertNotEquals('New city', $concert->city);
        $this->assertNotEquals('New state', $concert->state);
        $this->assertNotEquals('99999', $concert->zip);
        $this->assertNotEquals(7250, $concert->ticket_price);
    }

    /** @test */
    function promoters_cannot_edit_their_own_concerts_that_are_published()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->states('published')->create(['user_id' => $user->id]);
        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)
            ->patch(route('backstage.concerts.update', $concert), $this->validParams());

        $concert = $concert->fresh();

        $response->assertStatus(403);
        $this->assertNotEquals('New title', $concert->title);
        $this->assertNotEquals('New subtitle', $concert->subtitle);
        $this->assertNotEquals('New additional information', $concert->additional_information);
        $this->assertNotEquals(Carbon::parse('2018-12-31 8:00pm'), $concert->date);
        $this->assertNotEquals('New venue', $concert->venue);
        $this->assertNotEquals('New address', $concert->venue_address);
        $this->assertNotEquals('New city', $concert->city);
        $this->assertNotEquals('New state', $concert->state);
        $this->assertNotEquals('99999', $concert->zip);
        $this->assertNotEquals(7250, $concert->ticket_price);
    }

    /** @test */
    function guests_cannot_edit_concerts()
    {
        $concert = factory(Concert::class)->create();
        $this->assertFalse($concert->isPublished());

        $response = $this->patch(route('backstage.concerts.update', $concert), $this->validParams());

        $concert = $concert->fresh();

        $response->assertRedirect(route('auth.login'));
        $this->assertNotEquals('New title', $concert->title);
        $this->assertNotEquals('New subtitle', $concert->subtitle);
        $this->assertNotEquals('New additional information', $concert->additional_information);
        $this->assertNotEquals(Carbon::parse('2018-12-31 8:00pm'), $concert->date);
        $this->assertNotEquals('New venue', $concert->venue);
        $this->assertNotEquals('New address', $concert->venue_address);
        $this->assertNotEquals('New city', $concert->city);
        $this->assertNotEquals('New state', $concert->state);
        $this->assertNotEquals('99999', $concert->zip);
        $this->assertNotEquals(7250, $concert->ticket_price);
    }

    /** @test */
    function title_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'title' => 'Original title',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'title' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('title');
        $this->assertEquals('Original title', $concert->title);
    }

    /** @test */
    function subtitle_is_optional_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'subtitle' => 'Original subtitle',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'subtitle' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.index'));
        $response->assertSessionDoesntHaveErrors('subtitle');
        $this->assertNull($concert->subtitle);
    }

    /** @test */
    function additional_information_is_optional_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'additional_information' => 'Original additional information',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'additional_information' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.index'));
        $response->assertSessionDoesntHaveErrors('additional_information');
        $this->assertNull($concert->additional_information);
    }

    /** @test */
    function date_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => '2018-01-31 05:50:00',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'date' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('date');
        $this->assertEquals('2018-01-31 05:50:00', $concert->date);
    }

    /** @test */
    function date_must_be_properly_formatted_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => '2018-01-31 05:50:00',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'date' => '2050-01-',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('date');
        $this->assertEquals('2018-01-31 05:50:00', $concert->date);
    }

    /** @test */
    function time_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => '2018-01-31 05:50:00',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'time' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('time');
        $this->assertEquals('2018-01-31 05:50:00', $concert->date);
    }

    /** @test */
    function time_must_be_properly_formatted_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'date' => '2018-01-31 05:50:00',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'time' => '10:30',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('time');
        $this->assertEquals('2018-01-31 05:50:00', $concert->date);
    }

    /** @test */
    function venue_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'venue' => 'Original venue',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'venue' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('venue');
        $this->assertEquals('Original venue', $concert->venue);
    }

    /** @test */
    function venue_address_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'venue_address' => 'Original venue address',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'venue_address' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('venue_address');
        $this->assertEquals('Original venue address', $concert->venue_address);
    }

    /** @test */
    function city_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'city' => 'Original city',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'city' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('city');
        $this->assertEquals('Original city', $concert->city);
    }

    /** @test */
    function state_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'state' => 'Original state',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'state' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('state');
        $this->assertEquals('Original state', $concert->state);
    }

    /** @test */
    function zip_code_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'zip' => 'Original zip',
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'zip' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('zip');
        $this->assertEquals('Original zip', $concert->zip);
    }

    /** @test */
    function ticket_price_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 1234,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_price' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(1234, $concert->ticket_price);
    }

    /** @test */
    function ticket_price_must_be_numeric_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 1234,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_price' => 'not-numeric',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(1234, $concert->ticket_price);
    }

    /** @test */
    function ticket_price_must_be_greater_than_five_dollars_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_price' => 1234,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_price' => '4.99',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_price');
        $this->assertEquals(1234, $concert->ticket_price);
    }

    /** @test */
    function ticket_quantity_is_required_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_quantity' => '',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(5, $concert->ticket_quantity);
    }

    /** @test */
    function ticket_quantity_must_be_an_integer_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_quantity' => 'not-numeric',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(5, $concert->ticket_quantity);
    }

    /** @test */
    function ticket_quantity_must_be_greater_than_one_when_updating_an_unpublished_concert()
    {
        $user = factory(User::class)->create();
        $concert = factory(Concert::class)->create([
            'user_id' => $user->id,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)
            ->from(route('backstage.concerts.edit', $concert))
            ->patch(route('backstage.concerts.update', $concert), $this->validParams([
                'ticket_quantity' => '0',
            ]));

        $concert = $concert->fresh();

        $response->assertRedirect(route('backstage.concerts.edit', $concert));
        $response->assertSessionHasErrors('ticket_quantity');
        $this->assertEquals(5, $concert->ticket_quantity);
    }
}
