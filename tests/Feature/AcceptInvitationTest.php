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

    /** @test */
    function registering_with_a_used_invitation_code()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => factory(User::class)->create(),
            'code' => 'TESTCODE1234',
        ]);
        $this->assertEquals(1, User::count());

        $response = $this->post(route('auth.register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]));

        $response->assertStatus(404);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    function registering_with_an_invitation_code_that_does_not_exist()
    {
        $response = $this->post(route('auth.register', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'invitation_code' => 'TESTCODE1234',
        ]));

        $response->assertStatus(404);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function email_is_required_when_registering_an_account()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => '',
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function email_is_a_valid_email_address_when_registering_an_account()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => 'not-an-email',
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function email_must_be_unique_when_registering_an_account()
    {
        $existingUser = factory(User::class)->create(['email' => 'john@example.com']);
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);
        $this->assertEquals(1, User::count());

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => $existingUser->email,
                'password' => 'secret',
                'password_confirmation' => 'secret',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::count());
    }

    /** @test */
    function password_is_required_when_registering_an_account()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => 'john@example.com',
                'password' => '',
                'password_confirmation' => '',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function password_must_be_at_least_eight_characters_long_when_registering_an_account()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => 'john@example.com',
                'password' => '1234567',
                'password_confirmation' => '1234567',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function password_must_confirmed_when_registering_an_account()
    {
        $invitation = factory(Invitation::class)->create([
            'user_id' => null,
            'code' => 'TESTCODE1234',
        ]);

        $response = $this->from(route('invitations.show', 'TESTCODE1234'))
            ->post(route('auth.register', [
                'email' => 'john@example.com',
                'password' => 'long-and-secure-password',
                'password_confirmation' => '',
                'invitation_code' => 'TESTCODE1234',
            ]));

        $response->assertRedirect(route('invitations.show', 'TESTCODE1234'));
        $response->assertSessionHasErrors('password');
        $this->assertEquals(0, User::count());
    }
}
