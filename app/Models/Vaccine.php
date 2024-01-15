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
 * Class Vaccine
 * 
 * @property int $id
 * @property string $name
 * @property int $expire
 * @property int $notification
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class Vaccine extends Model
{
	use SoftDeletes;
	protected $table = 'vaccines';

	protected $casts = [
		'expire' => 'int',
		'notification' => 'int'
	];

	protected $fillable = [
		'name',
		'expire',
		'notification'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pet_has_vaccines', 'vaccine_id', 'pet_id')
					->withPivot('id', 'vaccine_date', 'local', 'application', 'observation', 'deleted_at')
					->withTimestamps();
	}
}
