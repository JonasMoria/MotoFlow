<?php

namespace App\Models\RepairOrder;

use App\Models\MotorCycle\MotorCycleModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $motorcycle_id
 * @property int $status
 * @property int $urgency_level
 * @property string $title
 * @property string $description
 * @property string|null $diagnosis
 * @property string|null $observations
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $delivered_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read MotorCycleModel $motorcycle
 * @property-read Collection<int, RepairOrderPartModel> $repairOrderParts
 * @property-read Collection<int, RepairOrderServiceModel> $repairOrderServices
 */
class RepairOrderModel extends Model {
    use SoftDeletes;

    protected $table = 'repair_order';

    protected $fillable = [
        'motorcycle_id',
        'status',
        'urgency_level',
        'title',
        'description',
        'diagnosis',
        'observations',
        'started_at',
        'finished_at',
        'delivered_at',
    ];

    public function motorcycle(): BelongsTo {
        return $this->belongsTo(MotorCycleModel::class);
    }

    public function repairOrderParts(): HasMany {
        return $this->hasMany(RepairOrderPartModel::class);
    }

    public function repairOrderServices(): HasMany {
        return $this->hasMany(RepairOrderServiceModel::class);
    }
}
