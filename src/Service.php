<?php


namespace fdd\Casbin;

use fdd\casbin\command\Publish;
use Casbin\Enforcer;
use Casbin\Model\Model;

class Service extends \think\Service
{

    /**
     * Register service.
     *
     * @return void
     */
    public function register()
    {

        // 绑定 Casbin决策器
        $this->app->bind('enforcer', function () {
            $config = $this->app->config->get('casbin');

            $adapter = $config['adapter'];

            $configType = $config['model']['config_type'];

            $model = new Model();
            if ('file' == $configType) {
                $model->loadModel($config['model']['config_file_path']);
            } elseif ('text' == $configType) {
                $model->loadModel($config['model']['config_text']);
            }

            return new Enforcer($model, app($adapter), $config['log']['enabled']);
        });
    }

    public function boot()
    {
        $this->commands(Publish::class);
    }
}
