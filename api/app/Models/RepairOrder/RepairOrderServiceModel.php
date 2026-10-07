<?php

namespace App\Models\RepairOrder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $repair_order_id
 * @property string $name
 * @property string|null $description
 * @property float $quantity
 * @property float $unit_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read RepairOrderModel $repairOrder
 */
class RepairOrderServiceModel extends Model {
    use SoftDeletes;

    protected $table = 'repair_order_service';

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'unit_value',
    ];

    public function repairOrder(): BelongsTo {
        return $this->belongsTo(RepairOrderModel::class);
    }
}
