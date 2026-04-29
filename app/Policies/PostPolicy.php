<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent() && $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent() && $user->id === $post->user_id;
    }
}
