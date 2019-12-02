<?php

namespace FuDanDa\Casbin;

use CasbinAdapter\DBAL\Adapter as DatabaseAdapter;
use Casbin\Enforcer;
use Casbin\Model\Model;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use FuDanDa\Casbin\facade\Tool;

class Casbin
{

    function __construct()
    { }

    public function initConfig()
    {
        $this->config = [
            'driver'              => Tool::getEnv('TYPE', 'pdo_mysql'),                                      // ibm_db2, pdo_sqlsrv, pdo_mysql, pdo_pgsql, pdo_sqlite
            'host'                => Tool::getEnv('HOSTNAME', '127.0.0.1'),
            'dbname'              => Tool::getEnv('DATABASE', 'myadmin'),
            'user'                => Tool::getEnv('USERNAME', 'root'),
            'password'            => Tool::getEnv('PASSWORD', 'root'),
            'port'                => Tool::getEnv('HOSTPORT', 3306),
            'casbinRuleTableName' => Tool::getEnv('CASBIN', 'casbin'),
        ];
    }
    public function initDb(DatabaseAdapter $adapter)
    {
        $tableName = $adapter->casbinRuleTableName;
        $conn = $adapter->getConnection();
        $queryBuilder = $conn->createQueryBuilder();
        $queryBuilder->delete($tableName)->where('1 = 1')->execute();
        $data = [
            ['ptype' => 'p', 'v0' => 'admin', 'v1' => 'data1', 'v2' => 'read'],
            ['ptype' => 'p', 'v0' => 'root', 'v1' => 'data2', 'v2' => 'write'],
        ];
        foreach ($data as $row) {
            $queryBuilder->insert($tableName)->values(array_combine(array_keys($row), array_fill(0, count($row), '?')))->setParameters(array_values($row))->execute();
        }
    }
    public function Enforcer(array $config = null, string $modelFile = null)
    {
        is_null($config) && $this->initConfig();
        is_null($modelFile) && $modelFile = file_build_path(__DIR__, '..', '..', 'config', 'casbin.rbac.conf');
        $connection = DriverManager::getConnection(
            $this->config,
            new Configuration()
        );
        $adapter = DatabaseAdapter::newAdapter($connection);
        // $this->initDb($adapter);
        $model = Model::newModelFromFile($modelFile);
        return new Enforcer($model, $adapter);
    }
}
