<?php

namespace App\Models;

use Framework\DB\Model;
use App\Entities\ImageFileInfoEntity;

class ImageFileInfo extends Model
{
    public static $_table_name = 'image_file_infos';

    public static function getByKeyValue($key_type, $key_value)
    {
        self::instance();
        $sql = "select * from " . self::getTableName() . " where active = '1' and key_type = :key_type and key_value = :key_value order by seq asc";
        return self::fetchAll($sql, [':key_type' => $key_type, ':key_value' => $key_value]);
    }

    public static function upsert($upsert_rows)
    {
        self::instance();

        try {

            self::connect();
            // トランザクション開始
            self::$_pdo->beginTransaction();

            foreach ($upsert_rows as $row) {
                $is_update = (!empty($row->id)) ? true : false;
                $sql = '';

                try {

                    if ($is_update) {
                        // キャプションのみ更新
                        $sql = self::updateSql(array('caption'));
                        $stmt = self::$_pdo->prepare($sql);
                        $stmt = self::bindKeyValues($stmt, $row);
                        $stmt->bindValue(':caption', $row->caption, \PDO::PARAM_STR);
                        $stmt->execute();
                    } else {
                        // file_nameの変更がある場合(新規ファイルの追加 or 既存ファイルの差し替え)

                        // 既存レコードをupdate(inactive)
                        $sql = self::updateSql(array('active'));
                        $stmt = self::$_pdo->prepare($sql);
                        $stmt = self::bindKeyValues($stmt, $row);
                        $stmt->bindValue(':active', 0, \PDO::PARAM_INT);    // activeを0に更新
                        $stmt->execute();

                        // 新規レコードをinsert
                        $sql  = self::buildSqlInsert($row);
                        $stmt = self::$_pdo->prepare($sql);
                        $stmt = self::bindValueForInsert($stmt, $row);
                        $stmt->execute();
                    }

                }  catch (\PDOException $e) {

                    // ロールバック
                    self::$_pdo->rollBack();

                    throw $e;
                }
            }
            // コミット
            self::$_pdo->commit();
        }  catch (\PDOException $e) {
            return false;
        }
        return true;
    }

    private static function bindKeyValues($stmt, $row)
    {
        $stmt->bindValue(':key_type', $row->key_type, \PDO::PARAM_INT);
        $stmt->bindValue(':key_value', $row->key_value, \PDO::PARAM_INT);
        $stmt->bindValue(':seq', $row->seq, \PDO::PARAM_INT);
        return $stmt;
    }

    private static function updateSql(array $cols)
    {
        $table = self::getTableName();
        $update_columns = '';

        foreach ($cols as $col) {
            $update_columns .= $col . ' = :' . $col . ',';
        }

        $sql = <<<EOF
                update {$table}
                    set
                        {$update_columns}
                        updated_at = now()
                where key_type = :key_type and key_value = :key_value and seq = :seq and active = 1;
        EOF;

        return $sql;
    }
}
