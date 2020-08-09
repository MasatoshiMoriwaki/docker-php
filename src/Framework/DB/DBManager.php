<?php

namespace Framework\DB;

use App\Config\DBSettings;

class DBManager
{
    private static DBManager $instance;
    public \PDO $connection;
    private array $models_table = array();

    public function __construct()
    {
        $this->initialize();
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param  string $model_key
     * @return Model
     */
    public function model($model_key)
    {
        return $this->models_table[$model_key];
    }

    public function registerModels(array $models_table)
    {
        $this->models_table = array_merge($this->models_table, $models_table);
    }

    /**
     * 初期化(DB接続)
     * @return void
     */
    private function initialize()
    {
        try {
            $dsn = $this->createDsn();

            $this->connection = new \PDO(
                $dsn,
                DBSettings::DB_USER,
                DBSettings::DB_PASS
            );

            // オプションを設定
            foreach (DBSettings::DB_OPTIONS as $option) {
                $this->connection->setAttribute($option[0], $option[1]);
            }

        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * コネクション用DSNを構築する
     */
    private function createDsn()
    {
        $dsn = DBSettings::DB_DRIVER . ':' .
            'dbname=' . DBSettings::DB_NAME . ';' .
            'host=' . DBSettings::DB_HOST . ';' .
            'charset=' . DBSettings::DB_CHARSET;
        return $dsn;
    }
}