<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupImport extends Model
{
    protected $fillable = [
        'filename',
        'original_name',
        'path',
        'status',
        'uploaded_by',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}