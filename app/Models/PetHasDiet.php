<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetHasDiet
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $diet_id
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
class PetHasDiet extends Model
{
	protected $table = 'pet_has_diets';

	protected $casts = [
		'pet_id' => 'int',
		'diet_id' => 'int',
		'date' => 'date'
	];

	protected $fillable = [
		'pet_id',
		'diet_id',
		'date',
		'portion',
		'observation'
	];

	public function diet()
	{
		return $this->belongsTo(Diet::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}
}
