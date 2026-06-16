<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['invoice_number', 'invoice_date', 'customer_id', 'grand_total'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}