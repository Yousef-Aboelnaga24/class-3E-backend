<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class PostService
{
    public function create(User $user, array $data): Post
    {
        $post = $user->posts()->create([
            'content' => $data['content'],
        ]);

        if (!empty($data['media'])) {
            foreach ($data['media'] as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $path = $file->store('posts/media', 'public');
                $type = str_contains((string) $file->getMimeType(), 'video') ? 'video' : 'image';

                $post->media()->create([
                    'file_path' => $path,
                    'type' => $type,
                ]);
            }
        }

        return $post->load(['user', 'media']);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update([
            'content' => $data['content'],
        ]);

        return $post->load(['user', 'media']);
    }
}
