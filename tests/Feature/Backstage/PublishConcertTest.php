<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_promoter_can_publish_their_own_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createUnpublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)
            ->post(route('backstage.publishedConcerts.store'), [
                'concert_id' => $concert->id,
            ]);

        $response->assertRedirect(route('backstage.concerts.index'));

        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function a_concert_can_only_be_published_once()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)
            ->post(route('backstage.publishedConcerts.store'), [
                'concert_id' => $concert->id,
            ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }

    /** @test */
    function a_promoter_cannot_publish_other_concerts()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createUnpublished([
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertStatus(404);

        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function a_guest_cannot_publish_concerts()
    {
        $concert = ConcertHelper::createUnpublished([
            'ticket_quantity' => 3,
        ]);

        $response = $this->post('/backstage/published-concerts', [
            'concert_id' => $concert->id,
        ]);

        $response->assertRedirect(route('login'));

        $concert = $concert->fresh();
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());
    }

    /** @test */
    function concerts_that_do_not_exist_cannot_be_published()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => 999,
        ]);

        $response->assertStatus(404);
    }
}
