<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Confession extends Model
{
    protected $fillable = ['user_id', 'content', 'is_anonymous'];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
