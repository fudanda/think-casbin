<?php

namespace FuDanDa\Casbin;

use FuDanDa\Casbin\model\Menu;
use FuDanDa\Casbin\model\Admins;
use think\facade\Cache;
use think\facade\Request;

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
    public function list($id)
    {
        try {
            $list = Admins::scope(['page', 'order'])->select();
            $list_count = count(Admins::all());
            $token = jwt_encode(['id' => $id]);
            return ['code' => 0, 'msg' => '成功', 'count' => $list_count, 'data' => $list, 'token' => $token];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function info($id)
    {
        try {
            $info = Admins::get($id);
            $token = jwt_encode(['id' => $id]);
            return ['code' => 0, 'msg' => '成功', 'data' => $info, 'token' => $token];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function edit($id)
    {
        try {
            $info = Admins::get($id);
            $token = jwt_encode(['id' => $id]);
            return ['code' => 0, 'msg' => '成功', 'data' => $info, 'token' => $token];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
    public function expurgate($id)
    {
        try {
            $id == 1 && exception('超级管理员无法删除');
            $result = Admins::destroy($id);
            $token = jwt_encode(['id' => $id]);
            return ['code' => 0, 'msg' => '成功', 'token' => $token];
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }
}
