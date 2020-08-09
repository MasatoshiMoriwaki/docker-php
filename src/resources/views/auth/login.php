<h4>
    ログイン画面です
</h4>

<div class="container">
    <ul class="tab_buttons">
        <li class="tab_button"><a href="#" class="js-tabTrigger <?= (!isset($action) || $action === App\Http\Forms\UserForm::ACTION_LOGIN ) ? 'active-section' : '' ?>" data-id="login">ログイン</a></li>
        <li class="tab_button"><a href="#" class="js-tabTrigger <?= (isset($action) && $action === App\Http\Forms\UserForm::ACTION_REGISTER ) ? 'active-section' : '' ?>" data-id="register">新規登録</a></li>
    </ul>

    <div class="tab_contents">

        <section class="tab-content <?= (!isset($action) || $action === App\Http\Forms\UserForm::ACTION_LOGIN ) ? 'active-section' : '' ?>" id="login">

            <?= err($errors, 'login_err') ?>

            <form action="/login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="login-email">
                    <p>メールアドレス</p>
                    <?= err($errors, 'email') ?>
                    <div class="<?= isErr($errors, 'email') ?>">
                        <input name="login[email]" type="text" value="<?= isset($email) ? e($email) : '' ?>">
                    </div>
                </div>

                <div class="login-password">
                    <p>パスワード</p>
                    <?= err($errors, 'password') ?>
                    <div class="<?= isErr($errors, 'password') ?>">
                        <input name="login[password]" type="password">
                    </div>
                </div>

                <div class="btn-submit">
                    <button type="submit" class="">ログイン</button>
                </div>
            </form>
        </section>

        <section class="tab-content <?= (isset($action) && $action === App\Http\Forms\UserForm::ACTION_REGISTER ) ? 'active-section' : '' ?>" id="register">

            <form action="/register" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <div class="register-name">
                    <p>ユーザ名</p>
                    <?= err($errors, 'name') ?>
                    <div class="<?= isErr($errors, 'name') ?>">
                        <input name="register[name]" type="text" value="<?= isset($name) ? e($name) : '' ?>">
                    </div>
                </div>
                <div class="register-email">
                    <p>メールアドレス</p>
                    <?= err($errors, 'email') ?>
                    <div class="<?= isErr($errors, 'email') ?>">
                        <input name="register[email]" type="text" value="<?= isset($email) ? e($email) : '' ?>">
                    </div>
                </div>

                <div class="register-password">
                    <p>パスワード</p>
                    <?= err($errors, 'password') ?>
                    <div class="<?= isErr($errors, 'password') ?>">
                        <input name="register[password]" type="password">
                    </div>
                </div>

                <div class="btn-submit">
                    <button type="submit" class="">登録する</button>
                </div>
            </form>
        </section>

    </div>
</div>
