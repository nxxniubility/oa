<?php
namespace Common\Model;
use Common\Model\SystemModel;

class AccessModel extends SystemModel
{
    /*
    |--------------------------------------------------------------------------
    | ��ȡְλȨ�޽ڵ��б�
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $order=null, $limit=null, $field='*', $join=null){
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }
}