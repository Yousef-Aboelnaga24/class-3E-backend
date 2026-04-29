<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Reaction;
use App\Notifications\PostReacted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function toggle(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:like,love,haha,sad',
        ]);

        $user = $request->user();
        $reaction = $post->reactions()->where('user_id', $user->id)->first();

        if ($reaction) {
            if ($reaction->type === $request->type) {
                $reaction->delete();
                return response()->json(['message' => 'Reaction removed.', 'action' => 'removed']);
            }
            
            $reaction->update(['type' => $request->type]);
            $action = 'updated';
        } else {
            $reaction = $post->reactions()->create([
                'user_id' => $user->id,
                'type' => $request->type,
            ]);
            $action = 'added';

            if ($post->user_id !== $user->id) {
                $post->user->notify(new PostReacted($reaction));
            }
        }

        return response()->json([
            'message' => "Reaction $action!",
            'action' => $action,
            'type' => $request->type
        ]);
    }
}
