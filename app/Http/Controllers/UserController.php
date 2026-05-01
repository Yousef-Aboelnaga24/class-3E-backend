<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $authUser = request()->user();

        $query = User::withCount('posts')->latest();

        if (!$authUser->isAdmin()) {
            $query->where('role', 'student');
        }

        return response()->json(UserResource::collection($query->get()));
    }

    public function show(string $id): JsonResponse
    {
        $user = User::withCount(['posts', 'comments'])
            ->withCount([
                'posts as photos_count' => function ($query) {
                    $query->join('media', 'posts.id', '=', 'media.post_id');
                },
            ])
            ->findOrFail($id);

        return response()->json(new UserResource($user));
    }

    public function posts(string $id): JsonResponse
    {
        User::findOrFail($id);

        $posts = Post::with(['user', 'media'])
            ->withCount(['reactions', 'comments'])
            ->where('user_id', $id)
            ->latest()
            ->paginate(12);

        return response()->json(PostResource::collection($posts));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            abort(403, 'You cannot edit this profile');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function updateRole(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:admin,student,user',
        ]);

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Role updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
