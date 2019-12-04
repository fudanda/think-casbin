<?php

namespace FuDanDa\Casbin\facade;

use think\Facade;
use think\Container;
use FuDanDa\Casbin\Adapter as DatabaseAdapter;
use Casbin\Enforcer;
use Casbin\Model\Model;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class Casbin extends Facade
{
    protected static function getFacadeClass()
    {
        if (!Container::getInstance()->has('casbin')) {
            Container::getInstance()->bindTo('casbin', function () {
                $config = [
                    'driver'              => 'pdo_mysql', // ibm_db2, pdo_sqlsrv, pdo_mysql, pdo_pgsql, pdo_sqlite
                    'host'                => config('database.hostname'),
                    'dbname'              => config('database.database'),
                    'user'                => config('database.username'),
                    'password'            => config('database.password'),
                    'port'                => config('database.hostport'),
                    'casbinRuleTableName' => config('database.prefix') . 'casbin_rule',
                ];
                $modelFile = file_build_path(__DIR__, '..', '..', 'config', 'casbin.rbac.conf');
                $connection = DriverManager::getConnection(
                    $config,
                    new Configuration()
                );
                $adapter = DatabaseAdapter::newAdapter($connection);
                $model = Model::newModelFromFile($modelFile);
                return new Enforcer($model, $adapter);
            });
        }
        return 'casbin';
    }
}