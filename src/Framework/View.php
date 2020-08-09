<?php

namespace Framework;

class View
{
    protected $base_dir;
    protected $defaults;
    protected $messages;
    protected $layout_variables = array();

    /**
     * コンストラクタ
     *
     * @param string $base_dir
     * @param array $defaults
     */
    public function __construct($base_dir, $defaults = array())
    {
        $this->base_dir = $base_dir;
        $this->defaults = $defaults;
        $this->messages = $defaults['messages'];
    }

    /**
     * ビューファイルをレンダリング
     *
     * @param string $_path
     * @param array $_variables
     * @param mixed $_layout
     * @return string
     */
    public function render($_path, $_variables = array(), $_layout = false)
    {
        // 表示するコンテンツを設定
        $_file = $this->base_dir . '/' . $_path . '.php';

        extract(array_merge($this->defaults, $_variables));

        // エラーが設定されてない場合
        isset($errors) ? $errors : $errors = array();

        ob_start();
        ob_implicit_flush(0);
        require $_file;

        $content = ob_get_clean();

        // レイアウトファイルの読み込み
        if ($_layout) {
            $content = $this->render(
                            $_layout,
                            array_merge($this->layout_variables, array(
                                '_content' => $content,
                                )));
        }

        return $content;
    }

    /**
     * レイアウトに渡す変数を指定
     *
     * @param string $name
     * @param mixed $value
     */
    public function setLayoutVar($name, $value)
    {
        $this->layout_variables[$name] = $value;
    }

    public function getMessage($type)
    {
        $msgArray = null;
        foreach (array_keys(array_column($this->messages, 'type'), $type) as $i) {
            $msgArray[] = $this->messages[$i];
        }
        return $msgArray;
    }
}