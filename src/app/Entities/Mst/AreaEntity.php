<?php

namespace App\Entities\Mst;

use Framework\DB\Entity;
use App\Models\Mst\Prefecture;

class AreaEntity extends Entity
{
    public $name;
    public $name_en;

    public $prefectures;

    public static function specificColumns()
    {
        return [
                'name',
                'name_en'
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
    public function prefectures()
    {
        return $this->prefectures = Prefecture::where('area_id', '=', $this->id);
    }
}