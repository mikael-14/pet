<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetStatus
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
class PetStatus extends Model
{
	protected $table = 'pet_statuses';

	protected $fillable = [
		'name',
		'color'
	];

	public function pets()
	{
		return $this->hasMany(Pet::class, 'pet_statuses_id');
	}
}
