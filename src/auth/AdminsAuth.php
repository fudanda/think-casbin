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

        /* 用户信息验证 */
        $returnData = ['code' => 1, 'msg' => '用户登录失败！！！', 'data' => []];             //初始化返回数据
        try {
            $map[] = ['name', '=', $data['name']];
            $map[] = ['password', '=', md5($data['password'])];
            /* 查询用户信息 */
            $admins = Admins::where($map)->find();
            /* 不存在该用户 */
            if (empty($admins)) {
                $returnData['msg'] = '用户名或密码错误，请重新输入！';
            }
            /* 用户被禁用 */ elseif ($admins['status'] !== 0) {
                $returnData['msg'] = '用户已禁用，请联系相关人员！';
            }
            /* 验证成功 */ else {
                $admins->last_login_ip   = Request::ip();
                $admins->last_login_time = time();
                $admins->save();
                $returnData['data'] = $admins;                                //用户信息
                $returnData['is_forget'] = $data['is_forget'];                  //是否忘记密码
                $returnData['code'] = 0; //正确码
                $returnData['msg'] = '成功';
                session('admins', $admins);
                session('admins_id', $admins->id);
            }
        } catch (DbException $e) {
            /* 出现错误：写入日志 */
            Log::record("登入后台系统失败：{$e->getMessage()}");
        }
        /* 返回数据 */
        return $returnData;
    }

    public function info()
    {
        $info = Cache::get('admins_' . session('admins_id'));
        if (!$info) {
            $info = Admins::get(session('admins_id'));
            Cache::set('admins_' . session('admins_id'), $info, 3600);
        }

        return $info;
    }

    public function clearCache()
    {
        return Cache::rm('admins_' . session('admins_id'));
    }

    public function id()
    {
        return session('admins_id');
    }

    public function menu()
    {
        $menus = [];
        $allmenu  = Menu::where('status', 0)->select();
        $this->initAllMenu($allmenu);
        $menu  = Menu::where('status', 0)->order('order asc');
        foreach ($this->info()->roles as $role) {
            if ($role->status !== 0) {
                continue;
            }
            if ($role['id'] == 1) {
                $new_menu = buildTree($menu->select()
                    ->toArray(), true);
                $this->initMenu($new_menu);
                return $new_menu;
            }
            $menus = array_merge($menus, $role->menus->toArray());
        }
        $menus = assoc_unique($menus, 'id');
        $new_menu = buildTree($menus, true);
        $this->initMenu($new_menu);

        return $new_menu;
    }
    public function initAllMenu($menu)
    {
        $configFilePath = env('app_path') . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'menus.json';
        // $json_string = file_get_contents($configFilePath);

        $data['code'] = 0;
        $data['msg'] = '成功！';
        $data['count'] = count($menu);
        $data['data'] = $menu;
        $json_string = json_encode($data, JSON_UNESCAPED_UNICODE);
        // 写入文件
        file_put_contents($configFilePath, $json_string);
    }
    public function initMenu($new_menu)
    {
        $configFilePath = env('app_path') . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'init.json';
        $json_string = file_get_contents($configFilePath);
        // 用参数true把JSON字符串强制转成PHP数组
        $data = json_decode($json_string, true);
        $data['menuInfo']['currency']['child'] = $new_menu;
        $json_string = json_encode($data, JSON_UNESCAPED_UNICODE);
        // 写入文件
        file_put_contents($configFilePath, $json_string);
    }

    public function check($path, $method = 'GET')
    {
        $path = parse_url($path)['path'];
        $path = trim($path, '/');
        $path = trim($path, '.html');
        foreach ($this->info()->roles as $role) {
            if ($role->status !== 0) {
                continue;
            }
            //查找所有权限
            $permissions = $role->permissions()->select();
            if (!$permissions) {
                //本次权限组无权限，跳出本次循环
                continue;
            }
            foreach ($permissions as $val) {
                if ($val['alias'] == $path) {
                    return $this->checkMethod($val['http_method'], $method);
                }
                foreach (explode("\r\n", $val['path']) as $v) {
                    if ($v == $path) {
                        return $this->checkMethod($val['http_method'], $method);
                    }
                    $pattern = trim($v, '/');
                    $pattern = trim($pattern, '.html');
                    $pattern = preg_quote($pattern, '#');
                    $pattern = str_replace('\*', '.*', $pattern);
                    if (preg_match('#^' . $pattern . '\z#u', $path) === 1) {
                        return $this->checkMethod($val['http_method'], $method);
                    }
                }
            }
        }

        return false;
    }

    private function checkMethod($http_method, $method)
    {
        if (strpos($http_method, strtoupper($method)) !== false || $http_method == '') {
            return true;
        }
    }
}