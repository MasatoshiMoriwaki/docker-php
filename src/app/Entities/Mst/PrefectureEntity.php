<?php

namespace App\Entities\Mst;

use Framework\DB\Entity;
use App\Entities\Mst\AreaEntity as AreaEntity;

class PrefectureEntity extends Entity
{
    public $name;
    public $name_en;
    public $area_id;

    public $area;

    public static function specificColumns()
    {
        return [
                'name',
                'name_en',
                'area_id'
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