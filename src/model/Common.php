<?php

namespace FuDanDa\Casbin\common\model;


use think\db\Query;
use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class Common 公共模型
 * @package app\model
 */
class Common extends Model
{
    use SoftDelete;

    /**
     * 查询范围：分页
     * @param Query $query
     */
    public function scopePage(Query $query)
    {
        /** 接收参数 */
        $page = input('param.page/d', 1);
        $limit = input('param.limit/d', 15);

        /** 设置分页 */
        $query->page($page, $limit);
    }

    /**
     * 查询范围：排序
     * @param Query $query
     */
    public function scopeOrder(Query $query)
    {
        /** 接收参数 */
        $sort = input('param.sort', $this->pk);
        $order = input('param.order', 'desc');
        /** 设置排序 */
        $query->order($sort, $order);
    }
}