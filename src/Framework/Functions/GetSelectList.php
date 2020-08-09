<?php

namespace Framework\Functions;

trait GetSelectList
{
    /**
     * セレクトボックス用のマスタデータ取得
     *
     * @param String $model_name
     * @return array
     */
    public function getSelectList($model_name)
    {
        // セレクトボックス
        $mst_data = $model_name::getAll();
        return array_column($mst_data, 'name', 'id');
    }
}