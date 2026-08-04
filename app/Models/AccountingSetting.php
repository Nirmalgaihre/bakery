<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'data_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get setting value with type casting.
     */
    public function getTypedValue()
    {
        return match ($this->data_type) {
            'Integer' => (int) $this->value,
            'Boolean' => $this->value === '1' || $this->value === 'true',
            'JSON' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
