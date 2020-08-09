<?php

namespace App\Http\Forms;

use Valitron\Validator;

class UserForm
{
    const ACTION_LOGIN              = 'login';
    const ACTION_REGISTER           = 'register';
    const ACTION_EDIT               = 'edit';
    const ACTION_EMAIL_CHANGE       = 'email_change';

    public static function labels()
    {
        return  [
                    /** [item_name, label_name] **/
                    ['name',                  'ユーザ名'],
                    ['email',                 'メールアドレス'],
                    ['password',              'パスワード'],
                    ['profile',               '自己紹介'],
                    ['web_page',              'リンク'],
                ];
    }

    public static function editableItems()
    {
        return  [
                    /** [input_name, label_name] **/
                    ['name',                  'ユーザ名'],
                    ['profile',               '自己紹介'],
                    ['web_page',              'リンク'],
                ];
    }

    public static function validate($input_values = array(), $action)
    {
        Validator::lang("ja");
        $_validator = new Validator($input_values);

        // ルールを設定
        self::setValidateRules($_validator, $input_values, $action);

        // ラベル名を設定
        $labels =[];
        foreach (self::labels() as $item) {
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

    private static function setValidateRules($_validator, $input_values, $action)
    {

        if ($action === self::ACTION_LOGIN || $action === self::ACTION_REGISTER || $action === self::ACTION_EMAIL_CHANGE) {

            // (新規登録時)パスワード
            if ($action === self::ACTION_REGISTER) {

                $_validator->rule('required', 'password');
                if (!empty($input_values['password'])) {
                    // 形式チェック
                    if (strlen($input_values['password'] > 32)) {
                        $_validator->rule('lengthMax', 'password', 32);
                    } else {
                        $_validator->rule('regex', 'password', "/\A[a-z\d]{8,32}+\z/i")->message('パスワードは半角英数字8文字以上で入力してください');
                    }
                }
            }

            // メールアドレス
            if (empty($input_values['email'])) {
                // 必須チェック
                $_validator->rule('required', 'email');
            } else {
                // 形式チェック
                $_validator->rule('email', 'email')->message('{field}の形式が正しくありません');
            }
        }

        // ユーザ名
        if ($action === self::ACTION_REGISTER || $action === self::ACTION_EDIT) {
            $_validator->rule('required', 'name');
            $_validator->rule('lengthMax', 'name', 20);
        }

        if ($action === self::ACTION_EDIT) {
            $_validator->rule('lengthMax', 'profile', 300);
            $_validator->rule('lengthMax', 'web_page', 100);

            // URL形式チェック
            $_validator->rule('url', 'web_page');
        }
    }

}