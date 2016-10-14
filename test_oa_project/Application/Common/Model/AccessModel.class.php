<?php
namespace Common\Model;
use Common\Model\SystemModel;

class AccessModel extends SystemModel
{
    /*
    |--------------------------------------------------------------------------
    | 获取职位权限节点列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $order=null, $limit=null, $field='*', $join=null){
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }
}