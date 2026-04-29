<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimelineEventResource;
use App\Models\TimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TimelineEventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = TimelineEvent::orderBy('event_date', 'asc')->get();
        return TimelineEventResource::collection($events);
    }

    public function store(Request $request)
    {
        // Admin only feature potentially, but implementing for completeness
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'event_date' => 'required|date',
        ]);

        $event = TimelineEvent::create($request->all());

        return response()->json([
            'message' => 'Event added to timeline!',
            'event' => new TimelineEventResource($event),
        ], 201);
    }
}
