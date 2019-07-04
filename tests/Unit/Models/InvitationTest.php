<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use App\Mail\InvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_invitation_can_be_retreived_by_its_code()
    {
        $invitation = factory(Invitation::class)->create([
            'code' => 'TESTCODE1234',
        ]);

        $foundInvitation = Invitation::findByCode('TESTCODE1234');

        $this->assertTrue($foundInvitation->is($invitation));
    }

    /** @test */
    function an_invitation_throws_a_model_not_found_exception_if_a_code_cannot_be_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $foundInvitation = Invitation::findByCode('TESTCODE1234');
    }

    /** @test */
    function an_invitation_can_describe_whether_or_not_it_has_been_used()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'code' => 'TESTCODE1234',
        ]);

        $this->assertTrue($invitation->hasBeenUsed());
    }

    /** @test */
    function an_invitation_can_be_sent_to_the_associated_email()
    {
        Mail::fake();

        $invitation = factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'email' => 'john@example.com',
            'code' => 'TESTCODE1234',
        ]);

        $invitation->send();

        Mail::assertSent(InvitationEmail::class, function ($mail) use ($invitation) {
            return $mail->hasTo('john@example.com')
                && $mail->invitation->is($invitation);
        });
    }
}
