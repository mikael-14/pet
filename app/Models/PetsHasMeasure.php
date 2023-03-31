<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

/**
 * Class PetHasMeasure
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
	public function calculateVariation(): float
	{
		$variation = 0;
		$getPreviousRecord = PetsHasMeasure::where('id', '!=', $this->id)->where('date', '<', $this->date)->where('type', $this->type)->orderBy('date', 'DESC')->first();
		if ($getPreviousRecord) {
			$variation = $this->value - $getPreviousRecord->value;
		}
		return $variation;
	}
	public function getConfigMeasureVariation(): float|null
	{
		$configMeasures = config('pet-measures', []);
		return $configMeasures[$this->type]['variation'] ?? null;
	}
	public function getConfigMeasureUnit(): string
	{
		$configMeasures = config('pet-measures', []);
		return $configMeasures[$this->type]['unit'] ?? '';
	}
	public function getConfigMeasureName(): string
	{
		$configMeasures = config('pet-measures', []);
		return $configMeasures[$this->type]['name'] ?? $this->type;
	}
}
