<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\DewormingType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Deworming
 * 
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int $expire
 * @property int|null $notification
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class Deworming extends Model
{
	use SoftDeletes;
	protected $table = 'dewormings';

	protected $casts = [
		'expire' => 'int',
		'notification' => 'int',
		'type' => DewormingType::class

	];

	protected $fillable = [
		'name',
		'type',
		'expire',
		'notification'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pet_has_dewormings', 'deworming_id', 'pet_id')
					->withPivot('id', 'date', 'expiration_date', 'local', 'application', 'observation')
					->withTimestamps();
	}
}
