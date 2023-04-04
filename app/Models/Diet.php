<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Diet
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class Diet extends Model
{
	protected $table = 'diets';

	protected $fillable = [
		'name'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pets_has_diets', 'diets_id', 'pets_id')
					->withPivot('id', 'date', 'portion', 'observation')
					->withTimestamps();
	}
}
