<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShelterLocation
 * 
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class ShelterLocation extends Model
{
	protected $table = 'shelter_locations';

	protected $fillable = [
		'name',
		'color'
	];

	public function pets()
	{
		return $this->hasMany(Pet::class, 'shelter_locations_id');
	}
}
