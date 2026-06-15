<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'products', key: 'product_id', timestamps: true)]
#[Fillable(['product_name', 'category_id', 'rack_id', 'sell_price', 'buy_price', 'stock', 'min_stock', 'is_active'])]
class Product extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sell_price' => 'decimal:2',
            'buy_price' => 'decimal:2',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get the rack that holds the product.
     */
    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class, 'rack_id', 'rack_id');
    }

    /**
     * Get the transaction items for the product.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'product_id', 'product_id');
    }

    /**
     * Get the stock alerts for the product.
     */
    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class, 'product_id', 'product_id');
    }

    /**
     * Get the price history for the product.
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'product_id', 'product_id');
    }
}
