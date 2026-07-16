<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address'];

    // Define the relationship: A supplier provides many inventory items
    public function inventoryMovements() {
        return $this->hasMany(InventoryMovement::class);
    }
}