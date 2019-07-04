<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Generators\TicketCodeGenerator;

class TicketCode extends Facade
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
        return TicketCodeGenerator::class;
    }

    /**
     * Get the mockable class for the bound instance.
     *
     * @return string|null
     */
    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}
