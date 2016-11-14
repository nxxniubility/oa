<?php
namespace System\Controller;

use Common\Controller\SystemController;

class InformationController extends SystemController
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
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 修改密码
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function changePwd()
    {
        $userInfo = $this->system_user;
        $username = decryptPhone($userInfo['username'], C('PHONE_CODE_KEY'));
        if(IS_POST){
            $request = I('post.');
            $reflag = D('SystemUser', 'Service')->editPwd($request);
            if($reflag['code'] == 0) {
                $this->ajaxReturn(0, '密码修改成功', U('System/Index/main'));
            }else {
                $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }
        }
        $this->assign('username', $username);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 呼叫号码设置列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function callNumberList()
    {
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 消息列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function msgList()
    {
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 发送消息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function sendMsg()
    {
        $this->display();
    }

}