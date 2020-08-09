<?php

namespace Framework\DB;

abstract class Entity
{
    public string $id = '';

    public $active;
    public $created_at;
    public $updated_at;

    public $self;
    public $relation_entity;

    public function __construct()
    {
        return $this->columns();
    }

    public static function commonColumns()
    {
        return [
                'active',
        ];
    }

    public static function timestampColumns()
    {
        return [
            'created_at',
            'updated_at'
        ];
    }

    // テーブル固有のカラム
    public abstract static function specificColumns();

    /**
     * テーブル内の全カラムを配列で持つ
     * array_merge([id], self::specificColumns(), self::commonColumns())
     * @return array
     */
    public abstract static function columns();

    // insert対象外のカラム
    public function nonInsertColumns()
    {
        return [];
    }

    // update対象外のカラム
    public function nonUpdateColumns()
    {
        return [];
    }

    /**
     * 外部テーブル取得
     *
     * @param string $target_entity
     * @return void
     */
    public function getRelationEntity($target_entity)
    {
        $key_value = $this->{$target_entity . '_id'};
        if ($key_value) {
            return ucfirst($target_entity)::getById($key_value);
        }
    }

}