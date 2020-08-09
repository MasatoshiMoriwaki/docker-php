<?php

namespace Framework;

use Framework\Router;
use Framework\Exceptions\HttpNotFoundException;
use Framework\Exceptions\UnauthorizedActionException;

class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $router;

    // ルーティング定義
    protected $routing_map;
    protected $routing_tree;
    // アプリケーション設定
    protected $conf;
    protected $namespace_controller;
    /**
     * コンストラクタ
     *
     * @param boolean $debug
     */
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    /**
     * デバッグモードを設定
     *
     * @param boolean $debug
     */
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    // アプリケーションの初期化
    protected function initialize()
    {
        $this->request      = new Request();
        $this->response     = new Response();
        $this->session      = new Session();
        $this->router       = new Router();
    }

    /**
     * アプリケーションの設定
     */
    protected function configure()
    {
        // 設定ファイルを読み込み
        $this->conf = require __DIR__ . '/../App/Config/conf.php';

        // ルーティング情報設定
        $this->registerRoutesTree($this->conf['web']);
        $this->namespace_controller = ($this->conf['namespace_controller']);
    }

    /**
     * デバッグモードか判定
     *
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debug;
    }
    /**
     * ルーティング情報を登録
     *
     * @return array
     */
    public function registerRoutesTree(array $routing_map)
    {
        // ルータにて探索用ツリー構築
        $this->routing_tree = $this->router->trieConstruction($routing_map);
    }

    /**
     * システム(src)のルートディレクトリを設定
     */
    public function getRootDir()
    {
        return $this->conf['system_root_dir'];
    }

    /**
     * アプリケーション(app)のディレクトリを設定
     */
    public function getAppDir()
    {
        return $this->getRootDir() . '/app';
    }

    /**
     * Requestオブジェクトを取得
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Responseオブジェクトを取得
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sessionオブジェクトを取得
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * コントローラファイルが格納されているディレクトリへのパスを取得
     *
     * @return string
     */
    public function getControllerDir()
    {
        return $this->getAppDir() . '/Http/Controllers';
    }
    /**
     * モデルファイルが格納されているディレクトリへのパスを取得
     *
     * @return stirng
     */
    public function getModelDir()
    {
        return $this->getAppDir() . '/models';
    }

    /**
     * ドキュメントルートへのパスを取得
     *
     * @return string
     */
    public function getWebDir()
    {
        return $this->getRootDir() . '/public';
    }

    /**
     * ビューファイルが格納されているディレクトリへのパスを取得
     *
     * @return string
     */
    public function getViewDir()
    {
        return $this->getRootDir() . '/resources/views';
    }

    /**
     * アプリケーションを実行する
     */
    public function run()
    {
        try {
            // ルーティングツリーから探索
            $params = $this->router->search($this->routing_tree, $this->request->getPathInfo(), $this->request-> getRequestMethod());
            // コントローラの情報が得られない場合
            if ($params === false) {
                throw new Exceptions\HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $action_info = isset($params['value']) ? explode("@", $params['value']) : null;
            $controller_class = isset($action_info[0]) ? $action_info[0] : null;
            $controller_action = isset($action_info[1]) ? $action_info[1] : null;
            // コントローラのアクションを実行する
            $this->runAction($controller_class, $controller_action, $params);

            // TODO データ更新の例外をキャッチしたらシステムエラー画面へリダイレクト
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e){
            // ログイン画面へリダイレクト
            header('Location:'. $this->request->getBaseUrl() . '/login', true, 301);
            exit;
        }

        $this->response->send();
    }

    /**
     * 指定されたアクションを実行する
     *
     * @param string $controller_name
     * @param string $action
     * @param array $params
     *
     * @throws HttpNotFoundException コントローラが特定できない場合
     */
    public function runAction($controller_class, $controller_action, $params = array())
    {
        $controller = $this->findController($controller_class);

        if ($controller === false) {
            throw new Exceptions\HttpNotFoundException($controller_class . ' controller is not found.');
        }

        // コントローラのアクションを実行し、レンダリングされたコンテンツを受け取る
        $content = $controller->run($controller_action, $params);
        // レスポンスにコンテンツを設定
        $this->response->setContent($content);
    }

    /**
     * 指定されたコントローラ名から対応するControllerオブジェクトを取得
     *
     * @param string $controller_class
     * @return Controller
     */
    public function findController($controller_class)
    {
        $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
        $controller_class = $this->namespace_controller . str_Replace('/' , '\\', $controller_class);

        if (is_readable($controller_file) && class_exists($controller_class)) {
            return new $controller_class($this);
        }
        return false;
    }

    /**
     * 404エラー画面を返す設定
     *
     * @param Exception $e
     */
    protected function render404Page($e) {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}