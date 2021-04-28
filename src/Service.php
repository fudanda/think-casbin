<?php


namespace fdd\Casbin;

use fdd\casbin\command\Publish;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(Publish::class);
    }
}
