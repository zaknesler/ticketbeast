<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use App\Mail\InvitationEmail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationEmailTest extends TestCase
{
    /** @test */
    function invitation_email_contains_a_link_to_the_register_page()
    {
        $invitation = factory(Invitation::class)->make([
            'user_id' => factory(User::class)->make(),
            'email' => 'john@example.com',
            'code' => 'TESTCODE1234',
        ]);

        $email = new InvitationEmail($invitation);

        $this->assertEquals('You have been invited to join Ticketbeast', $email->build()->subject);
        $this->assertStringContainsString(route('invitations.show', 'TESTCODE1234'), $email->render());
    }
}
