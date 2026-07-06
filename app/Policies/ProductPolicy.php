<?php
namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        // If you are using Spatie Permissions:
        return $user->hasRole('admin'); 
        
        // OR, if you use a simple 'is_admin' field in your users table:
        // return $user->is_admin === true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product)
    {
        return $user->hasRole('admin');
    }
}