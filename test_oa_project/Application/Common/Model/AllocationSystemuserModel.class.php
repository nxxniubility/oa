<?php

namespace Common\Model;
use Common\Model\BaseModel;

class AllocationSystemuserModel extends BaseModel
{
    protected $_id='user_allocation_id';
    public function _initialize(){
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | »ñÈ¡µ¥Ìõ¼ÇÂ¼
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | »ñÈ¡ÁÐ±í
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null, $group=false)
    {
        if($group){
            return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->group($group)->Distinct(true)->select();
        }
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | »ñÈ¡×ÜÊý
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null)
    {
        return $this->where($where)->join($join)->count();
    }


    /*
    |--------------------------------------------------------------------------
    | Ìí¼Ó
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addData($data)
    {
        // Èç¹û´´½¨Ê§°Ü ±íÊ¾ÑéÖ¤Ã»ÓÐÍ¨¹ý Êä³ö´íÎóÌáÊ¾ÐÅÏ¢
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_id =  $this->add($data);
            return array('code'=>0,'data'=>$re_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ÐÞ¸Ä
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editData($data,$id)
    {
        // Èç¹û´´½¨Ê§°Ü ±íÊ¾ÑéÖ¤Ã»ÓÐÍ¨¹ý Êä³ö´íÎóÌáÊ¾ÐÅÏ¢
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_flag =  $this->where(array($this->_id=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | É¾³ý
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delData($id)
    {
        return $this->where(array($this->_id=>$id))->delete();
    }

}