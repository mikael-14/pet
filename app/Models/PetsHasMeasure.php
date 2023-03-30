<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetsHasMeasure
 * 
 * @property int $id
 * @property int $pets_id
 * @property float $value
 * @property string $type
 * @property Carbon $date
 * @property string|null $local
 * @property string|null $application
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetsHasMeasure extends Model
{
	protected $table = 'pets_has_measures';

	protected $casts = [
		'pets_id' => 'int',
		'value' => 'float'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'pets_id',
		'value',
		'type',
		'date',
		'local',
		'application',
		'observation'
	];

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}
}
