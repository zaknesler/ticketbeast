<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Ticket;
use App\Models\Concert;
use App\Reservations\Reservation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function calculating_the_total_cost_of_a_reservation()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    function retrieving_the_customers_email_from_a_reservation()
    {
        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }

    /** @test */
    function reserved_tickets_are_released_when_cancelled()
    {
        $tickets = collect([
            $this->spy(Ticket::class),
            $this->spy(Ticket::class),
            $this->spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');
        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release')->once();
        }
    }
}
