<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent ledger group.
     */
    public function parent()
    {
        return $this->belongsTo(LedgerGroup::class, 'parent_id');
    }

    /**
     * Get child ledger groups.
     */
    public function children()
    {
        return $this->hasMany(LedgerGroup::class, 'parent_id');
    }

    /**
     * Get all ledgers in this group.
     */
    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }

    /**
     * Get the user who created this record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get only active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope to get only inactive groups.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }
}
