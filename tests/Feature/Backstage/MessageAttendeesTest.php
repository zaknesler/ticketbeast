<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendeeMessage;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessageAttendeesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_view_the_message_form_for_their_own_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('backstage.concerts.messages.create', $concert));

        $response->assertStatus(200);
        $response->assertViewIs('backstage.concerts.messages.create');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    function a_promoter_cannot_view_the_message_form_for_another_concert()
    {
        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished();

        $response = $this->actingAs($user)->get(route('backstage.concerts.messages.create', $concert));

        $response->assertStatus(404);
    }

    /** @test */
    function a_guest_cannot_view_the_message_form_for_any_concert()
    {
        $concert = ConcertHelper::createPublished();

        $response = $this->get(route('backstage.concerts.messages.create', $concert));

        $response->assertRedirect('/login');
    }

    /** @test */
    function a_promoter_can_send_a_new_message()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('backstage.concerts.messages.store', $concert), [
                'subject' => 'My Subject',
                'body' => 'My Message',
            ]);

        $response->assertRedirect(route('backstage.concerts.messages.create', $concert));
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();
        $this->assertTrue($concert->is($message->concert));
        $this->assertEquals('My Subject', $message->subject);
        $this->assertEquals('My Message', $message->body);
    }
}
