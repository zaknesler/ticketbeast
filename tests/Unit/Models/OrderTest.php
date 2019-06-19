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
