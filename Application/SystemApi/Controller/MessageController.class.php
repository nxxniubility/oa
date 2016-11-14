<?php
/*
|--------------------------------------------------------------------------
| 部门相关的数据接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemUserService;

class MessageController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取消息列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgList()
    {
        //获取请求？
        $param['isread'] = I('param.isread',null);
        $param['msgtype'] = I('param.msgtype',null);
        $param['page'] = I('param.page',null);
        //获取接口服务层
        $result = D('Message','Service')->getMsgList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取消息信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgInfo()
    {
        //获取请求？
        $param['message_id'] = I('param.message_id',null);
        //获取接口服务层
        $result = D('Message','Service')->getMsgInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取当前消息提示
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getMsgHint()
    {
        //获取接口服务层
        $result = D('Message','Service')->getMsgHint();
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
    |--------------------------------------------------------------------------
    | 删除消息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function delMsg()
    {
        $param['message_id'] = I('param.message_id', null);
        $msg_list = D('Message', 'Service')->delMsg($param);
        if ($msg_list['code']==0) {
            $this->ajaxReturn(0, '删除成功', $msg_list['data']);
        }
        else {
            $this->ajaxReturn($msg_list['code'], $msg_list['msg']);
        }
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
                $where['role_ids'] = array("IN", $request['role_id']);
                $where['usertype'] = array("NEQ", "10");
                $where['realname'] = !empty($request['keyname'])?array('LIKE', $request['keyname']):null;
                //员工列表
                $reflag = D('SystemUser','Service')->getSystemUsersList($where);
                if ($reflag['code']==0) $this->ajaxReturn(0, '获取成功', $reflag['data']['data']);
                else $this->ajaxReturn(303);
            }else{
                $result = D('Message', 'Service')->sendMsgs($request);
                if ($result['code'] != 0) {
                    $this->ajaxReturn($result['code'], "发送失败");
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