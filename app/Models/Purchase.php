<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'item_name', 
        'quantity', 
        'unit',
        'price_per_unit', 
        'total_amount', 
        'supplier_name', 
        'purchase_date', 
        'notes'
    ];

    /**
     * Relationship: The supplier mapped to this transaction entry
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}