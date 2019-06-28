<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PromoterLoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    // function user_can_log_in_successfully()
    // {
    //     $user = factory(User::class)->create([
    //         'email' => 'john@example.com',
    //         'password' => Hash::make('secret-password'),
    //     ]);

    //     $this->browse(function (Browser $browser) {
    //         $browser->visitRoute('login')
    //                 ->type('email', 'john@example.com')
    //                 ->type('password', 'secret-password')
    //                 ->press('Sign in')
    //                 ->assertRouteIs('backstage.concerts.index');
    //     });
    // }

    /** @test */
    // function user_cannot_log_in_with_invalid_credentials()
    // {
    //     $user = factory(User::class)->create([
    //         'email' => 'jane@example.com',
    //         'password' => Hash::make('secret-password'),
    //     ]);

    //     $this->browse(function (Browser $browser) {
    //         $browser->visitRoute('login')
    //                 ->type('email', 'jane@example.com')
    //                 ->type('password', 'wrong-password')
    //                 ->press('Sign in')
    //                 ->assertRouteIs('login')
    //                 ->assertSee('credentials do not match');
    //     });
    // }
}
