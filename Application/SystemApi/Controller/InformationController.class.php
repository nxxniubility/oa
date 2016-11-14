<?php
namespace SystemApi\Controller;

use Common\Controller\SystemApiController;
use Common\Service\InformationService;

class InformationController extends SystemApiController
{

    //控制器前置
    public function _initialize()
    {
        parent::_initialize();
    }


    /*
    * *****************************************************
    * 员工个人信息
    * *****************************************************
    * @author nxx
    */
    public function systemUserInfo()
    {
        $user['face'] = I("param.face", null);
        $updat = D('SystemUser', 'Service')->editSystemUserFace($user);
        if ($updat['data']['code'] != 0) {
            $this->ajaxReturn($updat['code'], '修改失败');
        }
        $this->system_user['face'] = $user['face'];
        session('system_user',$this->system_user);
        $userInfo = D('SystemUser', 'Service')->getSystemUserInfo(array('system_user_id'=>$system_user_id));
        $this->ajaxReturn(0, '修改成功',$userInfo['data']);

    }
    

}