<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Hash;
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

    /** @test */
    function registering_with_a_valid_invitation_code()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->post(route('auth.register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]));

        $response->assertRedirect(route('backstage.concerts.index'));
        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertAuthenticatedAs($user);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertTrue($invitation->fresh()->hasBeenUsed());
        $this->assertTrue($invitation->fresh()->user->is($user));
    }
}
