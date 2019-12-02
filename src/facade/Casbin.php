<?php

namespace FuDanDa\Casbin\facade;

use FuDanDa\Casbin\Facade;

class Casbin extends Facade
{
    public static function getFacadeAccessor()
    {
        return \FuDanDa\Casbin\Auth\Casbin::class;
    }
}
