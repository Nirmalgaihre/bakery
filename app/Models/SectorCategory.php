<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SectorCategory extends Model
{
    protected $table = 'sector_categories';

    protected $fillable = ['id', 'name'];

    // Tells Laravel not to expect an auto-incrementing integer ID
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Hook into the model lifecycle to auto-generate the custom string ID
     */
    protected static function booted(): void
    {
        static::creating(function (SectorCategory $category) {
            if (empty($category->id)) {
                // Generates 'cat-dry-yeast' cleanly from your input name string
                $category->id = 'cat-' . Str::slug($category->name);
            }
        });
    }
}