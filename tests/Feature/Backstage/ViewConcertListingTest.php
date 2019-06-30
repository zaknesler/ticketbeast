<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use App\Database\Helpers\ConcertHelper;
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
        $otherUser = factory(User::class)->create();

        $publishedA = ConcertHelper::createPublished(['user_id' => $user->id]);
        $publishedB = ConcertHelper::createPublished(['user_id' => $otherUser->id]);
        $publishedC = ConcertHelper::createPublished(['user_id' => $user->id]);

        $unpublishedA = ConcertHelper::createUnpublished(['user_id' => $user->id]);
        $unpublishedB = ConcertHelper::createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedC = ConcertHelper::createUnpublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.index'));

        $response->assertSuccessful();

        $response->data('publishedConcerts')->assertContains($publishedA);
        $response->data('publishedConcerts')->assertNotContains($publishedB);
        $response->data('publishedConcerts')->assertContains($publishedC);

        $response->data('publishedConcerts')->assertNotContains($unpublishedA);
        $response->data('publishedConcerts')->assertNotContains($unpublishedB);
        $response->data('publishedConcerts')->assertNotContains($unpublishedC);

        $response->data('unpublishedConcerts')->assertContains($unpublishedA);
        $response->data('unpublishedConcerts')->assertNotContains($unpublishedB);
        $response->data('unpublishedConcerts')->assertContains($unpublishedC);

        $response->data('unpublishedConcerts')->assertNotContains($publishedA);
        $response->data('unpublishedConcerts')->assertNotContains($publishedB);
        $response->data('unpublishedConcerts')->assertNotContains($publishedC);
    }
}
