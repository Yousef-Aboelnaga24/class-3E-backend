<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimelineEventResource;
use App\Models\TimelineEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class TimelineEventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $events = TimelineEvent::orderBy('event_date', 'asc')->get();
        return TimelineEventResource::collection($events);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        $event = TimelineEvent::create($validated);

        return response()->json([
            'message' => 'Event added to timeline!',
            'event' => new TimelineEventResource($event),
        ], 201);
    }

    public function update(Request $request, TimelineEvent $timelineEvent): JsonResponse
    {
        $validated = $this->validateEvent($request, partial: true);

        $timelineEvent->update($validated);

        return response()->json([
            'message' => 'Timeline event updated!',
            'event' => new TimelineEventResource($timelineEvent),
        ]);
    }

    public function destroy(TimelineEvent $timelineEvent): JsonResponse
    {
        $timelineEvent->delete();

        return response()->json([
            'message' => 'Timeline event deleted!',
        ]);
    }

    private function validateEvent(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string', 'max:2000'],
            'event_date' => [$required, 'date'],
            'place' => ['nullable', 'string', 'max:255'],
            'icon_type' => ['nullable', Rule::in(['star', 'award', 'book', 'heart'])],
            'color' => ['nullable', Rule::in(['amber', 'red', 'blue', 'green'])],
            'date_label' => ['nullable', 'string', 'max:80'],
        ]);
    }
}
