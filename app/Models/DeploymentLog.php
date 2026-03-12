<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeploymentLog extends Model
{
    protected $primaryKey = 'log_id';
    
    // Karena di migration kamu menggunakan 'timestamp' manual, matikan auto-timestamps Laravel
    public $timestamps = false;

    protected $fillable = [
        'project_id', 'process', 'status', 'message', 'timestamp'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}
