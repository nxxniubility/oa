<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\NodeController as NodeMain;

class NodeController extends SystemController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 节点管理
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function nodeList()
    {
        //获取数据
        $param['order'] = 'id asc';
        $nodeList = D('Node','Service')->getNodeList($param);
        $nodeList = $nodeList['data']['data'];
        //模版赋值
        $this->assign('nodeList', $nodeList);
        $this->display();
    }

    /**
     * 添加节点
     * @author zgt
     */
    public function createnode()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $reflag = D('Node','Service')->addNode($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '节点添加成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 修改节点
     * @author zgt
     */
    public function editnode()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $data['node_id'] = $data['id'];
        $reflag = D('Node','Service')->editNode($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '节点修改成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 删除节点
     * @author zgt
     */
    public function delnode()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $param['node_id'] = $data['id'];
        $reflag = D('Node','Service')->delNode($param);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '节点删除成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

}