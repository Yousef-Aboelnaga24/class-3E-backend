<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'place',
        'icon_type',
        'color',
        'date_label',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}
