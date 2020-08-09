<?php

namespace App\Http\Forms;

use Valitron\Validator;

class ImageFileInfoForm
{
    public static function editableItems()
    {
        return  [
                    /*** [input_name,           label_name] ***/
                    ['caption',                'キャプション']
                ];
    }
    private static $_separator = '***';

    public static function validate($input_values = array())
    {
        Validator::lang("ja");

        $input_array = self::toSingleArray($input_values);

        $_validator = new Validator($input_array);

        // ルールを設定
        self::setValidateRules($_validator, array_keys($input_array));

        // ラベル名を設定
        $labels =[];
        foreach (self::editableItems() as $item) {
            for ($i = 0; $i < count($input_array); $i++) {
                $labels[$item[0] . self::$_separator . $i] = $item[1];
            }
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

    private static function setValidateRules($_validator, $items)
    {
        // 文字数チェック
        $_validator->rule('lengthMax', $items, 20);
    }

    private static function toSingleArray($input_values)
    {
        $result = array();
        foreach ($input_values as $key => $item) {
            $result[array_keys($item)[0] . self::$_separator . $key] = array_values($item)[0];
        }
        return $result;
    }

}