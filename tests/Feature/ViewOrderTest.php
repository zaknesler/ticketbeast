<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Concert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $concert = factory(Concert::class)->create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('March 12, 2017 8:00pm'),
            'ticket_price' => 4250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
        ]);
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ord_1234',
            'card_last_four' => 'card_7890',
            'amount' => '1234',
            'created_at' => Carbon::parse('June 24, 2019 5:30pm'),
        ]);
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'tik_1234',
        ]);
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'tik_5678',
        ]);

        $response = $this->get('/orders/ord_1234');

        $response->assertStatus(200);
        $response->assertViewHas('order', $order);
        $response->assertSee('ord_1234');
        $response->assertSee('$12.34');
        $response->assertSee('card_7890');
        $response->assertSee('tik_1234');
        $response->assertSee('tik_5678');

        $response->assertSee('2019-06-24 17:30');
        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity and Lethargy');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('john@example.com');
        $response->assertSee('2017-03-12 20:00');
    }
}
