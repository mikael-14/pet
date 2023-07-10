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
 * @property int $shelter_block_id
 * @property Carbon $entry_date
 * @property int $entry_status_id
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
 * @property Collection|PetHasMeasure[] $pets_has_measures
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
		'chip_date' => 'datetime',
		'shelter_block_id' => 'int',
		'entry_date' => 'datetime',
		'entry_status_id' => 'int',
		'birth_date' => 'datetime',
		'sterilized' => 'bool',
		'sterilized_date' => 'datetime'
	];

	protected $fillable = [
		'name',
		'species',
		'gender',
		'adoptable',
		'chip',
		'chip_date',
		'shelter_block_id',
		'entry_date',
		'entry_status_id',
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
		return $this->belongsTo(EntryStatus::class);
	}

	public function shelter_block()
	{
		return $this->belongsTo(ShelterBlock::class);
	}

	public function people()
	{
		return $this->belongsToMany(Person::class, 'person_has_pets', 'pet_id', 'person_id')
			->withPivot('id', 'start_date', 'end_date', 'type', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function dewormings()
	{
		return $this->belongsToMany(Deworming::class, 'pet_has_dewormings', 'pet_id', 'deworming_id')
			->withPivot('id', 'date', 'expire_at', 'local', 'person_id', 'observation')
			->withTimestamps();
	}

	public function diets()
	{
		return $this->belongsToMany(Diet::class, 'pet_has_diets', 'pet_id', 'diet_id')
			->withPivot('id', 'date', 'portion', 'observation')
			->withTimestamps();
	}

	public function pet_has_measures()
	{
		return $this->hasMany(PetHasMeasure::class);
	}

	public function tests()
	{
		return $this->belongsToMany(Test::class, 'pet_has_tests', 'pet_id', 'test_id')
			->withPivot('id', 'date', 'result', 'local', 'person_id', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function vaccines()
	{
		return $this->belongsToMany(Vaccine::class, 'pet_has_vaccines', 'pet_id', 'vaccine_id')
			->withPivot('id', 'date', 'expire_at', 'local', 'person_id', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}

	public function getConfigSpecie(): string
	{
		$configSpecies = __('pet/species');
		return $configSpecies[$this->species] ?? $this->species;
	}

	public function pet_has_vaccine()
	{
		return $this->hasMany(PetHasVaccine::class);
	}

	public function pet_has_test()
	{
		return $this->hasMany(PetHasTest::class);
	}

	public function pet_has_measure()
	{
		return $this->hasMany(PetHasMeasure::class);
	}

	public function pet_has_diet()
	{
		return $this->hasMany(PetHasDiet::class);
	}

	public function pet_has_deworming()
	{
		return $this->hasMany(PetHasDeworming::class);
	}
}
