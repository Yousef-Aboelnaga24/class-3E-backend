<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function __construct(private readonly PostService $postService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $posts = Post::with(['user', 'media'])
            ->withCount(['comments', 'reactions'])
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function store(PostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);
        $post = $this->postService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Memory shared successfully!',
            'post' => new PostResource($post),
        ], 201);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post->load(['user', 'media', 'comments.user']));
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Memory deleted.']);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        $updatedPost = $this->postService->update($post, $request->validated());

        return response()->json([
            'message' => 'Memory updated successfully.',
            'post' => new PostResource($updatedPost),
        ]);
    }
}
