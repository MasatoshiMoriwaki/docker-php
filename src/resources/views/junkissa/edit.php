
<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>

<?  if(!isset($is_edit_images)) {
     echo $this->render('junkissa/_edit_form', array('junkissa' => $junkissa, 'prefectures' => $prefectures, 'errors' => $errors, 'csrf_token' => $csrf_token));
    } else {
     echo $this->render('junkissa/_images_form', array('junkissa' => $junkissa, 'images' => $images, 'errors' => $errors));
    }
?>