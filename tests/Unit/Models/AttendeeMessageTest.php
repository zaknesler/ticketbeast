<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\AttendeeMessage;
use App\Database\Helpers\OrderHelper;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeMessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function orders_can_be_fetched_for_an_attendee_message()
    {
        $concert = ConcertHelper::createPublished();
        $orderA = OrderHelper::createForConcert($concert, ['email' => 'john@example.com']);
        $orderB = OrderHelper::createForConcert($concert, ['email' => 'jane@example.com']);
        $orderC = OrderHelper::createForConcert($concert, ['email' => 'alex@example.com']);

        $attendeeMessage = factory(AttendeeMessage::class)->create([
            'concert_id' => $concert->id,
        ]);

        $orders = $attendeeMessage->orders()->get();

        $this->assertTrue($orders->contains($orderA));
        $this->assertTrue($orders->contains($orderB));
        $this->assertTrue($orders->contains($orderC));
    }

    /** @test */
    function orders_can_be_chunked_by_recipient()
    {
        $concert = ConcertHelper::createPublished();
        $orderA = OrderHelper::createForConcert($concert, ['email' => 'john@example.com']);
        $orderB = OrderHelper::createForConcert($concert, ['email' => 'jane@example.com']);
        $orderC = OrderHelper::createForConcert($concert, ['email' => 'alex@example.com']);

        $attendeeMessage = factory(AttendeeMessage::class)->create([
            'concert_id' => $concert->id,
        ]);

        $allRecipients = collect();
        $orders = $attendeeMessage->withChunkedRecipients(1, function ($recipient) use ($allRecipients) {
            $allRecipients->add($recipient);
        });

        $allRecipients->each(function ($chunk) {
            $this->assertTrue($chunk->count() == 1);
        });
    }
}
