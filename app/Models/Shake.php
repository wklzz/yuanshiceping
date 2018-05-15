<?php

/**
 * Created by Reliese Model.
 * Date: Thu, 03 May 2018 10:08:29 +0800.
 */

namespace App\Models;

/**
 * Class Shake
 * 
 * @property int $id
 * @property string $name
 * @property int $status
 * @property string $remark
 * @property int $created_at
 * @property int $updated_at
 * @property int $sort
 * @property string $entry
 * @property int $interest_id
 * @property string $interest_name
 * @property string $potential_ids
 * @property string $potential_names
 *
 * @package App\Models
 */
class Shake extends Common
{
	protected $casts = [
		'status' => 'int',
		'created_at' => 'int',
		'updated_at' => 'int',
		'sort' => 'int',
		'interest_id' => 'int'
	];

	protected $fillable = [
		'name',
		'status',
		'remark',
		'sort',
		'entry',
		'interest_id',
		'interest_name',
		'potential_ids',
		'potential_names'
	];

	static protected $member_shake_potential_grades;
	static protected $member_interest_grades;


    static public function grade($member_id)
    {
        $shakes = static::getAllIndexById();
        $shake_grades = [];

        foreach ($shakes as $shake) {
            static::updateMemberShakePotentialGrade($shake, $member_id);
            $shake_grades[$shake['id']] = [
                'member_id' => $member_id,
                'shake_id' => $shake['id'],
                'grade' => 0,
                'weight' => $shake['sort'],
            ];
        }
        static::$member_shake_potential_grades = MemberShakePotentialGrade::where(['member_id'=>$member_id])
            ->orderBy('grade', 'DESC')
            ->orderBy('weight', 'DESC')
            ->get();
        static::$member_interest_grades = MemberInterestGrade::where(['member_id'=>$member_id])
            ->orderBy('grade', 'DESC')
            ->orderBy('weight', 'DESC')
            ->get();
        foreach ($shakes as $shake) {

            $shake_grades[$shake['id']]['grade'] = static::gradeOne($shake, $member_id);
        }
        static::deleteByMemberId($member_id);
        MemberShakeGrade::insert($shake_grades);
    }

    static public function updateMemberShakePotentialGrade($shake, $member_id)
    {
        $where = [
            'member_id' => $member_id,
            'shake_id' => $shake['id'],
            'potential_ids' => $shake['potential_ids']
        ];
        $member_shake_potential_grade = MemberShakePotentialGrade::where($where)->first();
        if (!$member_shake_potential_grade) {
            $member_shake_potential_grade = new MemberShakePotentialGrade();
            $member_shake_potential_grade->member_id = $member_id;
            $member_shake_potential_grade->shake_id = $shake['id'];
            $member_shake_potential_grade->potential_ids = $shake['potential_ids'];
            $member_shake_potential_grade->grade = static::getPotentialGrade($shake, $member_id);
            $member_shake_potential_grade->save();
        } else {
            $data = [
                'grade' => static::getPotentialGrade($shake, $member_id)
            ];
            $member_shake_potential_grade->where($where)->update($data);
        }


    }

    static public function gradeOne($shake, $member_id)
    {
        $potential_grade = static::getPotentialGrade($shake, $member_id);
        $potential_rank_grade = static::getPotentialRankGrade(static::$member_shake_potential_grades, $shake);
        $interest_rank_grade = static::getInterestRankGrade(static::$member_interest_grades, $shake);
        $grade = $potential_grade + $potential_rank_grade + $interest_rank_grade;
        $grade = round((($grade / 140) * 100), 1);
        return $grade;
    }

    static public function getPotentialGrade($shake, $member_id)
    {
        $len = strlen($shake['potential_ids']);
        $potential_grade = 0;
        for ($i = 0; $i < $len; $i++) {
            $where = [
                'member_id' => $member_id,
                'potential_id' => $shake['potential_ids'][$i]
            ];
            $member_potential_grade = MemberPotentialGrade::where($where)->first();
            if ($member_potential_grade) {
                $potential_grade += $member_potential_grade->grade;
            } else {
                var_dump($shake['potential_ids'][$i]);
                var_dump('没找着，潜能');
            }
        }
        $potential_grade = $potential_grade / $len;
        return $potential_grade;

    }

    static public function getPotentialRankGrade($member_shake_potential_grades, $shake)
    {
        //潜能排名后加一次分。1+10分；2+5；3+2；其他+0
        foreach ($member_shake_potential_grades as $key => $member_shake_potential_grade) {
            if ($shake['id'] == $member_shake_potential_grade->shake_id) {
                $sort = $key + 1;
                if ($sort == 1) {
                    return 10;
                } elseif ($sort == 2) {
                    return 5;
                } elseif ($sort == 3) {
                    return 2;
                }
            }
        }

        return 0;

    }

    static public function getInterestRankGrade($member_interest_grades, $shake)
    {
        //兴趣排名加分。1+10，2+10；3+8；4+2；5+1；6+0
        foreach ($member_interest_grades as $key => $member_interest_grade) {
            if ($shake['interest_id'] == $member_interest_grade->interest_id) {
                $sort = $key + 1;
                if ($sort == 1) {
                    return 10;
                } elseif ($sort == 2) {
                    return 10;
                } elseif ($sort == 3) {
                    return 8;
                } elseif ($sort == 4) {
                    return 2;
                } elseif ($sort == 5) {
                    return 1;
                }
            }
        }

        return 0;
    }

    static public function deleteByMemberId($member_id)
    {
        $items = static::all()->toArray();
        foreach ($items as $item) {
            $row = MemberShakeGrade::where(['member_id'=>$member_id,'shake_id'=>$item['id']])->first();

            if ($row) {
                $row->where(['member_id'=>$member_id,'shake_id'=>$item['id']])->delete();
            }
        }

    }
}
