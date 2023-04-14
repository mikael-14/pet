<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

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
		'person_id' => 'int'
	];

	public function person()
	{
		return $this->belongsTo(Person::class);
	}
	public static function flags()
	{
		return collect(config('pet-person-flags'))->mapWithKeys(function (array $item, string $key) {
			if(!isset($key['status']) || $key['status'] !== false)
			return [$key => $item['name']?? $key];
		})->all();
	}
}
