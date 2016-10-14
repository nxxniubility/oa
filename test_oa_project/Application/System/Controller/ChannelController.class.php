<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\ChannelController as ChannelMain;

class ChannelController extends SystemController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 渠道管理
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function channelList()
    {
        //获取数据
        $channelController = new ChannelMain();
        $channelList = $channelController->getList();
        $channelList = $channelList['data']['data'];
        //模版赋值
        $this->assign('channelList', $channelList);
        $this->display();
    }

    /**
     * 添加渠道
     * @author zgt
     */
    public function createChannel()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $channelController = new ChannelMain();
        $data['operator_id'] = $this->system_user_id;
        $reflag = $channelController->create_channel($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '渠道添加成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 修改渠道
     * @author zgt
     */
    public function editChannel()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $channelController = new ChannelMain();
        $reflag = $channelController->edit_channel($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '渠道修改成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 删除渠道
     * @author zgt
     */
    public function delChannel()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $channelController = new ChannelMain();
        $data['status'] = 0;
        $reflag = $channelController->edit_channel($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '渠道删除成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

}