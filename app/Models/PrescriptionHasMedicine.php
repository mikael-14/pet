<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
}
