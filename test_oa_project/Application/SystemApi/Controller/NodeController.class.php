<?php
/*
|--------------------------------------------------------------------------
| 节点相关的数据接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class NodeController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 节点列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getNodeList()
    {
        //获取请求？
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        $param['status'] = I('param.status',1);
        //获取接口服务层
        $result = D('Node','Service')->getNodeList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 添加节点
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addNode()
    {
        //获取请求？
        $param['name'] = I('param.name',null);
        $param['title'] = I('param.title',null);
        $param['sort'] = I('param.sort',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Node','Service')->addNode($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 修改节点
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editNode()
    {
        //获取请求？
        $param['node_id'] = I('param.node_id',null);
        $param['name'] = I('param.name',null);
        $param['title'] = I('param.title',null);
        $param['sort'] = I('param.sort',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Node','Service')->editNode($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 删除节点
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delNode()
    {
        //获取请求？
        $param['node_id'] = I('param.node_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Node','Service')->delNode($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取节点详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getNodeInfo()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Node','Service')->getNodeInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }
}