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
    */
    public function systemUserInfo()
    {
        $system_user_id = $this->system_user_id;
        if (IS_POST) {
            $user['face'] = I("post.face");
            $updat = D('SystemUser')->editSystemUser($user,$system_user_id);
            if ($updat === false) {
                $this->ajaxReturn(1, '修改失败');
            }
            $this->system_user['face'] = $user['face'];
            session('system_user',$this->system_user);
            $this->ajaxReturn(0, '修改成功');
        }
        
        $userInfo = D('SystemUser')->getSystemUserInfo($system_user_id);
        if ($userInfo['sex'] == 1) {   //性别
                $userInfo['sex'] = '男';
        }elseif ($userInfo['sex'] == 2) {
            $userInfo['sex'] = '女';
        }else{
            $userInfo['sex'] = '未知';
        }

        if ($userInfo['birthday']) {
            $userInfo['birthday'] = !empty($userInfo['birthday'])?date('Y-m-d', $userInfo['birthday']):null;
        }

        if ($userInfo['entrytime']) {
            $userInfo['entrytime'] = $userInfo['entrytime']!=0?date('Y-m-d', $userInfo['entrytime']):'';
        }

        if ($userInfo['straightime']) {
            $userInfo['straightime'] = $userInfo['straightime']!=0?date('Y-m-d', $userInfo['straightime']):'';
        }

        if ($userInfo['marital'] == 0) {   //性别
                $userInfo['marital'] = '未婚';
        }elseif ($userInfo['marital'] == 1) {
            $userInfo['marital'] = '已婚';
        }else{
            $userInfo['marital'] = '未知';
        }

        switch ($userInfo['usertype']){
            case '10':
                $userInfo['usertype'] = '离职员工';
                break;
            case '11':
                $userInfo['usertype'] = '兼职';
                break;
            case '12':
                $userInfo['usertype'] = '实习生';
                break;
            case '13':
                $userInfo['usertype'] = '试用期';
                break;
            default:
                $userInfo['usertype'] = '正式员工';
            break;
        }        
        if ($userInfo['education_id']) {   //学历
            $edu = D('Education')->getEducationInfo($userInfo['education_id']);
            $userInfo['educationname'] = $edu;
        }
        if($userInfo['username']){
            $userInfo['username'] = decryptPhone($userInfo['username'], C('PHONE_CODE_KEY'));
        }
        $this->assign('userInfo', $userInfo);
        $this->display();

    }

    /**
     * 修改密码
     * @author  zgt
     */
    public function changePwd()
    {
        $userInfo = $this->system_user;
        $username = decryptPhone($userInfo['username'], C('PHONE_CODE_KEY'));
        if(IS_POST){
            $request = I('post.');
            if (empty($request['oldPassword']) || strlen($request['oldPassword'])<6) $this->ajaxReturn(1, '旧密码不能为空,请输入密码', '', 'oldPassword');
            if (empty($request['password']) || strlen($request['password'])<6) $this->ajaxReturn(1, '密码不能为空,并大于等于6位', '', 'password');
            if (empty($request['confirmPassword']) || strlen($request['confirmPassword'])<6) $this->ajaxReturn(1, '确认密码不能为空,并大于等于6位', '', 'password');
            if (empty($request['phoneverify']) || strlen($request['phoneverify'])<6) $this->ajaxReturn(1, '验证码不能为空,请输入6位验证码', '', 'phoneverify');
            if ($request['password'] != $request['confirmPassword']) $this->ajaxReturn(1, '您输入的两次密码不一致,请重新输入', '', 'confirmPassword');
            if(session('smsVerifyCode_pwdEdit') != $request['phoneverify']) $this->ajaxReturn(1, '短信验证码不正确，请重新输入', '', 'phoneverify');
            //数据验证
            $userInfo = D('SystemUser')->getSystemUser(array('username'=>$userInfo['username']));
            if($userInfo['password'] != passwd($request['oldPassword'],C('PHONE_CODE_KEY')))  $this->ajaxReturn(1, '您输入的旧密码验证有误', '', 'oldPassword');
            $data['password'] = passwd($request['password'],C('PHONE_CODE_KEY'));
            $reflag = D('SystemUser')->editSystemUser($data, $this->system_user_id);

            if(!empty($reflag)) $this->ajaxReturn(0, '密码修改成功', U('System/Index/main'));
            else $this->ajaxReturn(1, '密码修改失败');
        }
        $this->assign('username', $username);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 呼叫号码设置列表
    |--------------------------------------------------------------------------
    | @author zgt
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
            if($reflag['code']==0) $this->ajaxReturn(0, '操作成功');
            else $this->ajaxReturn(1, $reflag['msg']);
        }
        //获取数据
        $data['numberList'] = D('SystemUser','Service')->getCallNumber(array('system_user_id'=>$this->system_user_id));
        $this->assign('data', $data);
        $this->display();
    }
}