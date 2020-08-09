<?php

namespace Framework;

class Session
{
    private static Session $_instance;
    protected static $session_started = false;
    protected static $session_id_regenerated = false;

    /**
     * コンストラクタ
     * セッションを開始する
     */
    public function __construct()
    {
        if (!self::$session_started) {
            session_start();
            self::$session_started = true;
        }
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * セッションに値を設定
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションから値を取得
     *
     * @param string $name
     * @param mixed $default 指定したキーが存在しない場合のデフォルト値
     * @return array
     */
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    /**
     * セッションから値を削除
     *
     * @param string $name
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションをすべてクリアする
     *
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * セッションIDを再生成する
     *
     * @param boolean $destroy trueの場合は古いセッションを破棄する
     */
    public function regenerate($destroy = true)
    {
        if (!self::$session_id_regenerated) {
            session_regenerate_id($destroy);

            self::$session_id_regenerated = true;
        }
    }
}