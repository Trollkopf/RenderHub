<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'client_id',
        'descripcion',
        'archivo',
        'estado',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
