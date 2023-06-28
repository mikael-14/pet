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
 * Class Test
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
class Test extends Model
{
	use SoftDeletes;
	protected $table = 'tests';

	protected $fillable = [
		'name'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pet_has_tests', 'test_id', 'pet_id')
					->withPivot('id', 'date', 'result', 'local', 'application', 'observation', 'deleted_at')
					->withTimestamps();
	}
}
