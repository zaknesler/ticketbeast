<?php

namespace Tests\Feature\Backstage;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromoterLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_promoter_can_log_in_with_valid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jane@example.com',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect('/backstage/concerts/new');
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    function logging_in_with_invalid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('secret-password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'jane@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_in_with_an_account_that_does_not_exist()
    {
        $response = $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_out_the_current_user()
    {
        Auth::login(factory(User::class)->create());
        $this->assertTrue(Auth::check());

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }
}
