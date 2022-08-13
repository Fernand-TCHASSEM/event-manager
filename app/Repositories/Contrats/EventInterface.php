<?php

namespace App\Repositories\Contracts;

use App\Models\Font;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EventInterface extends BaseInterface
{

    /**
     * Return a list of events based on search criteria.
     * 
     * @param string $fromDate
     * @param string $toDate
     * @param string $keywords
     * 
     * @return mixed
     */
    public function filterEvents(string $fromDate, string $toDate, string $keywords = null): Collection;

}