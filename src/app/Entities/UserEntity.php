<?php

namespace App\Entities;

use Framework\DB\Entity;
use App\Service\ImageFileInfoService;

class UserEntity extends Entity
{
    // テーブル固有のカラム
    public $name;
    public $profile;
    public $web_page;
    public $email;
    public $password;
    public $new_email;
    public $new_email_verification_code;
    public $new_email_registered_at;
    public $last_login_at;

    // リレーションのあるエンティティ
    public $image;

    // テーブル固有のカラム
    public static function specificColumns()
    {
        return [
                    'name',
                    'profile',
                    'web_page',
                    'email',
                    'password',
                    'new_email',
                    'new_email_verification_code',
                    'new_email_registered_at',
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
                    'profile',
                    'web_page',
                    'new_email',
                    'new_email_verification_code',
                    'new_email_registered_at'
                ];
    }

    // update対象外のカラム
    public function nonUpdateColumns()
    {
        return [
                    'email',
                    'password',
                    'new_email',
                    'new_email_verification_code',
                    'new_email_registered_at'
                ];
    }

    public function image()
    {
        $images = ImageFileInfoService::getStoredImageFileInfos(IMAGE_KEY_TYPE_USER, $this->id);
        return $this->image = isset($images[0]) ? $images[0] : null;
    }

}