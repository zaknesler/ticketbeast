<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Generators\ConfirmationNumberGenerator;

class ConfirmationNumber extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return ConfirmationNumberGenerator::class;
    }
}
