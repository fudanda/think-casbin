<?php

namespace FuDanDa\Casbin\facade;

use think\Facade;

class AdminsAuth extends Facade
{
    protected static function getFacadeClass()
    {
        return 'FuDanDa\Casbin\AdminsAuth';
    }
}