<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Concert;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    function ordering_tickets_yields_proper_results()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 1200])->addTickets(5);
        $order = $concert->orderTickets('alex@example.com', 5);

        $results = $order->toArray();

        $this->assertEquals([
            'email' => 'alex@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $results);
    }

    /** @test */
    function tickets_are_released_when_an_order_is_canceled()
    {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(10);
        $order = $concert->orderTickets('alex@example.com', 5);
        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}
