<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'attendances', key: 'attendance_id', timestamps: false)]
#[Fillable(['user_id', 'login_at'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
        ];
    }

    /**
     * Get the user who logged in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
