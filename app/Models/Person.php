<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
 * @property float $latitude
 * @property float $longitude
 * @property string|null $observation
 * @property int|null $users_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|PersonFlag[] $person_flags
 * @property Collection|Pet[] $pets
 * @property Collection|PetsHasDeworming[] $pets_has_dewormings
 * @property Collection|PetsHasMeasure[] $pets_has_measures
 * @property Collection|PetsHasTest[] $pets_has_tests
 * @property Collection|PetsHasVaccine[] $pets_has_vaccines
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
		'users_id' => 'int'
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
		'users_id',
		'location',
		'map',
	];

	protected $appends = [
		'location',
		'map',
		'flags',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}

	public function person_flags()
	{
		return $this->hasMany(PersonFlag::class);
	}

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'person_has_pets', 'people_id', 'pets_id')
			->withPivot('id', 'start_date', 'end_date', 'type', 'observation', 'deleted_at')
			->withTimestamps();
	}

	public function pets_has_dewormings()
	{
		return $this->hasMany(PetsHasDeworming::class, 'people_id');
	}

	public function pets_has_measures()
	{
		return $this->hasMany(PetsHasMeasure::class, 'people_id');
	}

	public function pets_has_tests()
	{
		return $this->hasMany(PetsHasTest::class, 'people_id');
	}

	public function pets_has_vaccines()
	{
		return $this->hasMany(PetsHasVaccine::class, 'people_id');
	}
	public function getFlagsAttribute(): array {

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
				$query->select('users_id')
					->from($tableName)
					->whereRaw($tableName . '.users_id = users.id');
			})->pluck('full', 'id')->toArray();
	}
}
