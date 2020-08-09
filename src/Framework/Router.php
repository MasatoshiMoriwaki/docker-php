<?php

namespace Framework;

final class Router
{
    private static $VALID_STATE_MARK = '>';
    private static $URL_PARAM_NAME = 'name';

    /**
     * ルーティング決定のための探索を行う
     *
     * @param array $trie
     * @param string $request_uri
     * @param string $http_method
     */
    public static function search($trie, $request_uri, $http_method)
    {
        $p = $trie[$http_method];

        $length = strlen($request_uri);
        $i = 0;
        $ok = (0 < $length);
        $result = [];
        while ($i < $length) {
            if ($request_uri[$i] !== '/') {
                $ok = false;
                break;
            }
            $str = '' . $request_uri[$i++];
            $num_only = true;
            while ($i < $length && $request_uri[$i] !== '/') {
                $str .= $request_uri[$i];
                $x = ord($request_uri[$i]);
                $num_only &= (48 <= $x && $x <= 57);    // ASCIIコードで判定(0~9)
                $i++;
            }
            if (isset($p[$str])) {
                $p = $p[$str];
            } elseif ($num_only && isset($p[URL_PARAM_TYPE_NUM])) {
                $p = $p[URL_PARAM_TYPE_NUM];
                $result[$p[self::$URL_PARAM_NAME]] = substr($str, 1);
            } elseif (isset($p[URL_PARAM_TYPE_STR])) {
                $p = $p[URL_PARAM_TYPE_STR];
                $result[$p[self::$URL_PARAM_NAME]] = substr($str, 1);
            } else {
                $ok = false;
                break;
            }
        }
        if ($ok && isset($p[self::$VALID_STATE_MARK])) {
            $action_info = array();
            $action_info = ['value' => $p[self::$VALID_STATE_MARK], 'params' => $result];
            return $action_info;
        }

        return $ok && isset($p[self::$VALID_STATE_MARK]) ? ['value' => $p[self::$VALID_STATE_MARK], 'params' => $result] : null;
    }

    /**
     * Trie木を表現した連想配列を構築する
     *
     * @param array $conf
     * @return array
     */
    public static function trieConstruction(array $conf)
    {
        $trie = [];
        foreach ($conf as $con) {
            $http_method = $con[0];
            $path = $con[1];
            $value = $con[2];
            $param_mapping = isset($con[3]) ? $con[3] : [];

            if (!isset($trie[$http_method])) {
                $trie[$http_method] = [];
            }

            $node = &$trie[$http_method];

            $path_length = strlen($path);
            $i = 0;
            while ($i < $path_length) {
                if ($path[$i++] !== '/') {
                    throw new \Exception(sprintf("不正なパスが設定されています %s", $path));
                }
                $partial_path = '/';
                while ($i < $path_length && $path[$i] !== '/') {
                    $partial_path .= $path[$i++];
                }

                $is_url_parameter = false;
                $url_param_name = null;
                // URLパラメータだった場合
                if (1 < strlen($partial_path) && $partial_path[1] === ':') {
                    $is_url_parameter = true;
                    $url_param_name = substr($partial_path, 2);
                    if (!isset($param_mapping[$url_param_name])) {
                        throw new \Exception(sprintf("URLパラメータ :%s に対する設定が足りません", $url_param_name));
                    }
                    $partial_path = $param_mapping[$url_param_name];
                }

                if (!isset($node[$partial_path])) {
                    if ($is_url_parameter) {
                        $node[$partial_path] = [self::$URL_PARAM_NAME => $url_param_name];
                    } else {
                        $node[$partial_path] = [];
                    }
                } else {
                    if ($is_url_parameter && $node[$partial_path][self::$URL_PARAM_NAME] !== $url_param_name) {
                        throw new \Exception(sprintf("URLパラメータに別名をつけようとしています %s (:%s, :%s)", $path, $url_param_name, $node[$partial_path][self::$URL_PARAM_NAME]));
                    }
                }
                $node = &$node[$partial_path];
            }
            if (isset($node[self::$VALID_STATE_MARK])) {
                throw new \Exception(sprintf("重複したルーティングルールがあります %s", $path));
            }
            $node[self::$VALID_STATE_MARK] = $value;
        }
        return $trie;
    }
}