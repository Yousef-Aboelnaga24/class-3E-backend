<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimelineEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'place' => $this->place,
            'event_date' => $this->event_date->format('Y-m-d'),
            'icon_type' => $this->icon_type ?? 'star',
            'color' => $this->color ?? 'amber',
            'date_label' => $this->date_label ?? $this->event_date->format('M Y'),
        ];
    }
}
