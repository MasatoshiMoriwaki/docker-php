<?php

namespace App\Entities;

use Framework\DB\Entity;

class ImageFileInfoEntity extends Entity
{
    // テーブル固有のカラム
    public $key_type;
    public $key_value;
    public $seq;
    public $file_name;
    public $caption;
    public $created_by;
    public $updated_by;

    // テーブル固有のカラム
    public static function specificColumns()
    {
        return [
                    'key_type',
                    'key_value',
                    'seq',
                    'file_name',
                    'caption',
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

}