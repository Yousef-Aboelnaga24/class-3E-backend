<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $user && $user->isAdmin() ? $this->email : null,
            'role' => $this->role,
            'class_code_id' => $this->class_code_id,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'bio' => $this->bio,
            'cover_photo' => $this->cover_photo ? asset('storage/' . $this->cover_photo) : null,
            'last_seen' => $this->last_seen,
            'is_verified' => $this->hasVerifiedEmail(),
            'posts_count' => $this->whenCounted('posts'),
            'comments_count' => $this->whenCounted('comments'),
            'photos_count' => $this->when(isset($this->photos_count), $this->photos_count),
        ];
    }
}
