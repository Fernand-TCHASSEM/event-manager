<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\EventInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class EventRepository extends BaseRepository implements EventInterface
{

    /**      
     * @var Model      
     */
    protected $model;

    /**      
     * BaseRepository constructor.      
     *      
     * @param Event $model      
     */
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function filterEvents(string $fromDate, string $toDate, string $keywords = null): Collection
    {
        $events = $this->model
            ->dateRange($fromDate, $toDate)
            ->keywords($keywords)
            ->orderBy('start_date', 'desc')
            ->get();

        return $events;
    }

    public function checkSlotAvailability(string $fromDate, string $toDate, int $eventId = null): bool
    {
        $eventsNumber = $this->model
            ->dateRange($fromDate, $toDate)
            ->when($eventId, function ($query, $eventId) {
                return $query->where('id', '<>', $eventId);
            })
            ->count();

        return !$eventsNumber ? true : false;
    }
}
