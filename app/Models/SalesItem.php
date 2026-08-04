<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_name',
        'item_code',
        'description',
        'unit',
        'opening_price',
        'current_price',
        'quantity_in_hand',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opening_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'quantity_in_hand' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function invoiceItems()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'item_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getTotalValue()
    {
        return (float) $this->quantity_in_hand * (float) $this->current_price;
    }
}
