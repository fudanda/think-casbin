<?php

namespace FuDanDa\Casbin;

use FuDanDa\Casbin\model\Menu;
use FuDanDa\Casbin\model\Admins;
use think\facade\Request;
use FuDanDa\Casbin\facade\Casbin;


class AdminsAuth
{
    public function login($data)
    {
        try {
            $map[] = ['name', '=', $data['name']];
            $map[] = ['password', '=', md5($data['password'])];
            /* 查询用户信息 */
            $admin_info = Admins::where($map)->fetchSql(false)->find();
            /* 不存在该用户 */
            empty($admin_info) && exception('用户名或密码错误，请重新输入！');

            $admin_info['status'] > 0 && exception('用户名已禁用,请联系管理员！');
            $admin_info->last_login_ip   = Request::ip();
            $admin_info->last_login_time = time();
            $admin_info->save();
            $admin_info = $admin_info
                ->append(['status_text'])
                ->toArray();
            $token = jwt_encode(['id' => $admin_info['id'], 'permission' => $admin_info['permission'], 'name' => $admin_info['name']], null, 86400);
            return ['code' => 0, 'msg' => '成功', 'data' => $admin_info, 'token' => $token];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
            /* 出现错误：写入日志 */
        }
    }
    public function list($where = null)
    {
        try {
            $list = Admins::scope(['page', 'order'])
                ->with('permission')
                ->where($where)
                ->select();
            $list_count = count(Admins::where($where)->all());
            return ['code' => 0, 'msg' => '成功', 'count' => $list_count, 'data' => $list];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function info($id)
    {
        $info = Admins::get($id);
        return $info;
    }
    public function edit($data)
    {
        try {
            $result = Admins::Update($data);
            !$result && exception('失败');
            return ['code' => 0, 'msg' => '成功'];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function expurgate($id, $truedel = false)
    {
        try {
            $id == 1 && exception('超级管理员无法删除');
            $result = Admins::destroy($id, $truedel);
            return ['code' => 0, 'msg' => '成功'];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function add($data)
    {
        try {
            $result = Admins::create($data);
            empty($result) && exception('失败');
            return ['code' => 0, 'msg' => '成功'];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function menu($idarr = [])
    {
        return Menu::order('order', 'asc')->all($idarr)->toArray();
    }
    public function formattedMenu()
    {
        return buildTree(Menu::order('order', 'asc')->all()->toArray());
    }
    /**
     *
     */
    public function addPermissions($permissions, $data)
    {
        $removed = Casbin::removeFilteredPolicy(0, $permissions);
        foreach ($data as $key => $value) {
            $added = Casbin::addPolicy($permissions, $value['href'], "all", $value['id']);
        }
        return true;
    }
}
