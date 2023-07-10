<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PetHasVaccine
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $vaccine_id
 * @property Carbon $date
 * @property Carbon|null $expire_at
 * @property string|null $local
 * @property int|null $person_id
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Person|null $person
 * @property Pet $pet
 * @property Vaccine $vaccine
 *
 * @package App\Models
 */
class PetHasVaccine extends Model
{
	use SoftDeletes;
	protected $table = 'pet_has_vaccines';

	protected $casts = [
		'pet_id' => 'int',
		'vaccine_id' => 'int',
		'date' => 'datetime',
		'expire_at' => 'datetime',
		'person_id' => 'int'
	];

	protected $fillable = [
		'pet_id',
		'vaccine_id',
		'date',
		'expire_at',
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

	public function vaccine()
	{
		return $this->belongsTo(Vaccine::class);
	}
}
