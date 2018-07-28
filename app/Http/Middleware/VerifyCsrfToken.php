<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://home_qiwei.net/api/regist',
        'http://home_qiwei.net/api/loginCheck',
        'http://home_qiwei.net/api/changePassword',
        'http://home_qiwei.net/api/forgetPassword',
        'http://home_qiwei.net/api/addAddress',
        'http://home_qiwei.net/api/editAddress',
        'http://home_qiwei.net/api/addCart',
        'http://home_qiwei.net/api/addOrder',
    ];
}
