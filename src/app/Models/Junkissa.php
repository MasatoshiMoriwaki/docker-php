<?php

namespace App\Models;

use Framework\DB\Model;
use App\Entities\JunkissaEntity;

class Junkissa extends Model
{
    public static $_table_name = 'junkissas';

    // キーワード検索条件
    public static $_keyword_target = [
                                    "(name like :keyword)\n",
                                    "(address_1 like :keyword)\n",
                                    "(address_2 like :keyword)\n",
                                    "(address_3 like :keyword)\n",
                                    "(access_info like :keyword)\n",
                                    "(remark like :keyword)\n",
                                    "(menu_info like :keyword)\n",
                                    "(prefecture_id in (select id from _mst_prefectures where active = '1' and name like :keyword))\n"
                                ];
    // 更新対象外カラム
    protected static $_columns_non_target_insert = [];
    // 検索 : ソート順
    const SEARCH_ORDERING = array(
                                    'created_at_desc'       => ['新しく登録された順',   'created_at desc',  ''],
                                    'created_at_asc'        => ['古く登録された順',     'created_at asc',   ''],
                                    'updated_at_desc'       => ['新しく更新された順',   'updated_at desc',  'selected'],
                                    'updated_at_asc'        => ['古く更新された順',     'updated_at asc',   ''],
                                );
    // where句
    private static $where_phrase = '';

    /**
     * 検索処理
     *
     * @param array $params
     * @return void
     */
    public static function search($params = array())
    {
        self::instance();

        self::buildSearchWherePhrase($params);

        $sql = self::buildSearchSql($params);
        $stmt = self::connect()->prepare($sql);
        $stmt = self::bindValueSearchCondition($stmt, $params);
        $stmt->execute();
        $junkissas = $stmt->fetchAll(\PDO::FETCH_CLASS, self::$_entity_class);
        self::getCount($junkissas, $params);
        return $junkissas;
    }

    public static function getAll()
    {
        self::instance();
        $sql = "select * from " . self::getTableName() . " where active = '1'";
        $sql .= self::orderBy();
        return self::fetchAll($sql);
    }

    private static function buildSearchSql($params)
    {
        $base_sql = "select * from " . self::$_table_name . " where active = '1'";
        $order_by = self::orderBy($params);
        $limit = "\n limit :start, " . JUNKISSAS_PER_PAGE;

        return  $base_sql . self::$where_phrase . $order_by . $limit;
    }

    public static function getCount($junkissas, $params)
    {
        if (!empty($junkissas)) {
            $sql = "select count(*) as 'total_count' from " . self::$_table_name . " where active = '1' " . self::$where_phrase;
            $stmt = self::connect()->prepare($sql);
            $stmt = self::bindValueSearchCondition($stmt, $params, false);

            $stmt->execute();
            $count = $stmt->fetch(\PDO::FETCH_COLUMN);
            $junkissas[0]->total_count = $count;
        }
    }

    /**
     * where句を構築
     */
    private static function buildSearchWherePhrase($params)
    {
        $where = '';
        foreach ($params as $key => $values) {
            switch ($key) {

                case 'prefecture' :
                    $in_values = '';
                    for ($i = 1; $i <= count($values); $i++) {
                        $in_values = $in_values . ', :' . 'prefecture_id_' . (string)$i; // :prefecture_id_1, :prefecture_id_2, :prefecture_id_3 ...
                    }
                    $in_values = substr($in_values, 2);
                    $where = $where . " and (prefecture_id in ({$in_values}))";
                    break;

                case 'keyword' :
                    foreach (self::$_keyword_target as $i => $tgt) {
                        if ($i === 0) {
                            $where .= " and ( ";
                        } else {
                            $where .= "      or ";
                        }
                        $where .=  str_replace(':keyword', ':keyword_' . $i, $tgt);
                        if ($i === count(self::$_keyword_target) -1) {
                            $where .= " ) ";
                        }
                    }
                    break;

                case 'features' :
                    $features = \App\Constant\Junkissa::SEARCH_FEATURES;

                    $checked_count_in_feature = 0;
                    foreach ($features as $key => $items)
                    {
                        $checked_count_in_this_type = 0;
                        foreach ($items['items'] as $item){

                            if (in_array($key . '#' . $item['value'], $values)) {

                                $condition = $item['search_condition'];

                                if ($checked_count_in_this_type === 0) {
                                    $where .= "\n and ( ";
                                } else {
                                    $where .= "\n\t\t or ";
                                }

                                $where .= $condition;

                                $checked_count_in_feature++;
                                $checked_count_in_this_type++;

                                // valuesのリストから削除
                                $values = array_values(array_diff($values, array($key . '#' . $item['value'])));
                            }
                        }
                        if ($checked_count_in_this_type > 0) {
                            $where .= ")";
                        }
                    }
                    break;
                default:
                    break; // do nothing
            }
        }
        self::$where_phrase = $where;
    }

    private static function bindValueSearchCondition($stmt, $params, $limit = true)
    {
        foreach ($params as $key=>$values) {
            switch ($key) {

                case 'prefecture' :
                    for ($i = 1; $i <= count($values); $i++) {
                        $stmt->bindValue(':prefecture_id_' . (string)$i, (int)$values[$i-1], \PDO::PARAM_INT);
                    }
                    break;

                case 'keyword' :
                    $i = 0;
                    while ($i < count(self::$_keyword_target)) {
                        $stmt->bindValue(":keyword_{$i}", '%' . addcslashes($values, '\_%') . '%', \PDO::PARAM_STR);
                        $i +=1;
                    }
                    break;

                default:
                    break; // do nothing
            }
        }

        if ($limit) {
            $page = (isset($params['page']) ? (int)$params['page'] : 1);
            $stmt->bindValue(':start', ($page - 1) * JUNKISSAS_PER_PAGE, \PDO::PARAM_INT);
        }

        return $stmt;
    }

    private static function orderBy($params = '')
    {
        $order_by = "\n order by ";

        if (isset($params['ordering'])) {
            foreach (self::SEARCH_ORDERING as $key => $value) {
                if ($params['ordering'] === $key) {
                    return $order_by .= $value[1];
                }
            }
        }

        foreach (self::SEARCH_ORDERING as $item) {
            if ($item[2] === 'selected') {
                return $order_by .= $item[1];
            }
        }
    }
}
