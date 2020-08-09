<h4>
    新規登録画面
</h4>
<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>

<?= $this->render('junkissa/_edit_form', array('junkissa' => $junkissa, 'prefectures' => $prefectures, 'csrf_token' => $csrf_token)); ?>