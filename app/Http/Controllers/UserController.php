<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // 👀 GET /api/users
    public function index(): JsonResponse
    {
        abort_unless(request()->user()?->isAdmin(), 403, 'Unauthorized for this action.');

        return response()->json(
            User::select('id', 'name', 'email', 'avatar', 'role', 'class_code_id', 'created_at')->get()
        );
    }

    // 👀 GET /api/users/{id} (Public profile)
    public function show(string $id): JsonResponse
    {
        $user = User::select('id', 'name', 'avatar', 'bio', 'role', 'location', 'created_at')
            ->findOrFail($id);

        return response()->json($user);
    }

    // 👀 GET /api/users/{id}/posts (Public posts)
    public function posts(string $id): JsonResponse
    {
        $posts = Post::where('user_id', $id)
            ->with('user:id,name,avatar')
            ->latest()
            ->get();

        return response()->json($posts);
    }

    // ✏️ UPDATE profile (owner OR admin only)
    public function update(Request $request, string $id): JsonResponse
    {
        $authUser = $request->user();
        $user = User::findOrFail($id);

        // 🔒 Permission check
        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            abort(403, 'You cannot edit this profile');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:admin,student,user'
        ]);

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Role updated successfully',
            'user' => $user
        ]);
    }

    // 🗑️ DELETE user (admin only)
    public function destroy(Request $request, string $id): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
