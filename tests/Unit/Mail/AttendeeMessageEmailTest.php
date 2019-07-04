<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\User;
use App\Models\Concert;
use App\Models\AttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeMessageEmailTest extends TestCase
{
    /** @test */
    function attendee_message_email_contains_the_subject_and_body()
    {
        $attendeeMessage = factory(AttendeeMessage::class)->make([
            'concert_id' => factory(Concert::class)->make([
                'user_id' => factory(User::class)->make(),
            ]),
            'subject' => 'Test subject',
            'body' => 'Test body',
        ]);

        $email = new AttendeeMessageEmail($attendeeMessage);

        $this->assertEquals('Test subject', $email->build()->subject);
        $this->assertStringContainsString('Test body', $email->render());
    }

    /** @test */
    function attendee_message_email_renders_custom_markdown()
    {
        $attendeeMessage = factory(AttendeeMessage::class)->make([
            'concert_id' => factory(Concert::class)->make([
                'user_id' => factory(User::class)->make(),
            ]),
            'subject' => 'Test subject',
            'body' => '# This should be an h1',
        ]);

        $email = new AttendeeMessageEmail($attendeeMessage);

        $this->assertStringContainsString('This should be an h1</h1>', $email->render());
    }
}
