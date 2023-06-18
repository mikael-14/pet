<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PetsHasVaccine
 * 
 * @property int $id
 * @property int $pets_id
 * @property int $vaccines_id
 * @property Carbon $date
 * @property Carbon|null $expires_at
 * @property string|null $local
 * @property int|null $people_id
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
class PetsHasVaccine extends Model
{
	use SoftDeletes;
	protected $table = 'pets_has_vaccines';

	protected $casts = [
		'pets_id' => 'int',
		'vaccines_id' => 'int',
		'date' => 'date',
		'expires_at' => 'date',
		'people_id' => 'int'
	];

	protected $fillable = [
		'pets_id',
		'vaccines_id',
		'date',
		'expires_at',
		'local',
		'people_id',
		'observation'
	];

	public function person()
	{
		return $this->belongsTo(Person::class, 'people_id');
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}

	public function vaccine()
	{
		return $this->belongsTo(Vaccine::class, 'vaccines_id');
	}
}
