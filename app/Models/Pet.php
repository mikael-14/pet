<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Kenepa\ResourceLock\Models\Concerns\HasLocks;

/**
 * Class Pet
 * 
 * @property int $id
 * @property string $name
 * @property string|null $species
 * @property string $gender
 * @property bool $adoptable
 * @property string|null $chip
 * @property Carbon|null $chip_date
 * @property int $shelter_blocks_id
 * @property Carbon $entry_date
 * @property int $entry_statuses_id
 * @property Carbon|null $birth_date
 * @property bool $sterilized
 * @property Carbon|null $sterilized_date
 * @property string|null $sterilized_local
 * @property string|null $color
 * @property string|null $coat
 * @property string|null $breed
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property EntryStatus $entry_status
 * @property ShelterBlock $shelter_block
 * @property Collection|Person[] $people
 * @property Collection|Deworming[] $dewormings
 * @property Collection|Diet[] $diets
 * @property Collection|PetsHasMeasure[] $pets_has_measures
 * @property Collection|Test[] $tests
 * @property Collection|Vaccine[] $vaccines
 * @property Collection|Prescription[] $prescriptions
 *
 * @package App\Models
 */
class Pet extends Model implements HasMedia
{
	use SoftDeletes;
	use InteractsWithMedia;
	use HasLocks;

	protected $table = 'pets';

	protected $casts = [
		'adoptable' => 'bool',
		'chip_date' => 'date',
		'shelter_blocks_id' => 'int',
		'entry_date' => 'date',
		'entry_statuses_id' => 'int',
		'birth_date' => 'date',
		'sterilized' => 'bool',
		'sterilized_date' => 'date'
	];

	protected $dates = [
		'chip_date',
		'entry_date',
		'birth_date',
		'sterilized_date'
	];

	protected $fillable = [
		'name',
		'species',
		'gender',
		'adoptable',
		'chip',
		'chip_date',
		'shelter_blocks_id',
		'entry_date',
		'entry_statuses_id',
		'birth_date',
		'sterilized',
		'sterilized_date',
		'sterilized_local',
		'color',
		'coat',
		'breed',
		'observation'
	];

	public function entry_status()
	{
		return $this->belongsTo(EntryStatus::class, 'entry_statuses_id');
	}

	public function shelter_block()
	{
		return $this->belongsTo(ShelterBlock::class, 'shelter_blocks_id');
	}

	public function people()
	{
		return $this->belongsToMany(Person::class, 'person_has_pets', 'pets_id', 'people_id')
			->withPivot('id', 'start_date', 'end_date', 'type', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function dewormings()
	{
		return $this->belongsToMany(Deworming::class, 'pets_has_dewormings', 'pets_id', 'dewormings_id')
			->withPivot('id', 'date', 'expiration_date', 'local', 'application', 'observation')
			->withTimestamps();
	}
	public function diets()
	{
		return $this->belongsToMany(Diet::class, 'pets_has_diets', 'pets_id', 'diets_id')
			->withPivot('id', 'date', 'portion', 'observation')
			->withTimestamps();
	}

	public function pets_has_measures()
	{
		return $this->hasMany(PetsHasMeasure::class, 'pets_id');
	}

	public function tests()
	{
		return $this->belongsToMany(Test::class, 'pets_has_tests', 'pets_id', 'tests_id')
			->withPivot('id', 'date', 'result', 'local', 'application', 'observation', 'deleted_at')
			->withTimestamps();
	}
	public function vaccines()
	{
		return $this->belongsToMany(Vaccine::class, 'pets_has_vaccines', 'pets_id', 'vaccines_id')
			->withPivot('id', 'date', 'expires_at', 'local', 'application', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class, 'pets_id');
	}

	public function getConfigSpecie(): string
	{
		$configSpecies = __('pet/species');
		return $configSpecies[$this->species] ?? $this->species;
	}

	public function pet_has_vaccine()
	{
		return $this->hasMany(PetsHasVaccine::class, 'pets_id');
	}

	public function pet_has_test()
	{
		return $this->hasMany(PetsHasTest::class, 'pets_id');
	}

	public function pet_has_measure()
	{
		return $this->hasMany(PetsHasMeasure::class, 'pets_id');
	}

	public function pet_has_diet()
	{
		return $this->hasMany(PetsHasDiet::class, 'pets_id');
	}

	public function pet_has_deworming()
	{
		return $this->hasMany(PetsHasDeworming::class, 'pets_id');
	}

	// determines whether the associated media files should be deleted when the Eloquent model is deleted. True for don't delete the media files
	// public function shouldDeletePreservingMedia() :bool{
	// 	return true;
	// }
}
