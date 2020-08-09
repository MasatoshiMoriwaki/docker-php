
<header class="header">
    <div class="header_content">
        <div class="header_inner">
            <nav class="globalNav">
                <ul class="globalNav_links">
                    <li class="globalNav_link">
                        <a href="<?=$base_url?>/">ホーム</a>
                    </li>
                    <li class="globalNav_link">
                        <a href="<?=$base_url?>/search">検索</a>
                    </li>
                    <li class="globalNav_link">
                        <a href="<?=$base_url?>/junkissa/new">純喫茶登録</a>
                    </li>
                </ul>
            </nav>

            <div class="actionNav">

                <? if ($login_user) { ?>
                    <a href="<?=$base_url . '/user/' . $login_user->id ?>"><?= e($login_user->name) ?></a>
                    <? if ($login_user->image()) {?>
                        <img src="<?=e($login_user->image()->file_name)?>" style="width: 2rem;">
                    <? } ?>

                    <a href="<?=$base_url?>/logout">ログアウト</a>
                <? } else { ?>
                    <a href="<?=$base_url?>/login">ログイン</a>
                    <a href="<?=$base_url?>/login?show=register">新規登録</a>
                <? } ?>

            </div>
        </div>

    </div>

</header>