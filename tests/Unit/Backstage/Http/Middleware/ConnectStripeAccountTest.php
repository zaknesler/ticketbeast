<?php

namespace Tests\Unit\Backstage\Http\Middleware;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Middleware\ConnectStripeAccount;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConnectStripeAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_without_a_stripe_account_attached_are_forced_to_connect_with_stripe()
    {
        $user = factory(User::class)->create([
            'stripe_account_id' => null,
        ]);

        $this->be($user);

        $middleware = new ConnectStripeAccount;

        $response = new TestResponse($middleware->handle(new Request, function ($request) {
            $this->fail('Next middleware was called when it should not have been.');
        }));

        $response->assertRedirect('backstage/stripe-connect/connect');
    }

    /** @test */
    function user_with_a_stripe_account_attached_can_continue()
    {
        $user = factory(User::class)->create([
            'stripe_account_id' => 'fake_acct_1234',
        ]);

        $this->be($user);

        $request = new Request;
        $next = new class {
            public $called = false;

            public function __invoke($request)
            {
                $this->called = true;
                return $request;
            }
        };
        $middleware = new ConnectStripeAccount;

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
    }

    /** @test */
    function middleware_is_applied_to_all_backstage_routes()
    {
        $routes = [
            'backstage.concerts.index',
            'backstage.concerts.create',
            'backstage.concerts.store',
            'backstage.concerts.edit',
            'backstage.concerts.update',
            'backstage.publishedConcerts.store',
            'backstage.concerts.orders.show',
            'backstage.concerts.messages.create',
            'backstage.concerts.messages.store',
        ];

        foreach($routes as $route) {
            $this->assertContains(
                ConnectStripeAccount::class,
                Route::getRoutes()->getByName($route)->gatherMiddleware()
            );
        }
    }
}
