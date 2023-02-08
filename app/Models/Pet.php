<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Pet
 * 
 * @property int $id
 * @property string $name
 * @property Carbon $entry_date
 * @property int|null $weight
 * @property int|null $height
 * @property Carbon|null $birth_date
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models
 */
class Pet extends Model
{
	use SoftDeletes;
	protected $table = 'pets';

	protected $casts = [
		'weight' => 'int',
		'height' => 'int'
	];

	protected $dates = [
		'entry_date',
		'birth_date'
	];

	protected $fillable = [
		'name',
		'entry_date',
		'weight',
		'height',
		'birth_date',
		'color'
	];
}
