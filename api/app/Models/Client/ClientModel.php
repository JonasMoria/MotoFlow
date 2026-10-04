<?php

namespace App\Models\Client;

use App\Models\MotorCycle\MotorCycleModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $document
 * @property string|null $address
 * @property string|null $avatar_path
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 */
class ClientModel extends Model {
    use SoftDeletes;

    protected $table = 'users_clients';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'document',
        'address',
        'avatar_path',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function motorcycles(): HasMany {
        return $this->hasMany(MotorCycleModel::class);
    }
}
