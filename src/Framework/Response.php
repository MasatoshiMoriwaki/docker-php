<?php

namespace Framework;

class Response
{
    protected $content;
    protected $status_code = 200;
    protected $status_text = 'OK';
    protected $http_headers = array();

/**
 * レスポンスを送信
 */
    public function send()
    {
        header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_text);

        if (isset($this->http_headers)) {
            foreach ($this->http_headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        echo $this->content;
    }

    /**
     * コンテンツを設定
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * ステータスコードを設定
     *
     * @param integer $status_code
     * @param string $status_text
     */
    public function setStatusCode($status_code, $status_text = '')
    {
        $this->status_code = $status_code;
        $this->status_text = $status_text;
    }

    /**
     * HTTPレスポンスヘッダを設定
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setHttpHeaders($name, $value)
    {
        $this->http_headers[$name] = $value;
    }

    public static function redirect($location)
    {
        $response = new self('', 301);
        $response->setHttpHeaders('Location', $location);
        return $response;
    }
}