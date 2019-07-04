<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Generators\TicketCodeGenerator;
use Illuminate\Support\ServiceProvider;
use App\Providers\TelescopeServiceProvider;
use App\Billing\Stripe\StripePaymentGateway;
use App\Generators\ConfirmationNumberGenerator;
use App\Generators\Implementations\HashidsTicketCodeGenerator;
use App\Generators\Implementations\RandomConfirmationNumberGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        PaymentGateway::class => StripePaymentGateway::class,
        ConfirmationNumberGenerator::class => RandomConfirmationNumberGenerator::class,
        TicketCodeGenerator::class => HashidsTicketCodeGenerator::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(HashidsTicketCodeGenerator::class, function () {
            return new HashidsTicketCodeGenerator(config('ticketbeast.generators.tickets.salt'));
        });
    }
}
