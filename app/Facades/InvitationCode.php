<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Generators\InvitationCodeGenerator;

class InvitationCode extends Facade
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
        return InvitationCodeGenerator::class;
    }
}
