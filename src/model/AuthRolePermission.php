<?php

namespace FuDanDa\Casbin\model;

use think\model\Pivot;

class AuthRolePermission extends Pivot
{
    protected $autoWriteTimestamp = true;
    protected $name = 'auth_role_permission';
}