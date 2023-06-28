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
 * Class Medicine
 * 
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $dosage
 * @property array|null $active_ingredient
 * @property string|null $aplication
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Prescription[] $prescriptions
 *
 * @package App\Models
 */
class Medicine extends Model
{
	use SoftDeletes;
	protected $table = 'medicines';

	protected $casts = [
		'active_ingredient' => 'json'
	];

	protected $fillable = [
		'name',
		'type',
		'dosage',
		'active_ingredient',
		'aplication',
		'description'
	];

	protected $appends = [
		'active_ingredient_formatted'
	];

	public function getActiveIngredientFormattedAttribute(): array
	{
		return count($this->active_ingredient) ? array_column($this->active_ingredient, 'active_ingredient') : [];
	}

	public function prescriptions()
	{
		return $this->hasMany(Prescription::class);
	}

	public static function getAllActiveIngredientFormatted(): array
	{
		$array = Medicine::all(['active_ingredient'])->toArray();
		$all_values = [];
		foreach ($array as $value){
			$all_values = array_merge($all_values, $value['active_ingredient_formatted']);
		}
		return array_unique($all_values,SORT_STRING);
	}
}
