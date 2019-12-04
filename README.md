# [fdd/think-casbin](https://github.com/fudanda/think-casbin)

===============
> fdd/think-casbin 的运行环境要求PHP5.6+。

适用于 [ThinkPHP5.1](http://thinkphp.cn) RBAC权限

## 主要新特性

* 暂无

## 依赖

* topthink/think-migration=2.0.*

## 安装

~~~shell
composer require fdd/think-apidoc
~~~

>1.安装后config目录会自动生成 casbin.php和casbin.rbac.conf.php
>
>2.重命名 casbin.rbac.conf.php 为 casbin.rbac.conf

## 使用

1.配置数据库相关信息

2.执行命令迁移权限数据库

~~~shell
php think db:run
~~~

>数据库自动生成相关权限表

## 权限使用

```php
use Casbin
//获取全部角色(权限组)
Casbin::getGroupingPolicy();

......

```

## 未完待续
