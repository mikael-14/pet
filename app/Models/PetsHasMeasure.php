<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

/**
 * Class PetsHasMeasure
 * 
 * @property int $id
 * @property int $pets_id
 * @property float $value
 * @property string $type
 * @property Carbon $date
 * @property string|null $local
 * @property int|null $people_id
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Person|null $person
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetsHasMeasure extends Model
{
	protected $table = 'pets_has_measures';

	protected $casts = [
		'pets_id' => 'int',
		'value' => 'float',
		'date' => 'date',
		'people_id' => 'int'
	];

	protected $fillable = [
		'pets_id',
		'value',
		'type',
		'date',
		'local',
		'people_id',
		'observation'
	];

	public function person()
	{
		return $this->belongsTo(Person::class, 'people_id');
	}

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
		return round($variation,3);
	}
	public function getConfigMeasureVariation(): float|null
	{
		$configMeasures = __('pet/measures')  ?? [];
		return $configMeasures[$this->type]['variation'] ?? null;
	}
	public function getConfigMeasureUnit(): string
	{
		$configMeasures = __('pet/measures') ?? [];
		return $configMeasures[$this->type]['unit'] ?? '';
	}
	public function getConfigMeasureName(): string
	{
		$configMeasures = __('pet/measures')  ?? [];
		return $configMeasures[$this->type]['name'] ?? $this->type;
	}
}
