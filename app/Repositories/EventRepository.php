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
        $events = $this->model->select(DB::raw('id, title AS name, start_date AS start, end_date AS end, color'))
        ->dateRange($fromDate, $toDate)
        ->keywords($keywords)
        ->orderBy('start_date', 'desc')
        ->get();

        return $events;
    }
}
