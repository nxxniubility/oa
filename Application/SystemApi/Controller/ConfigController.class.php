<?php
/*
|--------------------------------------------------------------------------
| ����������صĽӿ�
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class ConfigController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | ��ȡ�����б�
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getAliOss()
    {
        //��ȡ����
        $param['bid'] = I('param.bid',null);
        $param['name'] = I('param.name',null);
        $param['size'] = I('param.size',null);
        //��ȡ�ӿڷ����
        $result = D('AliOss','Service')->osspolicy($param);
        //���ز���
        if($result['code']==0){
            $this->ajaxReturn(0,'��ȡ�ɹ�',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

}