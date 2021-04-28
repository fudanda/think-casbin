<?php


namespace fdd\Casbin;

use Fdd\ApiDoc\command\Publish;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(Publish::class);
    }
}
