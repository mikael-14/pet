<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\PersonFlag as EnumsPersonFlag;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonFlag
 * 
 * @property int $person_id
 * @property string $name
 * 
 * @property Person $person
 *
 * @package App\Models
 */
class PersonFlag extends Model
{
	protected $table = 'person_flags';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'person_id' => 'int',
		'name' => EnumsPersonFlag::class
	];
	protected $fillable = [
		'person_id',
		'name',
	];
	public function person()
	{
		return $this->belongsTo(Person::class);
	}

}
