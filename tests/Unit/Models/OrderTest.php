<?php

namespace Tests\Unit\Models;

use Mockery;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Ticket;
use App\Billing\Charge;
use App\Models\Concert;
use App\Reservations\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function creating_an_order_from_tickets_email_and_charge()
    {
        $tickets = factory(Ticket::class, 3)->create();
        $charge = new Charge([
            'amount' => 3600,
            'card_last_four' => 1234,
        ]);
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals('1234', $order->card_last_four);
        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }

    /** @test */
    function ordering_tickets_yields_proper_results_when_converting_to_an_array()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 6000,
        ]);
        $order->tickets()->saveMany([
            factory(Ticket::class)->create(['code' => 'TICKETCODE1']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE2']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE3']),
        ]);

        $results = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 6000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ],
        ], $results);
    }

    /** @test */
    function retrieving_an_order_by_its_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertTrue($foundOrder->is($order));
    }

    /** @test */
    function retrieving_a_nonexistent_order_by_its_confirmation_number_throws_an_exception()
    {
        $this->expectException(ModelNotFoundException::class);

        Order::findByConfirmationNumber('ord_nonexistent');
    }
}
