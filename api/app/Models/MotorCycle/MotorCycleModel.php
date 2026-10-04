<?php

namespace App\Models\MotorCycle;

use App\Models\Client\ClientModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $client_id
 * @property string $plate
 * @property string|null $renavam
 * @property string|null $chassis
 * @property string $brand
 * @property string $model
 * @property int|null $year
 * @property string|null $color
 * @property string|null $engine_number
 * @property Carbon|null $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read ClientModel $client
 */
class MotorCycleModel extends Model {
    use SoftDeletes;

    protected $table = 'clients_motorcycles';

    protected $fillable = [
        'client_id',
        'plate',
        'renavam',
        'chassis',
        'brand',
        'model',
        'year',
        'color',
        'engine_number',
    ];

    public function client(): BelongsTo {
        return $this->belongsTo(ClientModel::class);
    }
}
