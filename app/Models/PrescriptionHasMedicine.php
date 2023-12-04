<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PrescriptionHasMedicine
 * 
 * @property int $id
 * @property int $prescription_id
 * @property int $medicine_id
 * @property string $dosage
 * @property string $status
 * @property int $frequency
 * @property bool $emergency
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Medicine $medicine
 * @property Prescription $prescription
 * @property Collection|PetHasMedicine[] $pet_has_medicines
 *
 * @package App\Models
 */
class PrescriptionHasMedicine extends Model
{
	use SoftDeletes;
	protected $table = 'prescription_has_medicines';

	protected $casts = [
		'prescription_id' => 'int',
		'medicine_id' => 'int',
		'frequency' => 'int',
		'emergency' => 'bool',
		'start_date' => 'datetime',
		'end_date' => 'datetime'
	];

	protected $fillable = [
		'prescription_id',
		'medicine_id',
		'dosage',
		'status',
		'frequency',
		'emergency',
		'start_date',
		'end_date',
		'observation'
	];

	protected static function boot()
	{
		parent::boot();

		static::created(function ($model) {
			// Start a database transaction
			DB::beginTransaction();
			try {
				self::create_process($model);
				DB::commit();
			} catch (\Illuminate\Database\QueryException $e) {
				DB::rollback();
				Log::error('Error creating model: ' . $e->getMessage());
				throw $e;
			} catch (\Exception $e) {
				// If an exception occurs, rollback the transaction
				DB::rollback();
				// Log the error or handle it as needed
				// You might also throw the exception to propagate it up the stack
				Log::error('Error creating model: ' . $e->getMessage());
				// Don't forget to throw the exception to stop the creating process
				throw $e;
			}
		});
		static::updated(function ($model) {
			DB::beginTransaction();
			try {
				$originalValues = $model->getOriginal();
				if ($model->end_date->greaterThan($originalValues['end_date'])) {
					//ending date was increased
					if ((int)$model->frequency > 1) {
						$date = $originalValues['end_date']->addHours($model->frequency);
						self::create_process($model, $date);
					}
				} elseif ($model->end_date->lessThan($originalValues['end_date'])) {
					//ending date was increased
					PetHasMedicine::where('date', '>', $originalValues['end_date']->format('Y-m-d H:i:s'))
						->where('prescription_has_medicine_id', '=', $model->id)
						->delete();
				}
				//ending date is equal 
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
		static::deleted(function ($model) {
			PetHasMedicine::where([
				'prescription_has_medicine_id' => $model->id,
				'medicine_id' => $model->medicine_id,
			])->delete();
		});
	}

	public function medicine()
	{
		return $this->belongsTo(Medicine::class);
	}

	public function prescription()
	{
		return $this->belongsTo(Prescription::class);
	}

	public function pet_has_medicines()
	{
		return $this->hasMany(PetHasMedicine::class);
	}

	private static function set_administered_medicine($date, $now, $status)
	{
		if ($now->lessThan($date)) {
			return 0;
		}
		return match ($status) {
			default => 1,
			'active' => 1,
			'on_hold' => 0,
			'canceled' => 0,
			'completed' => 1,
		};
	}
	private static function create_pet_has_medicne(PrescriptionHasMedicine $model, Carbon $carbon_date, Carbon $now, int $pet_id)
	{
		PetHasMedicine::create([
			'pet_id' => $pet_id,
			'medicine_id' => $model->medicine_id,
			'dosage' => $model->dosage,
			'status' => $model->status,
			'emergency' => $model->emergency,
			'administered' => self::set_administered_medicine($carbon_date, $now, $model->status),
			'date' => $carbon_date->format('Y-m-d H:i:s'),
			'prescription_has_medicine_id' => $model->id
		]);
	}
	private static function create_process(PrescriptionHasMedicine $model, null|Carbon $start_from_date = null)
	{
		$now = Carbon::now();
		$pet_id = $model->prescription->pet_id;
		if (empty($model->frequency) || (int)$model->frequency < 1) {
			self::create_pet_has_medicne($model, $model->start_date, $now, $pet_id);
		} else {
			$start_date = $start_from_date ?? $model->start_date;
			while ($start_date < $model->end_date) {
				self::create_pet_has_medicne($model, $start_date, $now, $pet_id);
				$start_date = $start_date->addHours($model->frequency);
			}
		}
	}
}
