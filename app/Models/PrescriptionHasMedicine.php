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
 * @property int $prescriptions_id
 * @property int $medicines_id
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
		'prescriptions_id' => 'int',
		'medicines_id' => 'int',
		'frequency' => 'int',
		'emergency' => 'bool',
		'start_date' => 'date',
		'end_date' => 'date'
	];

	protected $fillable = [
		'prescriptions_id',
		'medicines_id',
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
		return $this->belongsTo(Medicine::class, 'medicines_id');
	}

	public function prescription()
	{
		return $this->belongsTo(Prescription::class, 'prescriptions_id');
	}
}
