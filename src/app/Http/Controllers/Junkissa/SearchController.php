<?php

namespace App\Http\Controllers\Junkissa;

use App\Http\Controllers\AppBase\AppBaseController;
use App\Models\Junkissa;
use App\Models\Mst\Area;

class SearchController extends AppBaseController
{
    private static $_input_items = ['keyword', 'prefecture', 'ordering', 'features', 'page'];

    public function index()
    {
        $params = $this->getSearchCondition();

        if(!$params) {
            $junkissas = Junkissa::getAll();
        } else {
            $junkissas = Junkissa::search($params);
        }

        return $this->render('junkissa/search', [
            'junkissas'     => $junkissas,
            'areas'         => Area::getAll(),
            'features'      => \App\Constant\Junkissa::SEARCH_FEATURES, // 絞り込み用の特徴
            'order_by'      => Junkissa::SEARCH_ORDERING,               // ソート
            // 'uri'           => $this->request->getRequestUri(),
            'params'        => $params
        ]);
    }

    private function getSearchCondition()
    {
        $conditions = array();
        foreach (self::$_input_items as $input) {
            if ($input_value = $this->request->getGetParam($input)) {
                $conditions[$input] = $input_value;
            }
        }
        if (empty($conditions['ordering'])) {
            foreach(Junkissa::SEARCH_ORDERING as $key => $value) {
                if($value[2] === 'selected') {
                    $conditions['ordering'] = $key;
                }
            }
        }

        return $conditions;
    }
}