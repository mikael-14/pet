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
 * @property int $pet_id
 * @property float $value
 * @property string $type
 * @property Carbon $date
 * @property string|null $local
 * @property int|null $person_id
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Person|null $person
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetHasMeasure extends Model
{
	protected $table = 'pet_has_measures';

	protected $casts = [
		'pet_id' => 'int',
		'value' => 'float',
		'date' => 'date',
		'person_id' => 'int'
	];

	protected $fillable = [
		'pet_id',
		'value',
		'type',
		'date',
		'local',
		'person_id',
		'observation'
	];

	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}
	
	public function calculateVariation(): float
	{
		$variation = 0;
		$getPreviousRecord = PetHasMeasure::where('id', '!=', $this->id)->where('date', '<', $this->date)->where('type', $this->type)->orderBy('date', 'DESC')->first();
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
