<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\AttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Support\Facades\Mail;
use App\Database\Helpers\OrderHelper;
use App\Database\Helpers\ConcertHelper;
use App\Jobs\Concerts\SendAttendeeMessage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendAttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function the_send_attendee_message_test_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();

        $concert = ConcertHelper::createPublished();
        $otherConcert = ConcertHelper::createPublished();
        $message = factory(AttendeeMessage::class)->create([
            'concert_id' => $concert->id,
            'subject' => 'Example subject',
            'body' => 'Example body',
        ]);
        $orderA = OrderHelper::createForConcert($concert, ['email' => 'steve@example.com']);
        $orderB = OrderHelper::createForConcert($concert, ['email' => 'jane@example.com']);
        $orderC = OrderHelper::createForConcert($concert, ['email' => 'john@example.com']);
        $otherOrder = OrderHelper::createForConcert($otherConcert, ['email' => 'nope@example.com']);

        SendAttendeeMessage::dispatch($message);
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('steve@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('jane@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('john@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertNotQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('nope@example.com');
        });
    }
}
