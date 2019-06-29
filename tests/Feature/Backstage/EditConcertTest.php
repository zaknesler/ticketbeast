<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditConcertTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertRedirect(route('login'));
    }

    /** @test */
    function guests_are_asked_to_login_when_attempting_to_view_the_edit_form_for_a_concert_that_does_not_exist()
    {
        $response = $this->get(route('backstage.concerts.edit', 999));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
}
