<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $confession) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $senderName = $this->confession->sender_id ? $this->confession->sender->name : 'Someone';
        
        return [
            'message_id' => $this->confession->id,
            'sender_id' => $this->confession->sender_id,
            'sender_name' => $senderName,
            'message' => "{$senderName} sent you a private message (confession).",
        ];
    }
}
