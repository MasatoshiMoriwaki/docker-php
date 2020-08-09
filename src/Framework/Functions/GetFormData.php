<?php

namespace Framework\Functions;
use Framework\DB\Entity;

trait GetFormData
{

    /**
     * フォームの入力データを取得する
     *
     * @param array $post_items     フォーム入力データ
     * @param array $editable_items 編集可能項目
     * @return array
     */
    public function getFormData($post_items , $editable_items)
    {
        $editable_values = [];
        foreach ($editable_items as $item) {
            // 編集可能な項目のみセット
            $input_item = $item[0];
            if(isset($post_items[$input_item])) {
                $editable_values[$input_item] = $post_items[$input_item];
                continue;
            } else {
                // チェックボックス未選択の場合、0で更新する
                if (isset($item[2]) && $item[2]) {
                    $editable_values[$input_item] = 0;
                }
            }
        }
        return $editable_values;
    }

    public function getArrayFormData($post_items_array , $editable_items)
    {
        $editable_values = [];
        foreach($post_items_array as $post_items) {
            $editable_values[] = $this->getFormData($post_items , $editable_items);
        }
        return $editable_values;
    }

    /**
     * フォームの入力データをエンティティにセットする
     *
     * @param Entity $entity
     * @param array $form_inputed_data
     * @return Entity
     */
    public function setFormDataToEntity(Entity $entity, $form_inputed_data = array())
    {
        foreach ($form_inputed_data as $item => $value) {
            if (in_array($item, $entity->columns())) {
                if ($item === 'password') {
                    $entity->{$item} = password_hash($value, PASSWORD_BCRYPT);
                    continue;
                }
                $entity->{$item} = $value;
            }
        }
        return $entity;
    }
}