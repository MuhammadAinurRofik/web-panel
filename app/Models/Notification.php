<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'notif_id';

    protected $fillable = [
        'user_id', 'project_id', 'message', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
