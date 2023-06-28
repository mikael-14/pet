<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonHasClinic
 * 
 * @property int $person_id
 * @property int $clinic_id
 * 
 * @property Clinic $clinic
 * @property Person $person
 *
 * @package App\Models
 */
class PersonHasClinic extends Model
{
	protected $table = 'person_has_clinics';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'person_id' => 'int',
		'clinic_id' => 'int'
	];

	protected $fillable = [
		'person_id',
		'clinic_id'
	];

	public function clinic()
	{
		return $this->belongsTo(Clinic::class);
	}

	public function person()
	{
		return $this->belongsTo(Person::class);
	}
}
