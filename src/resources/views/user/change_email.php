<h4>
    メールアドレス変更
</h4>
<?= msgDisplay($this->getMessage(MSG_TYPE_ERROR)) ?>

<div class="list-item">

    <form method="POST" action=""  class="" accept-charset="UTF-8">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="btn-submit">
            <button type="submit" class="">確認メールを送信する</button>
        </div>

        <table>
            <tbody>
                <tr>
                    <th>メールアドレス</th>
                    <td>
                        <?= err($errors, 'email') ?>
                        <div class="<?= isErr($errors, 'email') ?>">
                            <input type="email" name="user_edit[email]" value="<?= e($email) ?>">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

</div>
