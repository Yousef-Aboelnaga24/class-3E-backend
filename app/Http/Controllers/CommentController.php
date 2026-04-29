<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\Comment;
use App\Notifications\PostCommented;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments()->with('user')->latest()->paginate(20);
        return CommentResource::collection($comments);
    }

    public function store(Request $request, Post $post): JsonResponse
    {
        $request->validate(['content' => 'required|string']);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        if ($post->user_id !== $request->user()->id) {
            $post->user->notify(new PostCommented($comment));
        }

        return response()->json([
            'message' => 'Comment added!',
            'comment' => new CommentResource($comment->load('user')),
        ], 201);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(['message' => 'Comment deleted.']);
    }
}
