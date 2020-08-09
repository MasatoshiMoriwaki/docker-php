<h4>
    トップですー
</h4>

<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>

<div class="search-form">

    <form id="search-form" action="<?= $base_url ?>/search" method="GET">
        <div class="prefectures">
            <?= areaCheckbox($areas) ?>
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

        <div class="btn-submit">
            <button type="submit" class="">検索</button>
        </div>

    </form>

</div>
<h3>
    新着の純喫茶
</h3>
<? foreach($junkissas as $junkissa) { ?>
    <div class="list-item">
        <a href="<?= $base_url ?>/junkissa/<?= e($junkissa->id) ?>">
            <p>
                店名：<?= e($junkissa->name) ?>
            </p>
            <p>
                都道府県：<?= ($junkissa->prefecture()) ? e($junkissa->prefecture->name) : "" ?>
            </p>
            <p>
                更新日時<?= e($junkissa->updated_at) ?>
            </p>
            <p>
                登録日時<?= e($junkissa->created_at) ?>
            </p>
        </a>
    </div>
<? } ?>
