<?php

namespace App\Http\Forms;

use Valitron\Validator;
use App\Models\Mst\Prefecture;

class JunkissaForm
{
    public static function editableItems()
    {
        return  [
                    /*** [input_name,           label_name,             ,(未選択時)0で更新するか] ***/
                    ['name',                    '名前'],
                    ['prefecture_id',           '都道府県'],
                    ['address_1',               '住所1'],
                    ['address_2',               '住所2'],
                    ['address_3',               '住所３'],
                    ['access_info',             'アクセス'],
                    ['phone_number',            '電話番号'],
                    ['web_page',                'ホームページ'],
                    ['business_hours',          '営業時間'],
                    ['regular_holiday',         '定休日'],
                    ['remark',                  '備考'],
                    ['menu_info',               'メニュー情報'],
                    ['year_of_establishment',   '創業年'],
                    ['type_of_management',      '経営形態'],
                    ['has_home_roasting',       '自家焙煎'          , true],
                    ['has_paper_drip',          'ペーパードリップ'  , true],
                    ['has_nel_drip',            'ネルドリップ'      , true],
                    ['has_siphon',              'サイフォン'        , true],
                    ['has_french_press',        'フレンチプレス'    , true],
                    ['has_aero_press',          'エアロプレス'      , true],
                    ['has_espresso',            'エスプレッソ'      , true],
                    ['has_cold_brew',           '水出し'            , true],
                    ['has_light_meal',          '軽食'              , true],
                    ['has_morning_set',         'モーニングセット'  , true],
                    ['has_dessert',             'デザート'          , true],
                    ['has_alcohol',             'アルコール'        , true],
                    ['has_smoking_seat',        '喫煙席'            , true],
                    ['has_comic',               '漫画'              , true],
                    ['has_classical_music',     '名曲'              , true],
                    ['has_game_machine',        'ゲーム台'          , true],
                    ['has_karaoke',             'カラオケ'          , true],
                ];
    }

    public static function validate($input_values = array())
    {
        Validator::lang("ja");
        $_validator = new Validator($input_values);

        // ルールを設定
        self::setValidateRules($_validator, $input_values);

        // ラベル名を設定
        $labels =[];
        foreach (self::editableItems() as $item) {
            $labels[$item[0]] = $item[1];
        }
        $_validator->labels($labels);

        // エラーチェック
        if ($_validator->validate()) {
            return true;
        } else {
            $errors = $_validator->errors();
        }
        return $errors;
    }

    private static function setValidateRules($_validator, $input_values)
    {
        // 名前
        $_validator->rule('required', 'name')->message('必須項目です');
        $_validator->rule('lengthMax', 'name', 100);
        // 都道府県
        if (empty($input_values['prefecture_id'])) {
            // 必須チェック
            $_validator->rule('required', 'prefecture_id')->message('{field}を選択してください');
        } else {
            // 範囲チェック
            $_validator->rule('in', 'prefecture_id', array_column(Prefecture::getAll(), 'id'))->message('リストから正しく選択してください');
        }
        // 文字数チェック
        $_validator->rule('lengthMax', ['address_1', 'address_2', 'address_3', 'regular_holiday'], 40);
        $_validator->rule('lengthMax', ['access_info', 'business_hours'], 400);
        $_validator->rule('lengthMax', ['remark', 'menu_info'], 1000);
        $_validator->rule('lengthMax', 'web_page', 100);
        // 電話番号形式チェック
        $_validator->rule('regex', 'phone_number', "/^0\d{1,3}-\d{1,4}-\d{1,4}$/")->message('{field}を正しく入力してください');
        // URL形式チェック
        $_validator->rule('url', 'web_page');
        // 創業年
        $_validator->rule('in', 'year_of_establishment', \App\Constant\Junkissa::YEARS_ARRAY())->message('リストから正しく選択してください');
        // 経営タイプ
        $_validator->rule('in', 'type_of_management', array('0', '1','2'))->message('正しく選択してください');
        // チェックボックス系
        $check_items = ['has_home_roasting',
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
                        'has_karaoke'];
        $_validator->rule('in', $check_items, array('0','1'))->message('正しく選択してください');
    }

}