<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Shelter
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
 * @property Collection|ShelterBlock[] $shelter_blocks
 *
 * @package App\Models
 */
class Shelter extends Model
{
	protected $table = 'shelters';

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
		'status'
	];

    public function shelter_blocks()
	{
		return $this->hasMany(ShelterBlock::class);
	}
}
