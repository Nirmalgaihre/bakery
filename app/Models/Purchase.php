<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    // सबै फिल्डहरू क्लासको भित्र (ब्रेसको बीचमा) हुनुपर्छ
    protected $fillable = [
        'item_name', 'quantity', 'price_per_unit', 'total_amount', 
        'supplier_name', 'purchase_date', 'notes', 'unit'
    ];
}