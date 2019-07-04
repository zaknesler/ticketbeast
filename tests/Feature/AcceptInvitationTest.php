<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_see_the_registration_page_by_viewing_an_unused_invitation()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get(route('invitations.show', 'TESTCODE1234'));

        $response->assertSuccessful();
        $response->assertViewIs('invitations.show');
        $response->assertViewHas('invitation', $invitation);
    }

    /** @test */
    function viewing_a_used_invitation()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->get(route('invitations.show', 'TESTCODE1234'));

        $response->assertStatus(404);
    }

    /** @test */
    function viewing_a_invitation_that_does_not_exist()
    {
        $response = $this->get(route('invitations.show', 'TESTCODE1234'));

        $response->assertStatus(404);
    }
}
