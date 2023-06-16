<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonHasClinic
 * 
 * @property int $people_id
 * @property int $clinics_id
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
		'people_id' => 'int',
		'clinics_id' => 'int'
	];

	protected $fillable = [
		'people_id',
		'clinics_id'
	];

	public function clinic()
	{
		return $this->belongsTo(Clinic::class, 'clinics_id');
	}

	public function person()
	{
		return $this->belongsTo(Person::class, 'people_id');
	}
}
