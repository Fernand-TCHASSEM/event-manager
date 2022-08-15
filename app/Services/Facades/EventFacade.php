<?php
namespace App\Services\Facades;

use App\Services\EventService;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for user service
 */
class EventFacade extends Facade
{

    /**
     * Returning service name
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return EventService::class;
    }
}