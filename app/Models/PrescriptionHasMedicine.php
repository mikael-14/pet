<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property string $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Medicine $medicine
 * @property Prescription $prescription
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
		'start_date' => 'date',
		'end_date' => 'date'
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

	public function medicine()
	{
		return $this->belongsTo(Medicine::class, 'medicine_id');
	}

	public function prescription()
	{
		return $this->belongsTo(Prescription::class, 'prescription_id');
	}
}
