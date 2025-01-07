<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\PetGender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Kenepa\ResourceLock\Models\Concerns\HasLocks;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
 * @property int|null $entry_status_id
 * @property Carbon $entry_date
 * @property int $shelter_block_id
 * @property int|null $status_id
 * @property Carbon $status_date
 * @property Carbon|null $birth_date
 * @property bool $sterilized
 * @property Carbon|null $sterilized_date
 * @property string|null $sterilized_local
 * @property string|null $color
 * @property string|null $coat
 * @property string|null $breed
 * @property string|null $qrcode
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Status|null $status
 * @property ShelterBlock $shelter_block
 * @property Collection|Person[] $people
 * @property Collection|Deworming[] $dewormings
 * @property Collection|Diet[] $diets
 * @property Collection|PetHasMeasure[] $pet_has_measures
 * @property Collection|Medicine[] $medicines
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

	use SoftDeletes;
	protected $table = 'pets';

	protected $casts = [
		'adoptable' => 'bool',
		'chip_date' => 'datetime',
		'entry_status_id' => 'int',
		'entry_date' => 'datetime',
		'shelter_block_id' => 'int',
		'status_id' => 'int',
		'status_date' => 'datetime',
		'birth_date' => 'datetime',
		'sterilized' => 'bool',
		'sterilized_date' => 'datetime',
		'gender' => PetGender::class
	];

	protected $fillable = [
		'name',
		'species',
		'gender',
		'adoptable',
		'chip',
		'chip_date',
		'entry_status_id',
		'entry_date',
		'shelter_block_id',
		'status_id',
		'status_date',
		'birth_date',
		'sterilized',
		'sterilized_date',
		'sterilized_local',
		'color',
		'coat',
		'breed',
		'qrcode',
		'observation'
	];

	public function status()
	{
		return $this->belongsTo(Status::class);
	}


	public function people()
	{
		return $this->belongsToMany(Person::class, 'person_has_pets')
					->withPivot('id', 'start_date', 'end_date', 'type', 'observation', 'deleted_at')
					->withTimestamps();
	}

	public function dewormings()
	{
		return $this->belongsToMany(Deworming::class, 'pet_has_dewormings')
					->withPivot('id', 'date', 'expire_at', 'local', 'person_id', 'observation')
					->withTimestamps();
	}

	public function diets()
	{
		return $this->belongsToMany(Diet::class, 'pet_has_diets')
					->withPivot('id', 'date', 'portion', 'observation')
					->withTimestamps();
	}

	public function pet_has_measures()
	{
		return $this->hasMany(PetHasMeasure::class);
	}

	public function medicines()
	{
		return $this->belongsToMany(Medicine::class, 'pet_has_medicines')
					->withPivot('id', 'dosage', 'status', 'emergency', 'administered', 'date', 'observation', 'person_id', 'prescription_has_medicine_id', 'deleted_at')
					->withTimestamps();
	}

	public function tests()
	{
		return $this->belongsToMany(Test::class, 'pet_has_tests')
					->withPivot('id', 'date', 'result', 'local', 'person_id', 'observation', 'deleted_at')
					->withTimestamps();
	}

	public function vaccines()
	{
		return $this->belongsToMany(Vaccine::class, 'pet_has_vaccines')
					->withPivot('id', 'date', 'expire_at', 'local', 'person_id', 'observation', 'deleted_at')
					->withTimestamps();
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}


	public function entry_status()
	{
		return $this->belongsTo(Status::class);
	}

	public function shelter_block()
	{
		return $this->belongsTo(ShelterBlock::class);
	}


	public function getHighlighTestsAttribute()
	{
		return PetHasTest::select('pet_has_tests.date', 'pet_has_tests.result', 'tests.name', 'pet_has_tests.test_id')
		->join('tests', 'tests.id', '=', 'pet_has_tests.test_id')
		->whereIn(
			DB::raw('(pet_has_tests.date, pet_has_tests.test_id)'),
			function ($query) {
				$query->select(DB::raw('MAX(date) as date, test_id'))
					->from('pet_has_tests')
					->where('pet_id', $this->id)
					->groupBy('test_id');
			}
		)
		->where('tests.highlight', 1)
		->where('pet_has_tests.pet_id', $this->id)
		->get();
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

	public function pet_has_medicine()
	{
		return $this->hasMany(PetHasMedicine::class);
	}
}
