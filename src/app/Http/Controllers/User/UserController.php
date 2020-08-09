<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AppBase\AppBaseController;
use App\Models\User;
use App\Entities\UserEntity;
use App\Http\Forms\UserForm;
use App\Service\AuthService;
use App\Service\ImageFileInfoService;

class UserController extends AppBaseController
{
    protected $auth_actions = array('edit', 'save');

    public function index($param)
    {
        $user = User::getById($param['user_id']);
        if (empty($user)) {
            $this->forward404();
        }

        // 保存→リダイレクト時にメッセージ表示
        $this->getSessionMsg('info_msg');

        return $this->render('user/index', [
            'user'      => $user
        ]);
    }

    public function edit()
    {
        // 編集
        $user = AuthService::getLoginUser();
        if (empty($user)) {
            throw new UnauthorizedActionException();
        }

        // 画面表示
        return $this->viewEdit($user);
    }

    public function save()
    {
        // CSRFトークンチェック
        if (!$this->checkCsrfToken()) {
             // リダイレクト
             $this->redirectToEdit();
        }

        // ユーザidを取得
        $user = new UserEntity();
        $user->id = $this->session->get(AuthService::AUTH_ID_KEY);
        if (is_null($user->id)) {
            // リダイレクト
            $this->redirectToEdit();
        }

        // 入力項目を取得
        $input_values = $this->getInputValues();
        // エンティティにセット
        $inputed_user = $this->setFormDataToEntity($user, $input_values);

        // バリデーション
        $result_validate = UserForm::validate($input_values, UserForm::ACTION_EDIT);
        // アイコン画像保存
        $is_check_only = $result_validate !== true ? true : false;

        $result_icon_upload = ImageFileInfoService::uploadImageFiles(IMAGE_KEY_TYPE_USER, $user->id, array(), $is_check_only);
        if ($result_validate !== true || $result_icon_upload !== true) {
            // エラーメッセージ設定
            $this->setErrMessage($result_icon_upload);
            $errors = array();
            if (is_array($result_validate)) {
                $errors = $result_validate;
            }
            if (is_array($result_icon_upload)) {
                $errors = array_merge($errors, $result_icon_upload);
            }
            return $this->viewEdit($inputed_user, $errors);
        }

        // 保存処理
        $user = User::save($inputed_user);

        if ($user === false ) {
            //  model内のエラー
            $this->setDataCommitErrMessage();
            return $this->viewEdit($inputed_user);
        }

        $this->setSessionMsg('info_msg', 'ユーザ情報を更新しました！');

        $this->redirect('/user/' . $user->id);
    }

    public function changeEmail()
    {
        // 編集
        $user = AuthService::getLoginUser();
        if (empty($user)) {
            $this->forward404();
        }

        // 画面表示
        return $this->viewEmailChange();
    }

    public function sendConfirmationEmail()
    {
        // POSTのみ
        if ($this->request->isPost()) {
            // CSRFトークンチェック
            if (!$this->checkCsrfToken()) {
                // リダイレクト
                $this->redirectToEdit();
            }
            // ログインユーザ取得
            $user = AuthService::getLoginUser();
            if (empty($user)) {
                $this->forward404();
            }
            // 入力メールアドレス取得
            $email = $this->request->getPostParam('user_edit')['email'];
            // メール変更前チェック
            $errors = $this->emailChangeCheck();
            if (count($errors) > 0) {
                // 画面表示
                return $this->viewEmailChange($email, $errors);
            }

            // メール変更処理
            return $this->emailChangeProc($user, $email);
        }
    }

    public function activateNewEmail()
    {
        // 確認コード
        $verification_code = $this->request->getGetParam('code');

        // Userモデルで確認コード & 有効期限のチェック
        if (!User::activateNewEmail($verification_code, EMAIL_VERIFY_EXPIRATION_MINITES)) {
            $this->forward404();
        }
        echo '新規メールアドレスを有効化しました';
    }

    private function viewEdit($user, $errors = array())
    {
        return $this->render('user/edit', [
            'user'          => $user,
            'errors'        => $errors,
            'csrf_token'    => $this->generateCsrfToken()
        ]);
    }

    private function viewEmailChange($email = null, $errors = array())
    {

        return $this->render('user/change_email', [
            'email'         => $email,
            'errors'        => $errors,
            'csrf_token'    => $this->generateCsrfToken()
        ]);
    }

    private function redirectToEdit()
    {
        $redirect_to = '/user/me/edit';
        return $this->redirect($redirect_to);
    }

    private function emailChangeCheck()
    {
        $errors = [];

        // 入力メールアドレス取得
        $post_items = $this->request->getPostParam('user_edit');
        // バリデーション
        $validateError = UserForm::validate($post_items, UserForm::ACTION_EMAIL_CHANGE);
        if ($validateError !== true) {
            $errors = $validateError;
        }
        // メールアドレスの重複チェック
        $user = AuthService::getUserByEmail($post_items['email']);
        if (!empty($user)) {
            $errors = array_merge($errors, ['email' => array('このメールアドレスはすでに登録されています')]);
        }
        return $errors;
    }

    private function emailChangeProc($user, $new_email)
    {
        // 認証コード生成
        $verification_code = $this->generateVerificationCode();
        // 更新情報をセット
        $user->new_email = $new_email;
        $user->new_email_verification_code = $verification_code;

        // ユーザ情報を更新
        if (User::saveNewEmailInfo($user) === false ) {
            //  データ更新エラー
            $this->setDataCommitErrMessage();
            return $this->viewEdit($inputed_user);
        }

        // メール送信
        // TODO SMTP送信
        $to = $new_email;
        $subject = "TEST";
        $message = "This is TEST.\r\nClick here.\r\n" . SITE_BASE_URL . '/email/change/verify?code=' . $verification_code;
        $headers = "From: from@example.com";
        if (!mail($to, $subject, $message, $headers)) {
            // メール送信失敗
            $this->setFaildSendEmailErrMessage();
            // 画面表示
            return $this->viewEmailChange($new_email);
        }
        echo 'メール送信しました';

    }

    private function getInputValues()
    {
        // POSTされた項目を取得
        $post_items = $this->request->getPostParam('user_edit');
        $editable_items = UserForm::editableItems();
        return $this->getFormData($post_items, $editable_items);
    }

    private function generateVerificationCode()
    {
        $code = sha1(uniqid(mt_rand(), true));
        if (!User::checkVerificationCodeIsUnique($code)) {
            $this->generateVerificationCode();
        }
        return $code;
    }
}