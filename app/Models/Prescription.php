<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Prescription
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $clinic_id
 * @property int $person_id
 * @property Carbon $date
 * @property string $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Clinic $clinic
 * @property Person $person
 * @property Pet $pet
 * @property Collection|Medicine[] $medicines
 *
 * @package App\Models
 */
class Prescription extends Model implements HasMedia
{
	use SoftDeletes;
	use InteractsWithMedia;

	protected $table = 'prescriptions';

	protected $casts = [
		'pet_id' => 'int',
		'clinic_id' => 'int',
		'person_id' => 'int',
		'date' => 'date'
	];

	protected $fillable = [
		'pet_id',
		'clinic_id',
		'person_id',
		'date',
		'observation'
	];

	public function clinic()
	{
		return $this->belongsTo(Clinic::class);
	}

	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}

	public function medicines()
	{
		return $this->belongsToMany(Medicine::class, 'prescription_has_medicines', 'prescription_id', 'medicine_id')
			->withPivot('id', 'dosage', 'status', 'frequency', 'emergency', 'start_date', 'end_date', 'observation', 'deleted_at')
			->withTimestamps();
	}
	public function prescription_has_medicines()
	{
		return $this->hasMany(PrescriptionHasMedicine::class);
	}
	//custom attribute to get is $model->medicine_start_date
	public function getMedicineStartDateAttribute()
	{
		return $this->prescription_has_medicines()?->orderBy('start_date', 'asc')->first()->start_date ?? null;
	}
	public function getMedicineEndDateAttribute()
	{
		if ($this->medicines()->whereNull('end_date')->exists()) {
			return null;
		}
		return $this->prescription_has_medicines()?->orderBy('end_date', 'desc')->first()->end_date ?? null;
		// if($end_date) {
		// 	$start_date = $this->getMedicineStartDateAttribute();
		// 	if($start_date->greaterThan($end_date)) {
		// 		return null;
		// 	}
		// }
	}
	public function getCountMedicinesAttribute() {
		return count($this->medicines()->get());
	}
	public function getGlobalStateAttribute(): array
	{
		$status = array_merge(__('pet/prescriptionmedicines.additional_status'), __('pet/prescriptionmedicines.status'));
		$counter = array_fill_keys(array_keys($status), 0);
		// Get the current date using Carbon
		$currentDate = Carbon::now();
		$medicines = $this->medicines()->get();
		foreach ($medicines as $medicine) {
			if ($currentDate->lessThan($medicine->pivot->start_date)) {
				$counter['unstarted']++;
				continue;
			}
			if (isset($medicine->pivot->end_date) && $currentDate->greaterThan($medicine->pivot->end_date)) {
				$counter['ended']++;
				continue;
			}
			$counter[$medicine->pivot->status]++;
		}
		$counter = array_filter($counter, fn ($value) =>  $value !== 0);
		// array_walk($counter, function (&$value, $key) use ($status) {
		// 	if ($value == 1)
		// 		$value = $status[$key] ?? $key;
		// 	else
		// 		$value =  ($status[$key] ?? $key) . ' + ' . $value;
		// });
		return $counter;
	}
}
