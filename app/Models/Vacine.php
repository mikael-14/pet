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
 * Class Vacine
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class Vacine extends Model
{
	use SoftDeletes;
	protected $table = 'vacines';

	protected $fillable = [
		'name'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pets_has_vacines', 'vacines_id', 'pets_id')
					->withPivot('id', 'vacination_date', 'local', 'aplication', 'observation', 'deleted_at')
					->withTimestamps();
	}
}
