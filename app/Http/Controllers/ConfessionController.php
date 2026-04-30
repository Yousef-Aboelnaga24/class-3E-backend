<?php

namespace App\Http\Controllers;

use App\Models\Confession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Js;
use Psy\Util\Json;

class ConfessionController extends Controller
{
    public function index(): JsonResponse
    {
        $confessions = Confession::with('user')
            ->latest()
            ->get()
            ->map(function ($confession) {
                return [
                    'id' => $confession->id,
                    'content' => $confession->content,
                    'is_anonymous' => $confession->is_anonymous,
                    'author' => $confession->is_anonymous ? null : $confession->user->name,
                    'timestamp' => $confession->created_at->diffForHumans(),
                    'color' => 'bg-confession-gradient',
                ];
            });

         return response()->json(['data' => $confessions]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10',
            'is_anonymous' => 'required|boolean',
        ]);

        $confession = Confession::create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'is_anonymous' => $validated['is_anonymous'],
        ]);

        return response()->json([
            'message' => 'Your secret is safe with us! 🤫',
            'confession' => [
                'id' => $confession->id,
                'content' => $confession->content,
                'is_anonymous' => $confession->is_anonymous,
                'author' => $confession->is_anonymous ? null : $confession->user->name,
                'timestamp' => 'Just now',
                'color' => 'bg-confession-gradient',
            ],
        ], 201);
    }
}
