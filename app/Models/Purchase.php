<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'item_name',
        'quantity',
        'unit',
        'price_per_unit',
        'total_amount',
        'purchase_date',
        'nepali_date',
        'notes',
    ];

    /**
     * Relationship: The supplier mapped to this transaction entry
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}