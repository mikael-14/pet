<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class PetHasTest
 * 
 * @property int $id
 * @property int $pet_id
 * @property int $test_id
 * @property Carbon $date
 * @property string $result
 * @property string|null $local
 * @property int|null $people_id
 * @property string|null $observation
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Person|null $person
 * @property Pet $pet
 * @property Test $test
 *
 * @package App\Models
 */
class PetHasTest extends Model implements HasMedia
{
	use SoftDeletes;
	use InteractsWithMedia;
	
	protected $table = 'pet_has_tests';

	protected $casts = [
		'pet_id' => 'int',
		'test_id' => 'int',
		'date' => 'datetime',
		'person_id' => 'int'
	];

	protected $fillable = [
		'pet_id',
		'test_id',
		'date',
		'result',
		'local',
		'person_id',
		'observation'
	];

	public function person()
	{
		return $this->belongsTo(Person::class);
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class);
	}

	public function test()
	{
		return $this->belongsTo(Test::class);
	}
	
}
