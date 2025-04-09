<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'start',
        'recurrence',
        'repeat_count',
        'color',
        'description',
    ];
}
