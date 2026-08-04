<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_name',
        'file_name',
        'import_type',
        'total_records',
        'successfully_imported',
        'failed_records',
        'error_logs',
        'status',
        'import_date',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'error_logs' => 'json',
        'import_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
