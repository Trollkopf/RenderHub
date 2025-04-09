<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function calendarEvents()
    {
        return $this->belongsToMany(CalendarEvent::class, 'calendar_event_user', 'user_id', 'event_id');
    }

}
