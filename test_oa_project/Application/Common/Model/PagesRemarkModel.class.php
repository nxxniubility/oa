<?php

namespace Common\Model;
use Common\Model\BaseModel;

class PagesRemarkModel extends SystemModel
{
    public function _initialize(){
        parent::_initialize();
    }

    /**
     * 查看模板备注
     * @author nxx
     */
    // public function getPagesRemark($pages_id,$system_user_id)
    // {
    //     $remarklt = M('pages_remark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
    //     return $remarklt;
    // }
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }
    
    /**
     * 添加模板备注
     * @author nxx
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
