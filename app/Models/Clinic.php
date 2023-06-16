<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Clinic
 * 
 * @property int $id
 * @property string $name
 * @property string|null $country
 * @property string|null $state
 * @property string|null $local
 * @property string|null $street
 * @property string|null $zip
 * @property float|null $latitude
 * @property float|null $longitude
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Person[] $people
 *
 * @package App\Models
 */
class Clinic extends Model
{
	protected $table = 'clinics';

	protected $casts = [
		'latitude' => 'float',
		'longitude' => 'float',
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'country',
		'state',
		'local',
		'street',
		'zip',
		'latitude',
		'longitude',
		'status',
		'location',
		'map',
	];

	protected $appends = [
		'location',
		'map',
	];

	public function people()
	{
		return $this->belongsToMany(Person::class, 'person_has_clinics', 'clinics_id', 'people_id');
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
}
