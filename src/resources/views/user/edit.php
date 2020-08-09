<h4>
    ユーザ編集画面
</h4>
<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>


<?= $this->render('user/_common_user_form', array('user' => $user, 'errors' => $errors, 'csrf_token' => $csrf_token)); ?>