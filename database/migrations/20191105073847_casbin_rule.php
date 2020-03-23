<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CasbinRule extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

        $table = $this->table('casbin_rule');
        $table->addColumn('ptype', 'string', ['null' => true])
            ->addColumn('v0', 'string', ['null' => true])
            ->addColumn('v1', 'string', ['null' => true])
            ->addColumn('v2', 'string', ['null' => true])
            ->addColumn('v3', 'string', ['null' => true])
            ->addColumn('v4', 'string', ['null' => true])
            ->addColumn('v5', 'string', ['null' => true])
            ->create();
        $default[] = [
            'ptype' => 'g',
            'v0'    => '超级管理员',
            'v1'    => 'admin',
            'v2'    => '超级管理员',
            'v3'    => null,
            'v4'    => null,
            'v5'    => null,
        ];
        $default[] = [
            'ptype' => 'p',
            'v0'    => 'admin',
            'v1'    => '/admin',
            'v2'    => 'all',
            'v3'    => null,
            'v4'    => null,
            'v5'    => null,
        ];
        $default[] = [
            'ptype' => 'p',
            'v0'    => 'admin',
            'v1'    => '/admin/permissions',
            'v2'    => 'all',
            'v3'    => null,
            'v4'    => null,
            'v5'    => null,
        ];
        $default[] = [
            'ptype' => 'p',
            'v0'    => 'admin',
            'v1'    => '/admin/admins',
            'v2'    => 'all',
            'v3'    => null,
            'v4'    => null,
            'v5'    => null,
        ];
        $table->insert($default);
        $table->saveData();
    }
}
