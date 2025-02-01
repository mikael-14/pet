<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\MedicineType;
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
 * @property string|null $application
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Pet[] $pets
 * @property Collection|Prescription[] $prescriptions
 *
 * @package App\Models
 */
class Medicine extends Model
{
	use SoftDeletes;
	protected $table = 'medicines';

	protected $casts = [
		'active_ingredient' => 'json',
		'type' => MedicineType::class
	];

	protected $fillable = [
		'name',
		'type',
		'dosage',
		'active_ingredient',
		'application',
		'description'
	];

	public function pets()
	{
		return $this->belongsToMany(Pet::class, 'pet_has_medicines')
					->withPivot('id', 'dosage', 'status', 'administered', 'date', 'observation', 'person_id', 'prescription_has_medicine_id', 'deleted_at')
					->withTimestamps();
	}

	public function prescriptions()
	{
		return $this->belongsToMany(Prescription::class, 'prescription_has_medicines')
					->withPivot('id', 'dosage', 'status', 'frequency', 'emergency', 'start_date', 'end_date', 'observation', 'deleted_at')
					->withTimestamps();
	}
	
	public static function getAllActiveIngredientFormatted(): array
	{
		$array = Medicine::pluck('active_ingredient')->flatten()->toArray();
		$all_values = array_unique($array, SORT_STRING);
		return array_combine($all_values, $all_values);
	}
}
