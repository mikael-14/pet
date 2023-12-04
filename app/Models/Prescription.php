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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Prescription
 * 
 * @property int $id
 * @property string $number
 * @property int $pet_id
 * @property int|null $clinic_id
 * @property int $person_id
 * @property Carbon $date
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Clinic|null $clinic
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
		'date' => 'datetime'
	];

	protected $fillable = [
		'number',
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
	// end generation of code
	public function prescription_has_medicines()
	{
		return $this->hasMany(PrescriptionHasMedicine::class);
	}
	protected static function boot()
	{
		parent::boot();

		static::creating(function ($model) {
			// Your custom function for when the model is being created
			$last_number = 1;
			// get last inserted in current month 
			$currentMonth = Carbon::now()->startOfMonth(); // Get the start date of the current month
			$lastInsertedRecord = Prescription::whereMonth('created_at', $currentMonth->month)
				->whereYear('created_at', $currentMonth->year)
				->latest('created_at')
				->first();
			if ($lastInsertedRecord) {
				$last_number = (int)substr($lastInsertedRecord->number, -3);
				$last_number++;
			}
			$last_number = str_pad($last_number, 3, '0', STR_PAD_LEFT);
			// get the current year and month in the format 'YYYYMM'
			$yearMonth = date('Ym');
			$model->number = "$yearMonth-$last_number";
		});
		static::restored(function ($model) {
			PrescriptionHasMedicine::withTrashed()->where(['prescription_id' => $model->id])->restore();
		});
		static::deleted(function ($model) {
			DB::beginTransaction();
			try {
			//$ids = PrescriptionHasMedicine::where(['prescription_id' => $model->id])->get();
			//PetHasMedicine::whereIn(['prescription_id' => $ids])->delete();
			PrescriptionHasMedicine::where(['prescription_id' => $model->id])->delete();
			DB::commit();
		} catch (\Illuminate\Database\QueryException $e) {
			DB::rollback();
			Log::error('Error updating model: ' . $e->getMessage());
			throw $e;
		} catch (\Exception $e) {
			// If an exception occurs, rollback the transaction
			DB::rollback();
			// Log the error or handle it as needed
			// You might also throw the exception to propagate it up the stack
			Log::error('Error updating model: ' . $e->getMessage());
			// Don't forget to throw the exception to stop the creating process
			throw $e;
		}
		});
		static::updating(function ($model) {
			DB::beginTransaction();
			try {
				$originalValues = $model->getOriginal();
				$oldPetValue = $originalValues['pet_id'];
				if ($oldPetValue !== $model->pet_id) {
					$ids = PrescriptionHasMedicine::withTrashed()->where(['prescription_id' => $model->id]);
					PetHasMedicine::withTrashed()->whereIn('prescription_id', $ids)->update(['pet_id' => $model->pet_id]);
				}
				DB::commit();
			} catch (\Illuminate\Database\QueryException $e) {
				DB::rollback();
				Log::error('Error updating model: ' . $e->getMessage());
				throw $e;
			} catch (\Exception $e) {
				// If an exception occurs, rollback the transaction
				DB::rollback();
				// Log the error or handle it as needed
				// You might also throw the exception to propagate it up the stack
				Log::error('Error updating model: ' . $e->getMessage());
				// Don't forget to throw the exception to stop the creating process
				throw $e;
			}
		});
	}
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
	}

	public function getCountMedicinesAttribute()
	{
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
			$counter[$medicine->pivot->status]++;
		}
		return array_filter($counter, fn ($value) =>  $value !== 0);
	}
}
