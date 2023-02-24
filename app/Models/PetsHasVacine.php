<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PetsHasVacine
 * 
 * @property int $id
 * @property int $pets_id
 * @property int $vacines_id
 * @property Carbon $vacination_date
 * @property string|null $local
 * @property string|null $aplication
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Pet $pet
 * @property Vacine $vacine
 *
 * @package App\Models
 */
class PetsHasVacine extends Model
{
	use SoftDeletes;
	protected $table = 'pets_has_vacines';

	protected $casts = [
		'pets_id' => 'int',
		'vacines_id' => 'int'
	];

	protected $dates = [
		'vacination_date'
	];

	protected $fillable = [
		'pets_id',
		'vacines_id',
		'vacination_date',
		'local',
		'aplication',
		'observation'
	];

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}

	public function vacine()
	{
		return $this->belongsTo(Vacine::class, 'vacines_id');
	}
}
