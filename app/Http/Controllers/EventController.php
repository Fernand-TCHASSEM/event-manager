<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Redirect;
use App\Services\Facades\EventFacade as Event;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EventRequest $request)
    {

        $input = $request->validated();

        $eventData = Event::getEvents($input);

        return Inertia::render('Event/EventCalendar', $eventData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $input = $request->validated();

        Event::create($input);

        return Redirect::back()->with('success', trans('event_created'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {
        $input = $request->validated();

        $eventData = Event::update($id, $input);

        return Redirect::back()->with('success', trans('event_updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::delete($id);

        return Redirect::back()->with('success', trans('event_deleted'));
    }
}
