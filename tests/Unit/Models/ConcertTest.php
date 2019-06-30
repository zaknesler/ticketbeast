<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Concert;
use App\Database\Helpers\ConcertHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Billing\Exceptions\NotEnoughTicketsException;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_concert_can_format_the_date_properly()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    function a_concert_can_format_the_time_properly()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    function a_concert_can_format_its_price_properly()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_truly_published()
    {
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);
        $unpublishedConcert = factory(Concert::class)->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    function concert_can_be_published()
    {
        $concert = factory(Concert::class)->create([
            'published_at' => null,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $concert->publish();

        $this->assertTrue($concert->isPublished());
        $this->assertEquals(5, $concert->ticketsRemaining());
    }

    /** @test */
    function a_concerts_date_is_not_accidentally_updated_when_published()
    {
        $concert = factory(Concert::class)->create([
            'published_at' => null,
            'date' => Carbon::parse('June 7, 2019 7:30pm'),
        ]);
        $this->assertFalse($concert->isPublished());

        $concert->publish();

        $this->assertEquals('June 7, 2019', $concert->formatted_date);
        $this->assertEquals('7:30pm', $concert->formatted_start_time);
        $this->assertEquals('2019-06-07 19:30:00', $concert->fresh()->date);
        $this->assertTrue($concert->isPublished());
    }

    /** @test */
    function tickets_remaining_for_a_concert_does_not_include_ones_on_an_order()
    {
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 30)->create([
            'order_id' => factory(Order::class)->create()->id,
        ]));
        $concert->tickets()->saveMany(factory(Ticket::class, 20)->create());

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    function trying_to_reserve_more_tickets_than_remain_throws_an_exception()
    {
        $concert = ConcertHelper::createPublished(['ticket_quantity' => 10]);

        try {
            $concert->reserveTickets(11, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Order succeeded event though there were not enough tickets remaining.');
    }

    /** @test */
    function can_reserve_available_tickets()
    {
        $concert = ConcertHelper::createPublished(['ticket_quantity' => 3]);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = ConcertHelper::createPublished(['ticket_quantity' => 3]);
        $order = factory(Order::class)->create();

        $order->tickets()->saveMany($concert->tickets->take(2));

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already sold.');
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        $concert = ConcertHelper::createPublished(['ticket_quantity' => 3]);
        $concert->reserveTickets(2, 'jane@example.com');

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already reserved.');
    }
}
