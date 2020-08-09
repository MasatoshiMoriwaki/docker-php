<?php

namespace App\Constant;

class Junkissa
{
    // 検索 : 特徴
    const SEARCH_FEATURES = array(
        'manage_info'       =>  [
                                    'head_label'    => '経営タイプ',
                                    'items'         =>  array(
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'manage_type_personal',
                                                                'item_label'        => '個人店',
                                                                'search_condition'  => 'type_of_management = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'manage_type_chain',
                                                                'item_label'        => 'チェーン店',
                                                                'search_condition'  => 'type_of_management = 2'
                                                                )
                                                        )
                                ],
        'menu_info'         =>  [
                                    'head_label'    => 'メニュー',
                                    'items'         =>  array(
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'light_meal',
                                                                'item_label'    => '軽食',
                                                                'search_condition' => 'has_light_meal = 1'
                                                                ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'morning_set',
                                                                'item_label'    => 'モーニング',
                                                                'search_condition' => 'has_morning_set = 1'
                                                            ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'dessert',
                                                                'item_label'    => 'デザート',
                                                                'search_condition' => 'has_dessert = 1'
                                                            ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'alcohol',
                                                                'item_label'    => 'アルコール',
                                                                'search_condition' => 'has_alcohol = 1'
                                                            )
                                                        )
                                ],
        'coffee_info'       =>  [
                                    'head_label'    => 'コーヒー',
                                    'items'         =>  array(
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'home_roasting',
                                                                'item_label'        => '自家焙煎',
                                                                'search_condition'  => 'has_home_roasting = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'paper_drip',
                                                                'item_label'        => 'ペーパードリップ',
                                                                'search_condition'  => 'has_paper_drip = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'nel_drip',
                                                                'item_label'        => 'ネルドリップ',
                                                                'search_condition'  => 'has_nel_drip = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'siphon',
                                                                'item_label'        => 'サイフォン',
                                                                'search_condition'  => 'has_siphon = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'french_press',
                                                                'item_label'        => 'フレンチプレス',
                                                                'search_condition'  => 'has_french_press = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'aero_press',
                                                                'item_label'        => 'エアロプレス',
                                                                'search_condition'  => 'has_aero_press = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'espresso',
                                                                'item_label'        => 'エスプレッソ',
                                                                'search_condition'  => 'has_espresso = 1'
                                                            ),
                                                            array(
                                                                'input_type'        => 'checkbox',
                                                                'value'             => 'cold_brew',
                                                                'item_label'        => '水出し',
                                                                'search_condition'  => 'has_cold_brew = 1'
                                                            ),
                                                        )
                                ],
        'in_store_info'     =>  [
                                    'head_label'    => '店内',
                                    'items'         =>  array(
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'classical_music',
                                                                'item_label'    => 'クラシック・名曲',
                                                                'search_condition' => 'has_classical_music = 1'
                                                                ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'comic',
                                                                'item_label'    => '漫画',
                                                                'search_condition' => 'has_comic = 1'
                                                            ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'game_machine',
                                                                'item_label'    => 'ゲーム台',
                                                                'search_condition' => 'has_game_machine = 1'
                                                            ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'smoking_seat',
                                                                'item_label'    => '喫煙席',
                                                                'search_condition' => 'has_smoking_seat = 1'
                                                            ),
                                                            array(
                                                                'input_type'    => 'checkbox',
                                                                'value'         => 'karaoke',
                                                                'item_label'    => 'カラオケ',
                                                                'search_condition' => 'has_karaoke = 1'
                                                            )
                                                        )
                                ]
                            );

    public static function YEARS_ARRAY()
    {
        for ($i = 1900; $i <= date('Y'); $i++) {
            $y_list[] = $i;
        }
        return $y_list;
    }

    public static function MANAGEMENT_TYPE_ARRAY()
    {
        return array(
                    1 => '個人店',
                    2 => 'チェーン店',
        );
    }
}