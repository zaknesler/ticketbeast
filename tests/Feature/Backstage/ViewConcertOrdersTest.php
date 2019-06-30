<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.orders.show', $concert));

        $response->assertStatus(200);
        $response->assertViewIs('backstage.concerts.orders.show');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_unpublished_concerts()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createUnpublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.orders.show', $concert));

        $response->assertStatus(404);
    }

    /** @test */
    function a_promoter_cannot_view_the_orders_of_another_published_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished();

        $response = $this->actingAs($user)->get(route('backstage.concerts.orders.show', $concert));

        $response->assertStatus(404);
    }

    /** @test */
    function a_guest_cannot_view_the_orders_of_any_published_concert()
    {
        $concert = ConcertHelper::createPublished();

        $response = $this->get(route('backstage.concerts.orders.show', $concert));

        $response->assertRedirect(route('login'));
    }
}
