<?php

namespace Framework\DB;

use Framework\DB\DBManager;
use Framework\Exceptions\DataOparationException;
use Framework\Exceptions\DBAccessException;

class Model
{
    protected static \PDO $_pdo;
    protected static Model $instance;
    protected static $_table_name;
    protected static string $_entity_class;
    protected static $_columns_non_target_insert = [];    // insert対象外カラム
    protected static $_columns_non_target_update = [];    // update対象外カラム

    protected function entityClass()
    {
        return null;
    }

    public static function instance()
    {
        $table_name = self::getTableName();
        $entity_class = self::getEntityClass();
        self::$instance = new self($table_name, $entity_class);
    }

    /**
     * コンストラクタ
     * 外部から直接newされる場合                     : 引数なし
     * Modelクラス内部のinstanceで self new する場合 : 引数あり
     */
    public function __construct($table_name = null, $entity_class = null)
    {
        $class_name = (new \ReflectionClass(get_called_class()));

        self::$_table_name = is_null($table_name) ? $this->getTableName() : $table_name;
        self::$_entity_class = is_null($entity_class) ? $this->entityClass() : $entity_class;
        if (is_null(self::$_entity_class)) {
            throw new \InvalidArgumentException('$this->entity_class is required not string.');
        }
        $this->_entity = new self::$_entity_class;
        self::$_columns_non_target_insert = $this->_entity->nonInsertColumns();
        self::$_columns_non_target_update = $this->_entity->nonUpdateColumns();
    }

    public static function getById($id)
    {
        self::instance();
        $result = self::where('id', '=', $id);

        return empty($result[0]) ? null : $result[0];
    }

    public static function where($column, $operator, $value)
    {
        self::instance();
        $sql = "select * from " . self::getTableName() . " where active = '1' and {$column} {$operator} :value";
        return self::fetchAll($sql, [':value' => $value]);
    }

    public static function getAll()
    {
        self::instance();
        $sql = "select * from " . self::getTableName() . " where active = '1'";
        return self::fetchAll($sql);
    }

    public static function save($entity)
    {
        self::instance();

        $insert_flg = (empty($entity->id)) ? true : false;    // true:Inset, false:Update
        $sql = '';

        try {

            $sql = self::buildSqlForSave($entity, $insert_flg);
            self::connect();
            $stmt = self::$_pdo->prepare($sql);

            // トランザクション開始
            self::$_pdo->beginTransaction();

            try {

                $stmt = self::bindValueForSave($stmt, $entity, $insert_flg);

                $stmt->execute();

                if ($insert_flg) {
                    // insertしたidを取得
                    $entity->id = self::$_pdo->lastInsertId();
                }
                // コミット
                self::$_pdo->commit();

            }  catch (\PDOException $e) {

                // ロールバック
                self::$_pdo->rollBack();

                throw $e;
            }

        }  catch (\PDOException $e) {
            return false;
        }

        return self::getById($entity->id);
    }

    public static function buildSqlForSave($entity, $insert_flg)
    {
        if (!$insert_flg) {
            return self::buildSqlUpdate($entity);
        } else {
            return self::buildSqlInsert($entity);
        }
    }

    public static function bindValueForSave($stmt, $entity, $insert_flg)
    {
        if (!$insert_flg) {
            return self::bindValueForUpdate($stmt, $entity);
        } else {
            return self::bindValueForInsert($stmt, $entity);
        }
    }

     /**
      * クエリを実行
      *
      * @param string $sql
      * @param array $params
      */
    public static function execute($sql, $params = [])
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function connect()
    {
        if (empty(self::$_pdo)) {

            try {
                self::$_pdo = DBManager::instance()->connection;
            } catch (\PDOException $e) {
                throw new DBAccessException('Failed to connect to database');
            }
        }

        return self::$_pdo;
    }
    /**
     * クエリを実行し、結果を1行取得
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function fetch($sql, $params = array())
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetch(\PDO::FETCH_ASSOC, self::$_entity_class);
    }

    /**
     * クエリを実行し、結果をすべて取得
     *
     * @param string $sql
     * @param array $params
     * @return array(Entity)
     */
    public static function fetchAll($sql, $params = array())
    {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::$_entity_class);
    }

    public function _columns()
    {
        return $this->_entity_class::columns();
    }

    public function _common_columns()
    {
        return $this->_entity_class::commonColumns();
    }

    private static function buildSqlUpdate($entity)
    {
        $sql = "update " . self::$_table_name . " set \n";

        foreach ($entity->specificColumns() as $column) {
            if(in_array($column, self::$_columns_non_target_update, true)) {
                continue;
            }
            $key = self::_to_param($column);
            $sql .= " {$column} = {$key},\n";
        }

        $sql .= self::_setTimestampForUpdate('updated_at');
        $sql .= " where id = :id";

        return $sql;
    }

    private static function bindValueForUpdate($stmt, $entity)
    {
        foreach ($entity->specificColumns() as $column){
            if(in_array($column, self::$_columns_non_target_update, true)) {
                continue;
            }
            $value = $entity->$column;
            $data_type = \PDO::PARAM_STR;
            if ($value === "") {
                $data_type = \PDO::PARAM_NULL;
            } elseif(is_int($value)){
                $data_type = \PDO::PARAM_INT;
            }

            $stmt->bindValue(
                ":{$column}",
                ($value === "") ? null : $value,
                $data_type
                );
        }

        $stmt->bindValue(":id", (int)$entity->id, \PDO::PARAM_INT);

        return $stmt;
    }

    protected static function buildSqlInsert($entity)
    {
        // insert対象のカラムを取得
        $columns_array = $entity->specificColumns();
        if(count(self::$_columns_non_target_insert) > 0) {
            // insert対象外のカラムを除外
            $columns_array = array_diff($columns_array, self::$_columns_non_target_insert);
            $columns_array = array_values($columns_array);
        }

        $into_columns = implode(', ', $columns_array) . ', created_at, updated_at';
        $values_columns = implode(', ', self::_to_params($columns_array)) . ', now(), now()';
        $sql = "insert into " . self::$_table_name . " ({$into_columns}) values ({$values_columns})";

        return $sql;
    }

    protected static function bindValueForInsert($stmt, $entity)
    {
        foreach ($entity->specificColumns() as $column){
            if(in_array($column, self::$_columns_non_target_insert, true)) {
                continue;
            }
            $value = $entity->$column;
            $data_type = \PDO::PARAM_STR;
            if ($value === "") {
                $data_type = \PDO::PARAM_NULL;
            } elseif(is_int($value)){
                $data_type = \PDO::PARAM_INT;
            }

            $stmt->bindValue(
                ":{$column}",
                ($value === "") ? null : $value,
                $data_type
                );
        }

        return $stmt;
    }

    private static function _to_param(string $key): string
    {
        return ':' . $key;
    }

    private static function _to_params(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[] = self::_to_param($key);
        }
        return $result;
    }

    private static function _setTimestampForUpdate($column)
    {
        return " {$column} = now()\n";
    }

    public static function getTableName()
    {
        return static::$_table_name;
    }

    public static function getEntityClass()
    {
        $model_class_name = get_called_class();
        $entity_namespace = str_replace('Models', 'Entities', $model_class_name);
        return $entity_namespace . 'Entity';
    }
}