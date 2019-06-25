<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Orders\RandomConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RandomConfirmationNumberTest extends TestCase
{
    /** @test */
    function random_string_must_be_24_characters_long()
    {
        $generator = new RandomConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    function random_string_can_only_contain_uppercase_letters_and_numbers()
    {
        $generator = new RandomConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    function random_string_cannot_contain_ambiguous_characters()
    {
        $generator = new RandomConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[^10IO]+$/', $confirmationNumber);
    }

    /** @test */
    function random_string_must_be_unique()
    {
        $generator = new RandomConfirmationNumberGenerator;

        $confirmationNumbers = array_map(function ($i) use ($generator) {
            return $generator->generate();
        }, range(1, 1000));

        $this->assertCount(1000, array_unique($confirmationNumbers));
    }
}
