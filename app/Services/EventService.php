<?php

namespace App\Services;

use App\Repositories\Contracts\EventInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            $startOfWeek = clone ($now)->startOfMonth();
            $endOfWeek = clone ($now)->endOfMonth();

            $startDate = $startOfWeek->format('Y-m-d H:i:s');
            $endDate = $endOfWeek->format('Y-m-d H:i:s');
        } else {
            $startDate = $input['start_date'];
            $endDate = $input['end_date'];
        }

        $keywords = $input['keywords'] ?? null;

        $events = $this->eventRepository->filterEvents($startDate, $endDate, $keywords);

        return [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'keywords' => $keywords
            ],
            'events' => $events->transform(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->title,
                    'description' => $item->description,
                    'start' => $item->start_date,
                    'end' => $item->end_date,
                    'color' => $item->color,
                    'timed' => true
                ];
            })
        ];
    }

    public function getById($id, $input)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            return [
                'status' => 200,
                'data' => $event->toArray()
            ];
        } else {

            return [
                'code' => 422,
                'message' => trans('event_not_found')
            ];
        }
    }

    public function create($input)
    {

        $isSlotAvailable = $this->eventRepository->checkSlotAvailability($input['start_date'], $input['end_date']);

        if ($isSlotAvailable) {

            $event = $this->eventRepository->create($input);

            return [
                'code' => 200,
                'data' => $event->toArray()
            ];
        } else {

            return [
                'code' => 400,
                'field' => 'slot',
                'message' => trans('slot_not_available')
            ];
        }
    }

    public function update($id, $input)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            $isSlotAvailable = $this->eventRepository->checkSlotAvailability($input['start_date'], $input['end_date'], $id);

            if ($isSlotAvailable) {

                $this->eventRepository->setModel($event);

                $this->eventRepository->update($input);

                $event = $this->eventRepository->getModel();

                return [
                    'code' => 200,
                    'data' => $event->toArray()
                ];
            } else {

                return [
                    'code' => 400,
                    'field' => 'slot',
                    'message' => trans('slot_not_available')
                ];
            }
        } else {
            
            return [
                'code' => 422,
                'message' => trans('event_not_found')
            ];
        }
    }

    public function delete($id)
    {

        $event = $this->eventRepository->findById($id);

        if ($event !== null) {

            $this->eventRepository->setModel($event);

            $isDeleted = $this->eventRepository->delete();

            return [
                'code' => 200,
                'data' => $event->toArray()
            ];
        } else {
            
            return [
                'code' => 422,
                'message' => trans('event_not_found')
            ];
        }
    }
}
