<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AppBase\AppBaseController;
use App\Models\Junkissa;
use App\Models\Mst\Area;

class TopController extends AppBaseController
{
    public function index()
    {
        // 新着の純喫茶を取得
        $conditions['ordering'] = 'created_at_desc';
        $junkissas = Junkissa::search($conditions);

        return $this->render('top', [
            'junkissas'     => $junkissas,
            'areas'         => Area::getAll(),
            'features'      => \App\Constant\Junkissa::SEARCH_FEATURES,
        ]);
    }

}