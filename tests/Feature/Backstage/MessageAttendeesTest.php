<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendeeMessage;
use Illuminate\Support\Facades\Queue;
use App\Database\Helpers\ConcertHelper;
use App\Jobs\Concerts\SendAttendeeMessage;
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

        $response->assertRedirect(route('auth.login'));
    }

    /** @test */
    function a_promoter_can_send_a_new_message()
    {
        $this->withoutExceptionHandling();

        Queue::fake();

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

        Queue::assertPushed(SendAttendeeMessage::class, function ($job) use ($message) {
            return $job->attendeeMessage->is($message);
        });
    }

    /** @test */
    function a_promoter_cannot_send_a_new_message_for_other_concerts()
    {
        Queue::fake();

        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished();

        $response = $this->actingAs($user)
            ->post(route('backstage.concerts.messages.store', $concert), [
                'subject' => 'My subject',
                'body' => 'My message',
            ]);

        $response->assertStatus(404);
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(AttendeeMessage::class);
    }

    /** @test */
    function a_guest_cannot_send_a_new_message_for_any_concerts()
    {
        Queue::fake();

        $concert = ConcertHelper::createPublished();

        $response = $this->post(route('backstage.concerts.messages.store', $concert), [
            'subject' => 'My subject',
            'body' => 'My message',
        ]);

        $response->assertRedirect(route('auth.login'));
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(AttendeeMessage::class);
    }

    /** @test */
    function subject_is_required_when_sending_a_message()
    {
        Queue::fake();

        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $response = $this->from(route('backstage.concerts.messages.create', $concert))
            ->actingAs($user)
            ->post(route('backstage.concerts.messages.store', $concert), [
                'subject' => '',
                'body' => 'My message',
            ]);

        $response->assertRedirect(route('backstage.concerts.messages.create', $concert));

        $response->assertSessionHasErrors('subject');
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(AttendeeMessage::class);
    }

    /** @test */
    function body_is_required_when_sending_a_message()
    {
        Queue::fake();

        $user = factory(User::class)->create();
        $concert = ConcertHelper::createPublished(['user_id' => $user->id]);

        $response = $this->from(route('backstage.concerts.messages.create', $concert))
            ->actingAs($user)
            ->post(route('backstage.concerts.messages.store', $concert), [
                'subject' => 'My subject',
                'body' => '',
            ]);

        $response->assertRedirect(route('backstage.concerts.messages.create', $concert));

        $response->assertSessionHasErrors('body');
        $this->assertEquals(0, AttendeeMessage::count());
        Queue::assertNotPushed(AttendeeMessage::class);
    }
}
