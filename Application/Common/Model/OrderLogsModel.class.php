<?php
namespace Common\Model;

use Common\Model\SystemModel;

class OrderLogsModel extends SystemModel
{
    /*
    |--------------------------------------------------------------------------
    | 获取记录列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $order='createime DESC', $field='*', $join=null){
        return $this->field($field)->join($join)->where($where)->order($order)->select();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取记录总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null){
        return $this->where($where)->count();
    }
}