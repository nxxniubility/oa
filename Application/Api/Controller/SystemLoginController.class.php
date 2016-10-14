<?php
/*
|--------------------------------------------------------------------------
| 所有数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Api\Controller;
use Common\Controller\ApiBaseController;
use Common\Service\SystemUserService;

class SystemLoginController extends ApiBaseController
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
    public function login()
    {
        //获取请求？
        $data['username'] = I('param.username',null);
        $data['password'] = I('param.password',null);
        $data['verification'] = I('param.verification',null);
        //去除数组空值
        $data = array_filter($data);
        //获取接口服务层
        $SystemUserService = new SystemUserService();
        $result = $SystemUserService->login($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg'],$result['data']);
    }

}