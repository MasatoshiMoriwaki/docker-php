<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\AppBase\AppBaseController;
use App\Http\Forms\UserForm;
use App\Service\AuthService;
use App\Models\User;
use App\Entities\UserEntity;

class AuthController extends AppBaseController
{
    private $_email;
    private $_register_input;

    public function showLoginForm()
    {
        $show_tab = $this->request->getGetParam('show');
        return $this->render('auth/login', [
            'action'        => isset($show_tab) ? $show_tab : UserForm::ACTION_LOGIN,
            'csrf_token'    => $this->generateCsrfToken()
        ]);
    }

    public function login()
    {
        // POSTのみ
        if ($this->request->isPost()) {

            // CSRFトークンチェック
            if ($this->checkCsrfToken()) {
                // 入力値を取得
                $login_input = $this->request->getPostParam('login');
                $this->_email = $login_input['email'];

                // バリデーション
                if (($validateError = UserForm::validate($login_input, UserForm::ACTION_LOGIN)) !== true) {

                    // バリデーションエラー
                    return $this->render('auth/login', [
                        'csrf_token'    => $this->generateCsrfToken(),
                        'email'         => $this->_email,
                        'errors'        => $validateError
                    ]);
                }
                // ユーザ認証
                $failed_result = '';
                // メールアドレスでユーザ取得
                $user = AuthService::getUserByEmail($this->_email);
                if (is_null($user)) {
                    return $this->setLoginFaildResult();
                }

                // パスワード検証
                if (AuthService::login($user, $login_input['password'])) {
                    // ログイン成功
                    // マイページへリダイレクト
                    return $this->renderMyPage($user);
                } else {
                    // パスワード不一致
                    $failed_result = $this->setLoginFaildResult();
                }
                return $failed_result;
            }
        }
        return $this->showLoginForm();
    }

    public function logout()
    {
        AuthService::logout();

        return $this->redirect('/');
    }

    public function register()
    {
        // POSTのみ
        if ($this->request->isPost()) {

            // CSRFトークンチェック
            if ($this->checkCsrfToken()) {
                // 入力値を取得
                $this->_register_input = $this->request->getPostParam('register');

                // 登録前チェック
                $errors = $this->registerCheck();
                if (count($errors) > 0) {
                    return $this->setRegisterFaildResult($errors);
                }

                // 新規登録
                $user = new UserEntity();
                // エンティティにセット
                $inputed_user = $this->setFormDataToEntity($user, $this->_register_input);
                // 保存処理
                $user = User::save($inputed_user);
                if ($user === false ) {
                    //  TODO:: 例外発生させる
                    throw new \Exceptions;
                }
                // ログイン情報をセッションにセット
                AuthService::setLoginStatus($user);

                $this->setSessionMsg('info_msg', 'アカウントを登録しました！');
                // マイページへリダイレクト
                return $this->renderMyPage($user);
            }
        }
        return $this->redirect('/login?show=register');
    }

    private function registerCheck()
    {
        $errors = [];

        // バリデーション
        $validateError = UserForm::validate($this->_register_input, UserForm::ACTION_REGISTER);
        if ($validateError !== true) {
            $errors = $validateError;
        }
        // メールアドレスの重複チェック
        $user = AuthService::getUserByEmail($this->_register_input['email']);
        if (!empty($user)) {
            $errors = array_merge($errors, ['email' => array('このメールアドレスはすでに登録されています')]);
        }
        return $errors;
    }

    private function setLoginFaildResult()
    {
        $login_err = 'アカウントが登録されていないか、パスワードが正しくありません';
        $errors = ['login_err' => $login_err];

        $failed_result = $this->render('auth/login',
                                    [
                                        'action'        => UserForm::ACTION_LOGIN,
                                        'email'         => $this->_email,
                                        'errors'        => $errors,
                                        'csrf_token'   => $this->generateCsrfToken()
                                    ]);
        return $failed_result;
    }

    private function setRegisterFaildResult($errors)
    {
        $failed_result = $this->render('auth/login',
                                    [
                                        'action'        => UserForm::ACTION_REGISTER,
                                        'name'          => $this->_register_input['name'],
                                        'email'         => $this->_register_input['email'],
                                        'errors'        => $errors,
                                        'csrf_token'   => $this->generateCsrfToken()
                                    ]);
        return $failed_result;
    }

    private function renderMyPage($user)
    {
        $redirect_to = '/user/' . $user->id;
        return $this->redirect($redirect_to);
    }
}