
<div class="search-form">

    <form id="search-form" action="<?= $base_url ?>/search" method="GET">

        <div class="prefectures">
            <?= areaCheckbox($areas, $params) ?>
        </div>
        <div class="features">
        <?  $lbl_idx = 1;
            foreach($features as $key => $items) {?>

            <div class="<?= $key ?>">
                <h3 class="featureItem_head"><?= $items['head_label']?></h3>
                <div class="featureItem_body">

                    <? foreach($items['items'] as $item) {
                        $id = 'lb_features_' . $lbl_idx;
                        $val = $key . '#' . $item['value'];
                        $inputed_state = '';
                        if (isset($params['features'])) {
                            $inputed_state = in_array($val, $params['features']) ? 'checked="checked"' : '';
                        }
                    ?>
                        <div class="">
                            <?= "<input id='{$id}'  name='features[]' type='{$item['input_type']}' value='{$val}' {$inputed_state}>" ?>
                            <?= "<label for='{$id}'>{$item['item_label']}</label>" ?>
                        </div>
                    <?
                        $lbl_idx +=1;
                    }?>

                </div>
            </div>
        <?}?>

        </div>

        <div class="keyword">
            <p>キーワード</p>
            <input placeholder="喫茶名、エリアなど" name="keyword" type="text" value="<?= isset($params['keyword']) ? e($params['keyword']) : null?>">
        </div>

        <? if(isset($params['ordering'])) { ?>
        <div class="order-by">
            <p>並び順</p>
            <select name="ordering">
                <option value="">選択する</option>
                <? foreach ( $order_by as $key => $value ) { ?>
                    <option value="<?= $key ?>" <?= isset($params['ordering'])
                                                        && ($key === $params['ordering']) ? 'selected="selected"' : '' ?>><?= e($value[0]) ?></option>
                <? } ?>
            </select>
        </div>
        <? }?>

        <div class="btn-submit">
            <button type="submit" class="">検索</button>
        </div>

    </form>

</div>