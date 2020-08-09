<?php

namespace App\Models\Mst;

use Framework\DB\Model;
use App\Entities\Mst\AreaEntity;

class Area extends Model
{
    public static $_table_name = '_mst_areas';

    public function entityClass()
    {
        return AreaEntity::class;
    }
}