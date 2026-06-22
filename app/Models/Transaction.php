<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'transactions', key: 'transaction_id', timestamps: true)]
#[Fillable(['kasir_id', 'transaction_date', 'total_amount', 'is_special_price', 'printed_nota', 'pembayaran'])]
class Transaction extends Model
{
    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_special_price' => 'boolean',
            'printed_nota' => 'boolean',
            'total_amount' => 'decimal:2',
            'transaction_date' => 'datetime',
        ];
    }

    /**
     * Get the user (cashier) who processed the transaction.
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id', 'user_id');
    }

    /**
     * Get the items for the transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'transaction_id');
    }
}
