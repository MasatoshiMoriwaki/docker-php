<?php

namespace Framework;

use Framework\Exceptions\HttpNotFoundException;
use Framework\Exceptions\DataOparationException;
use Framework\Exceptions\UnauthorizedActionException;
use Framework\Functions\GetFormData;
use Framework\Functions\GetSelectList;
use Framework\Functions\SetMessage;

abstract class Controller
{
    use GetFormData;
    use GetSelectList;
    use SetMessage;

    protected $controller_name;
    protected $action_method;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $auth_actions = array();
    protected $login_user;
    private $_errors;
    public $messages = [];
    public $base_url;

    /**
     * コンストラクタ
     *
     * @param Application $application
     */
    public function __construct($application)
    {
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->base_url    = $this->request->getBaseUrl();
    }

    /**
     * アクションを実行
     *
     * @param string $action
     * @param array $params
     * @return string レスポンスとして返すコンテンツ
     *
     * @throws UnauthorizedActionException 認証が必須なアクションに認証前にアクセスした場合
     */
    public function run($action, $params = array())
    {
        $this->action_method = $action;

        if (!method_exists($this, $this->action_method)) {
            $this->forward404();
        }

        // ログイン対象のアクションか、ログイン済みか
        if ($this->needsAuthentication($action) && !\App\Service\AuthService::isAuthenticated()) {
            throw new UnauthorizedActionException();
        }

        // メソッドを実行
        $content = $this->$action($params['params']);
        return $content;
    }

    /**
     * ビューファイルのレンダリング
     *
     * @param string $template ビューファイル名(空の場合はアクション名を使う)
     * @param array $variables テンプレートに渡す変数の連想配列
     * @return string レンダリングしたビューファイルの内容
     */
    protected function render($template = null, $variables = array(), $use_layout = true)
    {
        $defaults = array(
            'request'       => $this->request,
            'base_url'      => $this->base_url,
            'session'       => $this->session,
            'login_user'    => $this->login_user,
            'messages'      => $this->messages
        );
        $view = new View($this->application->getViewDir(), $defaults);

        if (empty($template)) {
            $template = $this->action_method;
        }
        $path = $template;

        if ($use_layout) {
            $layout = 'layouts/app';
        } else {
            $layout = null;
        }

        return $view->render(
            $path,
            $variables,
            $layout
        );
    }

    /**
     * 404エラーを出力
     *
     * @throws HttpNotFoundException
     */
    protected function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from '
            . $this->controller_name . '\\' . $this->action_method);
    }

    /**
     * 指定されたURLへリダイレクト
     *
     * @param string $url
     */
    protected function redirect($url)
    {
        if (!preg_match('#https?://#', $url)) {
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $base_url = $this->base_url;

            $url = $protocol . $host . $base_url . $url;
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeaders('Location', $url);
    }

    /**
     * CSRFトークンを生成/セット
     *
     * @return string $token
     */
    protected function generateCsrfToken($key = 'csrf_token')
    {
        $token = $this->random(40);
        $this->session->set($key, $token);

        return $token;
    }

    /**
     * CSRFトークンが妥当かチェック
     *
     * @param string $form_name
     * @param string $token
     * @return boolean
     */
    protected function checkCsrfToken($key = 'csrf_token')
    {
        $post_token = filter_input(INPUT_POST, $key);
        $sess_token = $this->session->get($key);
        if (!is_null($sess_token) && !is_null($post_token) && $sess_token === $post_token) {
            return true;
        }
        return false;
    }

    /**
     * トークン文字列生成
     *
     * @param integer $length
     * @return void
     */
    public function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * 指定されたアクションが認証済みでないとアクセスできないか判定
     *
     * @param string $action
     * @return boolean
     */
    protected function needsAuthentication($action)
    {
        if ($this->auth_actions === true
            || (is_array($this->auth_actions) && in_array($action, $this->auth_actions))
        ) {
            return true;
        }

        return false;
    }

    protected function setMessage($type, $msg)
    {
        $message = new \stdClass();
        $message->type = $type;
        $message->msg  = $msg;
        $this->messages[] = $message;
    }

    /**
     * セッションにメッセージをセット
     */
    protected function setSessionMsg($name, $msg)
    {
        $this->session->set($name, $msg);
    }

    /**
     * セッションからメッセージを取得
     */
    protected function getSessionMsg($name)
    {
        if ($info_msg = $this->session->get($name)) {
            $this->setMessage(MSG_TYPE_INFO, $info_msg);
            $this->session->remove($name);
        }
    }

    protected function hasError() {
        return !empty(get_object_vars($this->_errors));
    }
}