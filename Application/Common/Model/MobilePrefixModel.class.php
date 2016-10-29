<?php

namespace Common\Model;
use Common\Model\BaseModel;

class MobilePrefixModel extends BaseModel
{
    public function _initialize(){
        parent::_initialize();
    }

    /*
     * 学历表
     * @author nxx
     * @return array
     */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
     * 学历详情
     * @author nxx
     * @return array
     */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }


}
