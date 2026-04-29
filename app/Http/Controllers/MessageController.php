<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    public function received(Request $request): AnonymousResourceCollection
    {
        $messages = $request->user()->receivedMessages()->with('sender')->latest()->paginate(20);
        return MessageResource::collection($messages);
    }

    public function sent(Request $request): AnonymousResourceCollection
    {
        $messages = $request->user()->sentMessages()->with('receiver')->latest()->paginate(20);
        return MessageResource::collection($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'is_anonymous' => 'boolean'
        ]);

        $message = Message::create([
            'sender_id' => $request->is_anonymous ? null : $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        $receiver = User::find($request->receiver_id);
        $receiver->notify(new MessageReceived($message));

        return response()->json([
            'message' => 'Confession sent successfully!',
            'data' => new MessageResource($message),
        ], 201);
    }
}
