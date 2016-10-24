<?php
/*
|--------------------------------------------------------------------------
| �ڵ���ص����ݽӿ�
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
   | �ڵ��б�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getNodeList()
    {
        //��ȡ����
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        $param['status'] = I('param.status',1);
        //��ȡ�ӿڷ����
        $result = D('Node','Service')->getNodeList($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'��ȡ�ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | ��ӽڵ�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addNode()
    {
        //��ȡ����
        $param['name'] = I('param.name',null);
        $param['title'] = I('param.title',null);
        $param['sort'] = I('param.sort',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('Node','Service')->addNode($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | �޸Ľڵ�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editNode()
    {
        //��ȡ����
        $param['node_id'] = I('param.node_id',null);
        $param['name'] = I('param.name',null);
        $param['title'] = I('param.title',null);
        $param['sort'] = I('param.sort',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('Node','Service')->editNode($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | ɾ���ڵ�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delNode()
    {
        //��ȡ����
        $param['node_id'] = I('param.node_id',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('Node','Service')->delNode($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | ��ȡ�ڵ�����
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getNodeInfo()
    {
        //��ȡ����
        $param['channel_id'] = I('param.channel_id',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('Node','Service')->getNodeInfo($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'��ȡ�ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }
}