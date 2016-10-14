<?php
/*
|--------------------------------------------------------------------------
| 渠道数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class ChannelController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取渠道列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelList()
    {
        //获取接口服务层
        $result = D('Channel','Service')->getChannelList();
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 添加渠道列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addChannel()
    {
        //获取请求？
        $param['channelname'] = I('param.channelname',null);
        $param['pid'] = I('param.pid',null);
        $param['sort'] = I('param.sort',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Channel','Service')->addChannel($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 修改渠道列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editChannel()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        $param['channelname'] = I('param.channelname',null);
        $param['pid'] = I('param.pid',null);
        $param['sort'] = I('param.sort',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Channel','Service')->editChannel($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取渠道及下级内容
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelChildren()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Channel','Service')->getChannelChildren($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取渠道详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelInfo()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Channel','Service')->getChannelInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

}