<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PetHasMedicine
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $medicine_id
 * @property string $dosage
 * @property string|null $status
 * @property bool $emergency
 * @property bool|null $administered
 * @property Carbon $date
 * @property string|null $observation
 * @property int|null $person_id
 * @property int|null $prescription_has_medicine_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Medicine $medicine
 * @property Person|null $person
 * @property Pet $pet
 * @property PrescriptionHasMedicine|null $prescription_has_medicine
 *
 * @package App\Models
 */
class PetHasMedicine extends Model
{
	use SoftDeletes;
	protected $table = 'pet_has_medicines';

	protected $casts = [
		'pet_id' => 'int',
		'medicine_id' => 'int',
		'emergency' => 'bool',
		'administered' => 'bool',
		'date' => 'datetime',
		'person_id' => 'int',
		'prescription_has_medicine_id' => 'int'
	];

	protected $fillable = [
		'pet_id',
		'medicine_id',
		'dosage',
		'status',
		'emergency',
		'administered',
		'date',
		'observation',
		'person_id',
		'prescription_has_medicine_id'
	];

	public function medicine()
	{
		return $this->belongsTo(Medicine::class);
	}

	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}

	public function prescription_has_medicine()
	{
		return $this->belongsTo(PrescriptionHasMedicine::class);
	}
}
