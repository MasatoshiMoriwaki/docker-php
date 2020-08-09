
<div class="list-item">

    <form method="POST" action=""  class="" accept-charset="UTF-8" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="btn-submit">
            <button type="submit" class="">保存する</button>
        </div>

        <table>
            <tbody>
                <tr>
                    <th>アイコン</th>
                    <td>
                        <?= err($errors, 'image_0') ?>
                        <div class="<?= isErr($errors, 'image_0') ?>">
                            <? if ($user->image()) {?>
                                <div class="user-icon" >
                                    <img src="<?=e($user->image->file_name)?>" style="width: 5rem;">
                                </div>
                            <? }?>
                            <input accept="image/jpeg,image/png,image/gif" class="" name="images[0][bin]" type="file">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>ユーザ名</th>
                    <td>
                        <?= err($errors, 'name') ?>
                        <div class="<?= isErr($errors, 'name') ?>">
                            <input type="text" name="user_edit[name]" value="<?= e($user->name) ?>">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td>
                        <div class="">
                            <?= e($user->email) ?>
                            <p>メールアドレスの変更は<a href="<?=$base_url?> /email/change">こちら</a></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>自己紹介</th>
                    <td>
                        <?= err($errors, 'profile') ?>
                        <div class="<?= isErr($errors, 'profile') ?>">
                            <textarea name="user_edit[profile]" cols="50" rows="5"><?= e($user->profile) ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>リンク</th>
                    <td>
                        <?= err($errors, 'web_page') ?>
                        <div class="<?= isErr($errors, 'web_page') ?>">
                            <input type="text" name="user_edit[web_page]" value="<?= e($user->web_page) ?>">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>
