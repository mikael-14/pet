<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
/**
 * Class Pet
 * 
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string $gender
 * @property string|null $chip
 * @property Carbon|null $chip_date
 * @property Carbon|null $birth_date
 * @property Carbon $entry_date
 * @property string $sterilized
 * @property Carbon|null $sterilized_date
 * @property string|null $sterilized_local
 * @property float|null $weight
 * @property float|null $height
 * @property string|null $color
 * @property string|null $coat
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Vacine[] $vacines
 *
 * @package App\Models
 */
class Pet extends Model implements HasMedia
{
	use SoftDeletes;
	use InteractsWithMedia;

	protected $table = 'pets';

	protected $casts = [
		'sterilized' => 'boolean'
	];

	protected $dates = [
		'chip_date',
		'birth_date',
		'entry_date',
		'sterilized_date'
	];

	protected $fillable = [
		'name',
		'image',
		'gender',
		'chip',
		'chip_date',
		'birth_date',
		'entry_date',
		'sterilized_date',
		'sterilized_local',
		'color',
		'coat',
		'observation'
	];

	public function vacines()
	{
		return $this->belongsToMany(Vacine::class, 'pets_has_vacines', 'pets_id', 'vacines_id')
					->withPivot('id', 'vacination_date', 'local', 'aplication', 'observation', 'deleted_at')
					->withTimestamps();
	}
}
