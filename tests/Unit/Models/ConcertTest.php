<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Concert;
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
    function concert_tickets_can_be_ordered()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);

        $order = $concert->orderTickets('john@example.com', 3);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
    }

    /** @test */
    function concert_can_have_tickets_added()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function tickets_remaining_for_a_concert_does_not_include_ones_on_an_order()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);

        $concert->orderTickets('john@example.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    function trying_to_purchase_more_tickets_than_remaining_throws_an_exception()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $concert->orderTickets('john@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Order succeeded event though there were not enough tickets remaining.');
    }

    /** @test */
    function cannot_order_tickets_that_have_already_been_purchased()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);

        try {
            $concert->orderTickets('john@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Order succeeded evet though there were not enough tickets remaining.');
    }

    /** @test */
    function can_reserve_available_tickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservedTickets = $concert->reserveTickets(2);

        $this->assertCount(2, $reservedTickets);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->orderTickets('jane@example.com', 2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already sold.');
    }

    /** @test */
    function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->reserveTickets(2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already reserved.');
    }
}
