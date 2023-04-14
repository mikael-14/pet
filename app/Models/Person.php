<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Person
 * 
 * @property int $id
 * @property string $name
 * @property string $gender
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $vat
 * @property string|null $cc
 * @property Carbon|null $birth_date
 * @property string $address
 * @property string $town
 * @property string|null $observation
 * @property int $users_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Collection|PersonFlag[] $person_flags
 *
 * @package App\Models
 */
class Person extends Model
{
	use SoftDeletes;
	protected $table = 'people';

	protected $casts = [
		'users_id' => 'int'
	];

	protected $dates = [
		'birth_date'
	];

	protected $fillable = [
		'name',
		'gender',
		'email',
		'phone',
		'vat',
		'cc',
		'birth_date',
		'address',
		'town',
		'observation',
		'users_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'users_id');
	}

	public function person_flags()
	{
		return $this->hasMany(PersonFlag::class);
	}
	public static function avaibleUsers(): array
	{
		$tableName = with(new Person)->getTable();
		return User::selectRaw('id,CONCAT(name, " - ", email) as full')
			->leftJoin('model_has_roles', 'id', '=', 'model_id')
			->where('role_id', '!=', 1)
			->whereNotIn('id', function ($query) use ($tableName) {
				$query->select('users_id')
					->from($tableName)
					->whereRaw($tableName . '.users_id = users.id');
			})->pluck('full', 'id')->toArray();
	}
}
