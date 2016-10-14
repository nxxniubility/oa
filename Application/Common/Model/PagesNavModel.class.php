<?php

namespace Common\Model;
use Common\Model\BaseModel;

class PagesNavModel extends SystemModel
{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 获取模板导航列表
     * @author nxx
     */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addData($data)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_id = $this->add($data);
            return array('code'=>0,'data'=>$re_id);
        }
    }


}
