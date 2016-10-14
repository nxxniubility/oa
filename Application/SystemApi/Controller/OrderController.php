<?php
/*
|--------------------------------------------------------------------------
| 订单相关的数据接口
|--------------------------------------------------------------------------
| @author nxx
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemUserService;

class OrderController extends SystemApiController
{

  /*
  |--------------------------------------------------------------------------
  | 创建订单
  |--------------------------------------------------------------------------
  | @author nxx
  */
  public function createOrder()
  {
      //获取请求？
      $param['username'] = I('param.username',null);
      $param['subscription'] = I('param.subscription',null);
      $param['user_id'] = I('param.user_id',null);
      $param['realname'] = I('param.realname',null);
      $param['system_user_id'] = $this->system_user_id;
      $param['zone_id'] = $this->system_user['zone_id'];
      //去除数组空值
      $param = array_filter($param);
      //获取接口服务层
      $result = D('Order','Service')->createOrder($param);
      //返回参数
      if($result['code']==0){
          $this->ajaxReturn(0,'获取成功',$result['data']);
      }
      $this->ajaxReturn($result['code'],$result['msg']);
  }


}
