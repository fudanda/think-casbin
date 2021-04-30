<?php

namespace fdd\casbin\facade;

use think\Facade;


/**
 * @see \Casbin\Enforcer
 * @package tauthz\facade
 * @mixin \Casbin\Enforcer
 * @method static mixed enforce(string $subject,string $object,string $action) static 权限检查
 * @method static mixed addPolicy(string $subject,string $object,string $action) static 当前策略添加授权规则
 * @method static mixed hasPolicy(string $subject,string $object,string $action) static 确定是否存在授权规则
 * @method static mixed removePolicy(string $subject,string $object,string $action) static 当前策略移除授权规则
 * @method static mixed getRolesForUser(string $username) static 获取用户具有的角色
 * @method static mixed getUsersForRole(string $role) static 获取具有角色的用户
 * @method static mixed hasRoleForUser(string $username, string $role) static 确定用户是否具有角色
 * @method static mixed addRoleForUser(string $username, string $role) static 为用户添加角色
 * @method static mixed deleteRoleForUser(string $username, string $role) static 删除用户的角色
 * @method static mixed deleteRolesForUser(string $username) static 删除用户的所有角色
 * @method static mixed deleteUser(string $username) static 删除一个用户
 * @method static mixed deleteRole(string $role) static 删除一个角色
 * @method static mixed deletePermission(string $policy) static 删除权限
 * @method static mixed addPermissionForUser(string $username,string $policy) static 为用户或角色添加权限
 * @method static mixed deletePermissionForUser(string $username,string $policy) static 删除用户或角色的权限
 * @method static mixed deletePermissionsForUser(string $username) static 删除用户或角色的权限
 * @method static mixed getPermissionsForUser(string $username) static 获取用户或角色的权限
 * @method static mixed hasPermissionForUser(string $username,string $policy) static 确定用户是否具有权限
 * @method static mixed getImplicitRolesForUser(string $username) static 获取用户具有的隐式角色
 * @method static mixed getImplicitPermissionsForUser(string $username) static 获取用户具有的隐式角色
 */
class Enforcer extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'enforcer';
    }
}
