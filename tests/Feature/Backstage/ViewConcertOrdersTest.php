<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Database\Helpers\OrderHelper;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertOrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.orders.show', $concert));

        $response->assertStatus(200);
        $response->assertViewIs('backstage.concerts.orders.show');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function a_promoter_can_view_the_ten_most_recent_orders_for_a_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $oldOrder = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(11)]);
        $recentOrder1 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(10)]);
        $recentOrder2 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(9)]);
        $recentOrder3 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(8)]);
        $recentOrder4 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(7)]);
        $recentOrder5 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(6)]);
        $recentOrder6 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(5)]);
        $recentOrder7 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(4)]);
        $recentOrder8 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(3)]);
        $recentOrder9 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(2)]);
        $recentOrder10 = OrderHelper::createForConcert($concert, ['created_at' => now()->subDays(1)]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.orders.show', $concert));

        $response->data('orders')->assertNotContains($oldOrder);
        $response->data('orders')->assertEquals([
            $recentOrder10,
            $recentOrder9,
            $recentOrder8,
            $recentOrder7,
            $recentOrder6,
            $recentOrder5,
            $recentOrder4,
            $recentOrder3,
            $recentOrder2,
            $recentOrder1,
        ]);
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
