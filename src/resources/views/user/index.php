
<h4>
    ユーザページです
</h4>

<div>
    <div class="">
        <a href="<?= $base_url ?>/user/me/edit">プロフィールを編集する</a>
    </div>
</div>

<div class="list-item">
    <? if ($user->image()) {?>
        <div class="user-icon" >
            <img src="<?=e($user->image->file_name)?>" style="width: 5rem;">
        </div>
    <? }?>
    <table>
        <tbody>
            <tr>
                <th>ユーザ名</th>
                <td><?= e($user->name) ?></td>
            </tr>
            <tr>
                <th>自己紹介</th>
                <td><?= e($user->profile) ?></td>
            </tr>
            <tr>
                <th>リンク</th>
                <td><?= e($user->web_page) ?></td>
            </tr>
            <tr>
                <th>登録日時</th>
                <td><?= e($user->created_at) ?></td>
            </tr>
            <tr>
                <th>更新日時</th>
                <td><?= e($user->updated_at) ?></td>
            </tr>
        </tbody>
    </table>
</div>
