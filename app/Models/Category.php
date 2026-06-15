<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'categories', key: 'category_id', timestamps: false)]
#[Fillable(['category_name', 'description'])]
class Category extends Model
{
    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Get the racks for the category.
     */
    public function racks(): HasMany
    {
        return $this->hasMany(Rack::class, 'category_id', 'category_id');
    }
}
