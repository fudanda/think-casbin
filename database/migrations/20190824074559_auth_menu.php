<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class AuthMenu extends Migrator
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
        $table_name = config('database.prefix') ?: '' . 'menu';

        $table = $this->table('menu');

        $table->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('title', 'string', ['limit' => 100])
            ->addColumn('parent_id', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('order', 'integer', ['limit' => MysqlAdapter::BLOB_REGULAR, 'default' => 1000])
            ->addColumn('icon', 'string', ['limit' => 100])
            ->addColumn('href', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('permission', 'string', ['limit' => 100, 'default' => ''])
            ->addColumn('status', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'null' => false])
            ->addColumn('create_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('update_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => 0])
            ->addColumn('delete_time', 'integer', ['limit' => MysqlAdapter::INT_REGULAR, 'default' => null, 'null' => true])
            ->addIndex(['parent_id'], ['unique' => false])
            ->create();
        $default[] = [
            'name'        => '系统管理',
            'title'        => '系统管理',
            'parent_id'   => 0,
            'order'       => 99,
            'icon'        => 'layui-icon layui-icon-set',
            'href'         => '/admin',
            'create_time' => time(),
            'update_time' => time(),
        ];
        $default[] = [
            'name'        => '权限管理',
            'title'        => '权限管理',
            'parent_id'   => 1,
            'order'       => 99,
            'icon'        => 'layui-icon layui-icon-group',
            'href'         => '/admin/permissions',
            'create_time' => time(),
            'update_time' => time(),
        ];
        $default[] = [
            'name'        => '管理员管理',
            'title'       => '管理员管理',
            'parent_id'   => 1,
            'order'       => 99,
            'icon'        => 'layui-icon layui-icon-user',
            'href'         => '/admin/admins',
            'create_time' => time(),
            'update_time' => time(),
        ];
        $table->insert($default);
        $table->saveData();
    }
}
