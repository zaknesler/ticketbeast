<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Tickets\TicketCodeGenerator;
use Illuminate\Support\ServiceProvider;
use App\Orders\ConfirmationNumberGenerator;
use App\Providers\TelescopeServiceProvider;
use App\Tickets\HashidsTicketCodeGenerator;
use App\Billing\Stripe\StripePaymentGateway;
use App\Orders\RandomConfirmationNumberGenerator;

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
        if ($this->app->isLocal() && !$this->app->environment('testing')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
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
