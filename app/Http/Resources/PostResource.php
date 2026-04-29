<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
            'reactions_count' => $this->reactions_count ?? $this->reactions()->count(),
            'comments_count' => $this->comments_count ?? $this->comments()->count(),
            'my_reaction' => $this->when(auth('sanctum')->check(), function () {
                $reaction = $this->reactions()->where('user_id', auth('sanctum')->id())->first();
                return $reaction ? $reaction->type : null;
            }),
        ];
    }
}
