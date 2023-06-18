<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PetsHasTest
 * 
 * @property int $id
 * @property int $pets_id
 * @property int $tests_id
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
class PetsHasTest extends Model
{
	use SoftDeletes;
	protected $table = 'pets_has_tests';

	protected $casts = [
		'pets_id' => 'int',
		'tests_id' => 'int',
		'date' => 'date',
		'people_id' => 'int'
	];

	protected $fillable = [
		'pets_id',
		'tests_id',
		'date',
		'result',
		'local',
		'people_id',
		'observation'
	];

	public function person()
	{
		return $this->belongsTo(Person::class, 'people_id');
	}

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}

	public function test()
	{
		return $this->belongsTo(Test::class, 'tests_id');
	}
}
