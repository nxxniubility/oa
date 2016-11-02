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

    /*
    |--------------------------------------------------------------------------
    | 消息列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function msgList()
    {
        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='getInfo'){
                $msg_list = D('Message', 'Service')->getMsgInfo($request);
            }else{
                $msg_list = D('Message', 'Service')->getMsgList($request);
            }
            if ($msg_list['code']==0) $this->ajaxReturn(0, '获取成功', $msg_list['data']);
            else $this->ajaxReturn(303);
        }
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
        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='getSystem'){
                if(empty($request['role_id'])) $this->ajaxReturn(302, '请先选中职位');
                $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $where['role_ids'] = $request['role_id'];
                $where['usertype'] = array("IN", "20,30,40,50");
                $where['realname'] = !empty($request['keyname'])?array('LIKE', $request['keyname']):null;
                //员工列表
                $reflag = D('SystemUser','Service')->getSystemUsersList($where);

                if ($reflag['code']==0) $this->ajaxReturn(0, '获取成功', $reflag['data']['data']);
                else $this->ajaxReturn(303);
            }else{
                $request['senduser_id'] = $this->system_user_id;
                if ($request['msgtype'] == 20) {
                    $request['readtype'] = 1;
                }else{
                    $request['readtype'] = 0;
                }

                if (!$request['system_user_id']) {
                    $where['role_id'] = array("IN", $request['role_id']);
                    $systemUsers = D("Role", 'Service')->getRoleUser($where);
                }else{
                    $systemUsers['data'] = explode(',', $request['system_user_id']);
                    unset($request['system_user_id']);
                }
                foreach ($systemUsers['data'] as $key => $systemUser) {
                    $request['system_user_id'] = $systemUser;
                    $result = D("Message", 'Service')->addMsg($request);
                    if ($result['code'] != 0) {
                        $this->ajaxReturn($result['code'], $result['msg']);
                    }
                }
                $this->ajaxReturn(0, "发送成功",  U('System/Information/msgList'));
            }
        }
        //区域
        $zoneList = D("Zone", 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneList['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        $this->assign('data', $data);
        $this->display();
    }

}