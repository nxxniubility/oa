<?php
namespace Common\Model;

use Common\Model\SystemModel;
class MarketStandardModel extends SystemModel
{
    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null){
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null){
        return $this->where($where)->join($join)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取单条
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null,$field='*'){
        return $this->field($field)->where($where)->find();
    }
}