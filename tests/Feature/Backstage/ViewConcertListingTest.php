<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get(route('backstage.concerts.index'));

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $user = factory(User::class)->create();
        $ownedConcertA = factory(Concert::class)->create(['user_id' => $user->id]);
        $ownedConcertB = factory(Concert::class)->create(['user_id' => $user->id]);
        $unownedConcertA = factory(Concert::class)->create();
        $unownedConcertB = factory(Concert::class)->create();

        $response = $this->actingAs($user)->get(route('backstage.concerts.index'));

        $response->assertSuccessful();
        $response->data('concerts')->assertContains($ownedConcertA);
        $response->data('concerts')->assertContains($ownedConcertB);
        $response->data('concerts')->assertNotContains($unownedConcertA);
        $response->data('concerts')->assertNotContains($unownedConcertB);
    }
}
