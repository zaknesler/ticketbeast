<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
