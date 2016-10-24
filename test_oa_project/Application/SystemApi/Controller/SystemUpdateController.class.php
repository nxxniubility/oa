<?php
/*
|--------------------------------------------------------------------------
| ϵͳ������ص����ݽӿ�
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class SystemUpdateController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | ϵͳ�����б�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateList()
    {
        //��ȡ����
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        //��ȡ�ӿڷ����
        $result = D('SystemUpdate','Service')->getSystemUpdateList($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'��ȡ�ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | ���ϵͳ����
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addSystemUpdate()
    {
        //��ȡ����
        $param['uptitle'] = I('param.uptitle',null);
        $param['upbody'] = I('param.upbody',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('SystemUpdate','Service')->addSystemUpdate($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | �޸�ϵͳ����
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editSystemUpdate()
    {
        //��ȡ����
        $param['system_update_id'] = I('param.system_update_id',null);
        $param['uptitle'] = I('param.uptitle',null);
        $param['upbody'] = I('param.upbody',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('SystemUpdate','Service')->editSystemUpdate($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | ɾ��ϵͳ����
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delSystemUpdate()
    {
        //��ȡ����
        $param['system_update_id'] = I('param.system_update_id',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('SystemUpdate','Service')->delSystemUpdate($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'�����ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | ��ȡϵͳ��������
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateInfo()
    {
        //��ȡ����
        $param['system_update_id'] = I('param.system_update_id',null);
        //ȥ�������ֵ
        $param = array_filter($param);
        //��ȡ�ӿڷ����
        $result = D('SystemUpdate','Service')->getSystemUpdateInfo($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'��ȡ�ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }
}