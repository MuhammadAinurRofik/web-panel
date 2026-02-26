<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    // Tentukan nama tabel jika tidak otomatis jamak (opsional)
    protected $table = 'projects';

    // WAJIB: Kolom yang boleh diisi secara massal (Mass Assignment)
    protected $fillable = [
        'user_id',
        'author_name',
        'project_name',
        'project_type',
        'entry_point',
        'flask_instance',      
        'db_config_file',   
        'need_db',          
        'file_path',
        'sql_path',
        'extract_path', 
        'subdomain', 
        'db_name', 
        'db_user', 
        'db_password', 
        'status',
    ];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi: Satu project punya banyak log deployment
    public function logs()
    {
        return $this->hasMany(DeploymentLog::class, 'project_id', 'project_id');
    }
}
