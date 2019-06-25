<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Concert;
use App\Reservations\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function creating_an_order_from_tickets_email_and_amount()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3000);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3000, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    function ordering_tickets_yields_proper_results_when_converting_to_an_array()
    {
        // $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 1200])->addTickets(5);
        // $order = $concert->orderTickets('john@example.com', 5);

        $order = factory(Order::class)->create([
            'confirmation_number' => 'ord_1234',
            'email' => 'john@example.com',
            'amount' => 6000,
        ]);
        $order->tickets()->saveMany(factory(Ticket::class, 5)->create());

        $results = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'ord_1234',
            'email' => 'john@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $results);
    }

    /** @test */
    function retreiving_an_order_by_its_confirmation_number()
    {
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ord_1234',
        ]);

        $foundOrder = Order::findByConfirmationNumber('ord_1234');

        $this->assertTrue($foundOrder->is($order));
    }

    /**
     * @test
     * @doesNotPerformAssertions
    */
    function retrieving_a_nonexistent_order_by_its_confirmation_number_throws_an_exception()
    {
        try {
            Order::findByConfirmationNumber('ord_nonexistent');
        } catch (ModelNotFoundException $e) {
            return;
        }

        $this->fail('No exception was thrown when accessing a nonexistent confirmation number.');
    }
}
