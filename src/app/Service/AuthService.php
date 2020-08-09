<?php

namespace App\Service;

use Framework\Session;
use App\Models\User;

class AuthService
{
    const AUTHENTICATED_KEY = '_authenticated';
    const AUTH_ID_KEY = '_auth_id';

    public static function getLoginUser($is_secure = true)
    {
        $user = User::getById(self::_session()->get(self::AUTH_ID_KEY));
        if (empty($user)) {
            return $user;
        }
        if ($is_secure === false) {
            return $user;
        }
        $user->password = '';
        return $user;
    }

    public static function getUserByEmail($email)
    {
        return User::getByEmail($email);
    }

    public static function login($user, $password)
    {
        if (password_verify($password, $user->password) === false) {
            return false;
        }
        self::setLoginStatus($user);
        return true;
    }

    public static function setLoginStatus($user)
    {
        self::_session()->set(self::AUTHENTICATED_KEY, true);
        self::_session()->set(self::AUTH_ID_KEY, $user->id);
        self::_session()->regenerate();
    }

    public static function logout()
    {
        self::_session()->set(self::AUTHENTICATED_KEY, false);
        self::_session()->set(self::AUTH_ID_KEY , false);
        self::_session()->regenerate();
    }

    public static function isAuthenticated()
    {
        return self::_session()->get(self::AUTHENTICATED_KEY) === true;
    }

    protected static function _session()
    {
        return Session::instance();
    }
}