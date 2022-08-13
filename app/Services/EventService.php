<?php

namespace App\Services;

use App\Repositories\Contracts\EventInterface;
use Carbon\Carbon;

class EventService
{

    protected $eventRepository;

    public function __construct(EventInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEvents($input)
    {
        if (empty($input['start_date']) && empty($input['end_date'])) {

            $now = Carbon::now();

            $startOfWeek = clone($now)->startOfWeek();
            $endOfWeek = clone($now)->endOfWeek();

            $startDate = $startOfWeek->format('Y-m-d H:i:s');
            $endDate = $endOfWeek->format('Y-m-d H:i:s');
        } else {
            $startDate = $input['start_date'];
            $endDate = $input['end_date'];
        }

        $keywords = $input['keywords'] ?? null;

        $events = $this->eventRepository->filterEvents($startDate, $endDate, $keywords);

        return [
            'data' => $events
        ];
    }

    public function getById($id, $input)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            return [
                'data' => $event->toArray()
            ];
        } else {
            abort(422, trans('event.event_not_found'));
        }
    }

    public function create($input)
    {

        $event = $this->eventRepository->create($input);

        return [
            'data' => $event->toArray()
        ];
    }

    public function update($id, $input)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            $this->eventRepository->setModel($event);

            $this->eventRepository->update($input);

            $event = $this->eventRepository->getModel();

            return [
                'data' => $event->toArray()
            ];
        } else {
            abort(422, trans('event.event_not_found'));
        }
    }

    public function delete($id)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            $this->eventRepository->setModel($event);

            $isDeleted = $this->eventRepository->delete();

            return [
                'data' => $event->toArray()
            ];
        } else {
            abort(422, trans('event.event_not_found'));
        }
    }
}
