<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetHasDeworming
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $deworming_id
 * @property Carbon $date
 * @property Carbon|null $expire_at
 * @property string|null $local
 * @property int|null $person_id
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Deworming $deworming
 * @property Person|null $person
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetHasDeworming extends Model
{
	protected $table = 'pet_has_dewormings';

	protected $casts = [
		'pet_id' => 'int',
		'deworming_id' => 'int',
		'date' => 'datetime',
		'expire_at' => 'datetime',
		'person_id' => 'int'
	];

	protected $fillable = [
		'pet_id',
		'deworming_id',
		'date',
		'expire_at',
		'local',
		'person_id',
		'observation'
	];

	public function deworming()
	{
		return $this->belongsTo(Deworming::class);
	}

	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}
}
