<h4>
    純喫茶画像の編集
</h4>


<div class="file-upload-form">

    <label><?= e($junkissa->name) ?></label>

    <form action="" method="POST" accept-charset="UTF-8" enctype="multipart/form-data">
            <?= createJunkissaImagesForm($images, $errors) ?>

            <input type="submit" value="ファイルをアップロードする">
    </form>

</div>