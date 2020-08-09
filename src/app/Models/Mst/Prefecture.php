<?php

namespace App\Models\Mst;

use Framework\DB\Model;
use App\Entities\Mst\PrefectureEntity;

class Prefecture extends Model
{
    public static $_table_name = '_mst_prefectures';

    public function entityClass()
    {
        return PrefectureEntity::class;
    }
}