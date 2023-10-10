<?php

namespace Fatihozpolat\Netgsm\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fatihozpolat\Netgsm\Netgsm
 */
class Netgsm extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fatihozpolat\Netgsm\Netgsm::class;
    }
}
