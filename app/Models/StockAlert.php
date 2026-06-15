<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'stock_alerts', key: 'alert_id', timestamps: false)]
#[Fillable(['product_id', 'alert_date', 'current_stock', 'min_stock', 'is_dismissed', 'dismissed_at'])]
class StockAlert extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_dismissed' => 'boolean',
        ];
    }

    /**
     * Get the product associated with the stock alert.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
