<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Person
 * 
 * @property int $id
 * @property string $name
 * @property string $gender
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $vat
 * @property string|null $cc
 * @property Carbon|null $birth_date
 * @property string|null $country
 * @property string|null $state
 * @property string|null $local
 * @property string|null $street
 * @property string|null $zip
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $observation
 * @property int|null $user_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|PersonFlag[] $person_flags
 * @property Collection|Clinic[] $clinics
 * @property Collection|Pet[] $pets
 * @property Collection|PetHasDeworming[] $pet_has_dewormings
 * @property Collection|PetHasMeasure[] $pets_has_measures
 * @property Collection|PetHasTest[] $pets_has_tests
 * @property Collection|PetHasVaccine[] $pet_has_vaccines
 *
 * @package App\Models
 */
class Person extends Model
{
	use SoftDeletes;
	protected $table = 'people';

	protected $casts = [
		'birth_date' => 'datetime',
		'latitude' => 'float',
		'longitude' => 'float',
		'user_id' => 'int'
	];

	protected $fillable = [
		'name',
		'gender',
		'email',
		'phone',
		'vat',
		'cc',
		'birth_date',
		'country',
		'state',
		'local',
		'street',
		'zip',
		'latitude',
		'longitude',
		'observation',
		'user_id',
		'location',
		'map',
	];

	protected $appends = [
		'flags',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function person_flags()
	{
		return $this->hasMany(PersonFlag::class);
	}

	public function clinics()
	{
		return $this->belongsToMany(Clinic::class, 'person_has_clinics');
	}

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'person_has_pets')
			->withPivot('id', 'start_date', 'end_date', 'type', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function pet_has_dewormings()
	{
		return $this->hasMany(PetHasDeworming::class);
	}

	public function pet_has_measures()
	{
		return $this->hasMany(PetHasMeasure::class);
	}

	public function pet_has_tests()
	{
		return $this->hasMany(PetHasTest::class);
	}

	public function pet_has_vaccines()
	{
		return $this->hasMany(PetHasVaccine::class);
	}

	public function pet_has_medicines()
	{
		return $this->hasMany(PetHasMedicine::class);
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}

	public function getFlagsAttribute(): array
	{

		return $this->person_flags()->pluck('name')->toArray();
	}
	/**
	 * Returns the 'latitude' and 'longitude' attributes as the computed 'location' attribute,
	 * as a standard Google Maps style Point array with 'lat' and 'lng' attributes.
	 *
	 * Used by the Filament Google Maps package.
	 *
	 * Requires the 'location' attribute be included in this model's $fillable array.
	 *
	 * @return array
	 */

	public function getLocationAttribute(): array
	{
		return [
			"lat" => (float)$this->latitude,
			"lng" => (float)$this->longitude,
		];
	}
	public function getMapAttribute(): array
	{
		return [
			"lat" => (float)$this->latitude,
			"lng" => (float)$this->longitude,
		];
	}

	/**
	 * Takes a Google style Point array of 'lat' and 'lng' values and assigns them to the
	 * 'latitude' and 'longitude' attributes on this model.
	 *
	 * Used by the Filament Google Maps package.
	 *
	 * Requires the 'location' attribute be included in this model's $fillable array.
	 *
	 * @param ?array $location
	 * @return void
	 */
	public function setLocationAttribute(?array $location): void
	{
		if (is_array($location)) {
			$this->attributes['latitude'] = $location['lat'];
			$this->attributes['longitude'] = $location['lng'];
			unset($this->attributes['location']);
		}
	}
	public function setMapAttribute(?array $location): void
	{
		if (is_array($location)) {
			$this->attributes['latitude'] = $location['lat'];
			$this->attributes['longitude'] = $location['lng'];
			unset($this->attributes['location']);
		}
	}

	/**
	 * Get the lat and lng attribute/field names used on this table
	 *
	 * Used by the Filament Google Maps package.
	 *
	 * @return string[]
	 */
	public static function getLatLngAttributes(): array
	{
		return [
			'lat' => 'latitude',
			'lng' => 'longitude',
		];
	}

	/**
	 * Get the name of the computed location attribute
	 *
	 * Used by the Filament Google Maps package.
	 *
	 * @return string
	 */
	public static function getComputedLocation(): string
	{
		return 'location';
	}
	public static function getComputedMap(): string
	{
		return 'map';
	}

	public static function avaibleUsers(): array
	{
		$tableName = with(new Person)->getTable();
		return User::selectRaw('id,CONCAT(name, " - ", email) as full')
			->leftJoin('model_has_roles', 'id', '=', 'model_id')
			->where('role_id', '!=', 1)
			->whereNotIn('id', function ($query) use ($tableName) {
				$query->select('user_id')
					->from($tableName)
					->whereRaw($tableName . '.user_id = users.id');
			})->pluck('full', 'id')->toArray();
	}
	public static function getPersonByFlag(array $flag): \Illuminate\Support\Collection
	{
		return Person::join('person_flags', 'id', '=', 'person_id')
			->whereIn('person_flags.name', $flag)
			->pluck('people.name', 'people.id');
	}
	public static function searchPerson(array $flag, string | bool $search = false)
	{
		if (empty($search)) {
			return Person::join('person_flags', 'id', '=', 'person_id')
				->whereIn('person_flags.name', $flag)
				->select('people.id', 'people.name', 'person_flags.name as flag_name')
				->orderBy('created_at', 'desc')
				->limit(10)->get();
		}
		return Person::join('person_flags', 'id', '=', 'person_id')
			->whereIn('person_flags.name', $flag)
			->where('people.name', 'like', "%{$search}%")
			->select('people.id', 'people.name', 'person_flags.name as flag_name')
			->limit(10)->get();
	}
}
