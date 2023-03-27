<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
 * @property string|null $application
 * @property string|null $observation
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
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
		'tests_id' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'pets_id',
		'tests_id',
		'date',
		'result',
		'local',
		'application',
		'observation'
	];

	public function pet()
	{
		return $this->belongsTo(Pet::class, 'pets_id');
	}

	public function test()
	{
		return $this->belongsTo(Test::class, 'tests_id');
	}
}
