<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTwoFactorCode extends Model {
    protected $fillable = [
        'user_id',
        'code_hash',
        'expires_at',
        'attempts',
        'used_at',
    ];

    protected function casts(): array {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
