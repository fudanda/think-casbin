<?php

//复制的 casbin/dbal-adapter 包应为无法修改权限的表名
//所以复制一份自己修改

declare(strict_types=1);

namespace FuDanDa\Casbin;

use Casbin\Persist\Adapter as AdapterContract;
use Casbin\Persist\AdapterHelper;
use Casbin\Model\Model;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

/**
 * DBAL Adapter.
 *
 * @author techlee@qq.com
 */
class Adapter implements AdapterContract
{
    use AdapterHelper;

    /**
     * Connection instance.
     *
     * @var Connection
     */
    public $connection;

    /**
     * CasbinRule table name.
     *
     * @var string
     */
    public $casbinRuleTableName = 'casbin_rule';

    /**
     * Adapter constructor.
     *
     * @param Connection|array $connection
     */
    public function __construct($connection)
    {
        $prefix = config('database.prefix');
        $this->casbinRuleTableName =  $prefix . $this->casbinRuleTableName;
        if ($connection instanceof Connection) {
            $this->connection = $connection;
        } else {
            $this->connection = DriverManager::getConnection(
                $connection,
                new Configuration()
            );
        }
        $this->initTable();
    }

    /**
     * New a Adapter.
     *
     * @param Connection|array $connection
     *
     * @return Adapter
     */
    public static function newAdapter($connection): AdapterContract
    {
        return new static($connection);
    }

    /**
     * Initialize the policy rules table, create if it does not exist.
     *
     * @return void
     */
    public function initTable()
    {
        $sm = $this->connection->getSchemaManager();
        if (!$sm->tablesExist([$this->casbinRuleTableName])) {
            $schema = new \Doctrine\DBAL\Schema\Schema();
            $table = $schema->createTable($this->casbinRuleTableName);
            $table->addColumn('id', 'integer', array('autoincrement' => true));
            $table->addColumn('ptype', 'string', ['notnull' => false]);
            $table->addColumn('v0', 'string', ['notnull' => false]);
            $table->addColumn('v1', 'string', ['notnull' => false]);
            $table->addColumn('v2', 'string', ['notnull' => false]);
            $table->addColumn('v3', 'string', ['notnull' => false]);
            $table->addColumn('v4', 'string', ['notnull' => false]);
            $table->addColumn('v5', 'string', ['notnull' => false]);
            $table->setPrimaryKey(['id']);
            $sm->createTable($table);
        }
    }

    public function savePolicyLine($ptype, array $rule)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->insert($this->casbinRuleTableName)
            ->values([
                'ptype' => '?',
            ])
            ->setParameter(0, $ptype);

        foreach ($rule as $key => $value) {
            $queryBuilder->setValue('v' . strval($key), '?')->setParameter($key + 1, $value);
        }

        return $queryBuilder->execute();
    }

    /**
     * loads all policy rules from the storage.
     *
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $stmt = $queryBuilder->select('ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5')->from($this->casbinRuleTableName)->execute();

        while ($row = $stmt->fetch()) {
            $line = implode(', ', array_filter($row, function ($val) {
                return '' != $val && !is_null($val);
            }));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * saves all policy rules to the storage.
     *
     * @param Model $model
     */
    public function savePolicy(Model $model): void
    {
        foreach ($model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
        foreach ($model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
    }

    /**
     * adds a policy rule to the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        $this->savePolicyLine($ptype, $rule);
    }

    /**
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete($this->casbinRuleTableName)->where('ptype = ?')->setParameter(0, $ptype);

        foreach ($rule as $key => $value) {
            $queryBuilder->andWhere('v' . strval($key) . ' = ?')->setParameter($key + 1, $value);
        }

        $queryBuilder->delete($this->casbinRuleTableName)->execute();
    }

    /**
     * RemoveFilteredPolicy removes policy rules that match the filter from the storage.
     * This is part of the Auto-Save feature.
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param string ...$fieldValues
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->where('ptype = :ptype')->setParameter(':ptype', $ptype);

        foreach (range(0, 5) as $value) {
            if ($fieldIndex <= $value && $value < $fieldIndex + count($fieldValues)) {
                if ('' != $val = $fieldValues[$value - $fieldIndex]) {
                    $key = 'v' . strval($value);
                    $queryBuilder->andWhere($key . ' = :' . $key)->setParameter($key, $val);
                }
            }
        }

        $queryBuilder->delete($this->casbinRuleTableName)->execute();
    }

    /**
     * Gets connection.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
}