<?php

namespace FuDanDa\Casbin\model;

use think\Model;
use think\model\concern\SoftDelete;

class Menu extends Common
{
    use SoftDelete;
    protected $name = 'menu';

    public function setUriAttr($value)
    {
        return trim($value, '/');
    }
}