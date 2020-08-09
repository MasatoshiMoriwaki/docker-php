
<h4>
    検索結果ですー
</h4>
<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>

<?= $this->render('junkissa/_search_form', array('areas' => $areas, 'features' => $features, 'params' => $params, 'order_by' => $order_by)); ?>

<h3>純喫茶 検索結果一覧</h3>

<? if($junkissas) {?>
    <div class="search-result-count">
        <h4 class="result_count">検索結果:<?= e($junkissas[0]->total_count)?>件</h4>
    </div>
<?} else {?>
    <p>条件に該当する純喫茶は見つかりませんでした。<br>検索条件を変更して、再度検索してみてください。</p>
<?}?>

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
                更新日時：<?= e($junkissa->updated_at) ?>
            </p>
            </p>
            <p>
                登録日時：<?= e($junkissa->created_at) ?>
            </p>
        </a>
    </div>
<? } ?>

<?= pagination($junkissas) ?>
