<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShelterBlock
 * 
 * @property int $id
 * @property int $shelter_id
 * @property string $name
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Shelter $shelter
 * @property Collection|Pet[] $pets
 *
 * @package App\Models
 */
class ShelterBlock extends Model
{
	protected $table = 'shelter_blocks';

	protected $casts = [
		'shelter_id' => 'int'
	];

	protected $fillable = [
		'shelter_id',
		'name',
		'color'
	];

	public function shelter()
	{
		return $this->belongsTo(Shelter::class);
	}

	public function pets()
	{
		return $this->hasMany(Pet::class);
	}
	public static function getOptions()
	{
		$tableName = with(new Shelter)->getTable();
		return ShelterBlock::join($tableName, "shelter_blocks.shelter_id", '=', "$tableName.id")
			->selectRaw("CONCAT(shelter_blocks.name, ' - ', $tableName.name) AS name,shelter_blocks.id,shelter_blocks.color")
			->where("$tableName.status", 1)
			->get();
	}
}
