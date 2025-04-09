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

    public function admins()
    {
        return $this->belongsToMany(User::class, 'calendar_event_user', 'event_id', 'user_id');
    }

}
