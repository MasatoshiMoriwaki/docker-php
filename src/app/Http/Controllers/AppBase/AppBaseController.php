<?php

namespace App\Http\Controllers\AppBase;

use Framework\Controller as Controller;

class AppBaseController extends Controller
{

    public function __construct($application)
    {
        parent::__construct($application);
        $this->login_user = \App\Service\Authservice::getLoginUser(); // ログインユーザ
    }

}