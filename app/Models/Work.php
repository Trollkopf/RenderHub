<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'titulo',
        'descripcion',
        'estado',
        'archivos',
    ];

    protected $casts = [
        'archivos' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
