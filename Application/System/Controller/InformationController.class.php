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
        if (IS_POST) {
            $user['face'] = I("post.face");
            $updat = D('SystemUser', 'Service')->editSystemUserFace($user);
            if ($updat['data']['code'] != 0) {
                $this->ajaxReturn($updat['code'], '修改失败');
            }
            $this->system_user['face'] = $user['face'];
            session('system_user',$this->system_user);
            $this->ajaxReturn(0, '修改成功');
        }
        $userInfo = D('SystemUser', 'Service')->getSystemUserInfo(array('system_user_id'=>$system_user_id));
        $this->assign('userInfo', $userInfo['data']);
        $this->display();

    }

    /**
     * 修改密码
     * @author  nxx
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
        if(IS_POST){
            $request = I('post.');
            $request['system_user_id'] = $this->system_user_id;
            if($request['type']=='add'){
                $reflag = D('SystemUser','Service')->addCallNumber($request);
            }elseif($request['type']=='edit'){
                $reflag = D('SystemUser','Service')->editCallNumber($request);
            }elseif($request['type']=='del'){
                $reflag = D('SystemUser','Service')->delCallNumber($request);
            }elseif($request['type']=='start'){
                $reflag = D('SystemUser','Service')->startCallNumber($request);
            }
            if($reflag['code']==0){
                $this->ajaxReturn(0, '操作成功');
            }else{
                $this->ajaxReturn(1, $reflag['msg']);
            }
        }
        //获取数据
        $data['numberList'] = D('SystemUser','Service')->getCallNumber(array('system_user_id'=>$this->system_user_id));
        $this->assign('data', $data);
        $this->display();
    }
}