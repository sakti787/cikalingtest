<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'users', key: 'user_id', timestamps: true)]
#[Fillable(['username', 'password_hash', 'role'])]
#[Hidden(['password_hash'])]
class User extends Authenticatable
{
    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    protected $primaryKey = 'user_id';

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'user_id';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    /**
     * Get the transactions for the user (as cashier).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'kasir_id', 'user_id');
    }

    /**
     * Get the price history changed by the user.
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'changed_by', 'user_id');
    }
}
