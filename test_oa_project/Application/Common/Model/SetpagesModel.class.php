<?php
/*
|--------------------------------------------------------------------------
| 模板表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class SetpagesModel extends SystemModel
{
	public function _initialize(){
       
        $this->setPageInfoDb = M("setpageinfo");
    }			
    public function getSetPages($where)
    {
    	$where['status'] = 1;
    	return $this->where($where)->select();
    }
	 /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfo($setpages_id)
    {
    	return $this->setPageInfoDb->where("setpages_id = $setpages_id")->select();
    }

}