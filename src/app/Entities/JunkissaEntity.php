<?php

namespace App\Entities;

use Framework\DB\Entity;
use App\Models\Mst\Prefecture;

class JunkissaEntity extends Entity
{
    // テーブル固有のカラム
    public $name;
    public $prefecture_id;
    public $address_1;
    public $address_2;
    public $address_3;
    public $access_info;
    public $phone_number;
    public $web_page;
    public $business_hours;
    public $regular_holiday;
    public $remark;
    public $menu_info;
    public $year_of_establishment;
    public $type_of_management;
    public $has_home_roasting;
    public $has_paper_drip;
    public $has_nel_drip;
    public $has_siphon;
    public $has_french_press;
    public $has_aero_press;
    public $has_espresso;
    public $has_cold_brew;
    public $has_light_meal;
    public $has_morning_set;
    public $has_dessert;
    public $has_alcohol;
    public $has_smoking_seat;
    public $has_comic;
    public $has_classical_music;
    public $has_game_machine;
    public $has_karaoke;
    public $created_by;
    public $updated_by;

    public $closed_flg;

    // リレーションのあるエンティティ
    public $prefecture;
    // 検索の結果件数
    public $total_count;

    // テーブル固有のカラム
    public static function specificColumns()
    {
        return [
                    'name',
                    'prefecture_id',
                    'address_1',
                    'address_2',
                    'address_3',
                    'access_info',
                    'phone_number',
                    'web_page',
                    'business_hours',
                    'regular_holiday',
                    'remark',
                    'menu_info',
                    'year_of_establishment',
                    'type_of_management',
                    'has_home_roasting',
                    'has_paper_drip',
                    'has_nel_drip',
                    'has_siphon',
                    'has_french_press',
                    'has_aero_press',
                    'has_espresso',
                    'has_cold_brew',
                    'has_light_meal',
                    'has_morning_set',
                    'has_dessert',
                    'has_alcohol',
                    'has_smoking_seat',
                    'has_comic',
                    'has_classical_music',
                    'has_game_machine',
                    'has_karaoke',
                    'closed_flg',
                    'created_by',
                    'updated_by'
                ];
    }

    public static function columns()
    {
        return array_merge(
            ['id'],
            self::specificColumns(),
            self::commonColumns(),
            self::timestampColumns(),
        );
    }

    // insert対象外のカラム
    public function nonInsertColumns()
    {
        return [
                'closed_flg'
                ];
    }

    // update対象外のカラム
    public function nonUpdateColumns()
    {
        return [
                'closed_flg'
                ];
    }

    public function prefecture()
    {
        return $this->prefecture = Prefecture::getById($this->prefecture_id);
    }
}