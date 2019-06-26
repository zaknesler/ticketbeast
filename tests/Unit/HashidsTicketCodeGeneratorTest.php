<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Ticket;
use App\Tickets\HashidsTicketCodeGenerator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    function ticket_codes_are_at_least_6_characters_long()
    {
        $generator = new HashidsTicketCodeGenerator('salt1');

        $code = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    function ticket_codes_should_only_contain_non_ambiguous_uppercase_letters_or_numbers()
    {
        $generator = new HashidsTicketCodeGenerator('salt1');

        $code = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegExp('/^[A-HJ-NP-Z2-9]+$/', $code);
    }

    /** @test */
    function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $generator = new HashidsTicketCodeGenerator('salt1');

        $codeA = $generator->generateFor(new Ticket(['id' => 1]));
        $codeB = $generator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($codeA, $codeB);
    }

    /** @test */
    function ticket_codes_for_different_ticket_ids_are_different()
    {
        $generator = new HashidsTicketCodeGenerator('salt1');

        $codeA = $generator->generateFor(new Ticket(['id' => 1]));
        $codeB = $generator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($codeA, $codeB);
    }

    /** @test */
    function ticket_codes_generated_with_different_salts_are_different()
    {
        $generatorA = new HashidsTicketCodeGenerator('salt1');
        $generatorB = new HashidsTicketCodeGenerator('salt2');

        $codeA = $generatorA->generateFor(new Ticket(['id' => 1]));
        $codeB = $generatorB->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($codeA, $codeB);
    }
}
