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

}
