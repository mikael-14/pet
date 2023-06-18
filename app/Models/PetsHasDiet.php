<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetsHasDiet
 * 
 * @property int $id
 * @property int $pets_id
 * @property int $diets_id
 * @property Carbon $date
 * @property string|null $portion
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Diet $diet
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetsHasDiet extends Model
{
	protected $table = 'pets_has_diets';

	protected $casts = [
		'pets_id' => 'int',
		'diets_id' => 'int',
		'date' => 'date'
	];

	protected $fillable = [
		'pets_id',
		'diets_id',
		'date',
		'portion',
		'observation'
	];

	public function diet()
	{
		return $this->belongsTo(Diet::class, 'diets_id');
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}
}
