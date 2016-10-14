<?php
/*
|--------------------------------------------------------------------------
| 员工数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class SystemUserController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取客户详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function get()
    {
        //获取请求？
        $param['username'] = I('param.username',null);
        $param['password'] = I('param.password',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->login($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

}