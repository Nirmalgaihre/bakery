<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Define the table name if it is not exactly 'transactions'
    // protected $table = 'transactions';

    // Allow mass assignment for these fields
    protected $fillable = [
    'product_id',     // Link to your products table
    'partner_name',   // Use this for the Supplier or Buyer name
    'transaction_type', // Use 'inward' or 'outward'
    'quantity', 
    'rate', 
    'transaction_date'
];

    // Define the relationship back to the Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function product() {
    return $this->belongsTo(Product::class, 'product_id');
}
}