<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model {
    protected $fillable = ['sale_id', 'product_id', 'rate_per_kg', 'quantity_kg', 'quantity_gm', 'total'];
}