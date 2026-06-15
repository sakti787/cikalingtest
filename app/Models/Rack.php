<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'racks', key: 'rack_id', timestamps: false)]
#[Fillable(['rack_code', 'category_id', 'capacity', 'description'])]
class Rack extends Model
{
    /**
     * Get the category that owns the rack.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the products for the rack.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'rack_id', 'rack_id');
    }
}
