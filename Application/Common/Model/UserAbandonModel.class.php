<?php
/*
|--------------------------------------------------------------------------
| 用户表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class UserAbandonModel extends SystemModel
{
    public function getAbandonList($where="",$fields=""){
        $where['status'] = 1;
        return $this->field($fields)->where($where)->select();
    }
}