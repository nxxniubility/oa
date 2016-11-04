<?php
/*
|--------------------------------------------------------------------------
| 部门相关的数据接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemUserService;

class MessageController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取消息列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgList()
    {
        //获取请求？
        $param['isread'] = I('param.isread',null);
        $param['msgtype'] = I('param.msgtype',null);
        $param['page'] = I('param.page',null);
        //获取接口服务层
        $result = D('Message','Service')->getMsgList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取消息信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgInfo()
    {
        //获取请求？
        $param['message_id'] = I('param.message_id',null);
        //获取接口服务层
        $result = D('Message','Service')->getMsgInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取当前消息提示
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgHint()
    {
        //获取接口服务层
        $result = D('Message','Service')->getMsgHint();
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
    |--------------------------------------------------------------------------
    | 删除消息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function delMsg()
    {
        $param['message_id'] = I('param.message_id', null);
        $msg_list = D('Message', 'Service')->delMsg($param);
        if ($msg_list['code']==0) {
            $this->ajaxReturn(0, '删除成功', $msg_list['data']);
        }
        else {
            $this->ajaxReturn($msg_list['code'], $msg_list['msg']);
        }
    }

}