<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 26 Apr 2018 14:11:24 +0800.
 */

namespace App\Models;

/**
 * Class Personality
 * 
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $remark
 * @property int $sort
 *
 * @package App\Models
 */
class Personality extends Common
{
	public $timestamps = false;

	protected $casts = [
		'status' => 'int',
		'sort' => 'int'
	];

	protected $fillable = [
		'name',
		'status',
		'remark',
		'sort'
	];

	static public $TYPES = [
	    'EI' => ['E','I'],
	    'SN' => ['S','N'],
	    'TF' => ['T','F'],
	    'JP' => ['J','P'],
    ];

    static public function deleteByMemberId($member_id)
    {
        $items = static::all()->toArray();
        foreach ($items as $item) {
            $row = MemberPersonalityGrade::where(['member_id'=>$member_id,'personality_id'=>$item['id']])->first();

            if ($row) {
                $row->where(['member_id'=>$member_id,'personality_id'=>$item['id']])->delete();
            }
        }

    }

    /**
     * 获取风格类型
     * @param $member_id
     * @return array
     */
    static public function getType($member_id)
    {
        $type_box = [];

        $name_box = [];

        $items = MemberPersonalityGrade::where(['member_id'=>$member_id])
            ->orderBy('grade', 'DESC')
            ->orderBy('weight', 'DESC')
            ->get();
        $personalities = static::getAllIndexById();

        foreach ($items as $item) {
            if (in_array($personalities[$item['personality_id']]['remark'], $type_box)) {
                continue;
            }
            $type_box[] = $personalities[$item['personality_id']]['remark'];
            $name_box[$personalities[$item['personality_id']]['id']] = $personalities[$item['personality_id']]['name'];
        }

        return $name_box;
    }

}
