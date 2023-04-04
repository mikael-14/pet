<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PetsHasDeworming
 * 
 * @property int $id
 * @property int $pets_id
 * @property int $dewormings_id
 * @property Carbon $date
 * @property Carbon|null $expiration_date
 * @property string|null $local
 * @property string|null $application
 * @property string|null $observation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Deworming $deworming
 * @property Pet $pet
 *
 * @package App\Models
 */
class PetsHasDeworming extends Model
{
	protected $table = 'pets_has_dewormings';

	protected $casts = [
		'pets_id' => 'int',
		'dewormings_id' => 'int'
	];

	protected $dates = [
		'date',
		'expiration_date'
	];

	protected $fillable = [
		'pets_id',
		'dewormings_id',
		'date',
		'expiration_date',
		'local',
		'application',
		'observation'
	];

	public function deworming()
	{
		return $this->belongsTo(Deworming::class, 'dewormings_id');
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}
}
