<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Prescription
 * 
 * @property int $id
 * @property int $medicines_id
 * @property int $pets_id
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
 * @property Pet $pet
 *
 * @package App\Models
 */
class Prescription extends Model
{
	use SoftDeletes;
	protected $table = 'prescriptions';

	protected $casts = [
		'medicines_id' => 'int',
		'pets_id' => 'int',
		'frequency' => 'int',
		'emergency' => 'bool',
		'start_date' => 'datetime',
		'end_date' => 'datetime'
	];

	protected $fillable = [
		'medicines_id',
		'pets_id',
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

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}
}
