<?php

namespace App\Notifications;

use App\Models\Reaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PostReacted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reaction $reaction) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reaction_id' => $this->reaction->id,
            'post_id' => $this->reaction->post_id,
            'user_name' => $this->reaction->user->name,
            'type' => $this->reaction->type,
            'message' => "{$this->reaction->user->name} reacted '{$this->reaction->type}' to your post.",
        ];
    }
}
