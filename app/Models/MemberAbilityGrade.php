<?php

/**
 * Created by Reliese Model.
 * Date: Tue, 15 May 2018 10:19:32 +0800.
 */

namespace App\Models;

use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class MemberAbilityGrade
 * 
 * @property int $member_id
 * @property int $ability_id
 * @property float $grade
 * @property int $weight
 *
 * @package App\Models
 */
class MemberAbilityGrade extends Eloquent
{
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'member_id' => 'int',
		'ability_id' => 'int',
		'grade' => 'float',
		'weight' => 'int'
	];

	protected $fillable = [
		'grade',
		'weight'
	];
}
