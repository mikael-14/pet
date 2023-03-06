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
 * Class Pet
 * 
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string $gender
 * @property string|null $chip
 * @property Carbon|null $chip_date
 * @property int $pet_statuses_id
 * @property int $pet_locations_id
 * @property Carbon|null $birth_date
 * @property Carbon $entry_date
 * @property bool $sterilized
 * @property Carbon|null $sterilized_date
 * @property string|null $sterilized_local
 * @property string|null $color
 * @property string|null $coat
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property PetLocation $pet_location
 * @property PetStatus $pet_status
 * @property Collection|Vaccine[] $vaccines
 *
 * @package App\Models
 */
class Pet extends Model implements HasMedia
{
	use SoftDeletes;
	use InteractsWithMedia;

	protected $table = 'pets';

	protected $casts = [
		'pet_statuses_id' => 'int',
		'pet_locations_id' => 'int',
		'sterilized' => 'bool'
	];

	protected $dates = [
		'chip_date',
		'birth_date',
		'entry_date',
		'sterilized_date'
	];

	protected $fillable = [
		'name',
		'species',
		'image',
		'gender',
		'chip',
		'chip_date',
		'pet_statuses_id',
		'pet_locations_id',
		'birth_date',
		'entry_date',
		'sterilized',
		'sterilized_date',
		'sterilized_local',
		'color',
		'coat',
		'observation'
	];

	public function pet_location()
	{
		return $this->belongsTo(PetLocation::class, 'pet_locations_id');
	}

	public function pet_status()
	{
		return $this->belongsTo(PetStatus::class, 'pet_statuses_id');
	}

	public function vaccines()
	{
		return $this->belongsToMany(Vaccine::class, 'pets_has_vaccines', 'pets_id', 'vaccines_id')
					->withPivot('id', 'vaccine_date', 'local', 'aplication', 'observation', 'deleted_at')
					->withTimestamps();
	}
}
