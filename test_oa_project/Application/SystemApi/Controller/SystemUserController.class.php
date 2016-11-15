<?php
/*
|--------------------------------------------------------------------------
| 员工数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class SystemUserController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取员工列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUserList()
    {
        //获取请求？x
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        $param['zone_id'] = I('param.zone_id',$this->system_user['zone_id']);
        $param['usertype'] = I('param.usertype',null);
        $param['role_ids'] = I('param.role_ids',null);
        $param['key_name'] = I('param.key_name',null);
        $param['key_value'] = I('param.key_value',null);
        $param['status'] = 1;
        if($param['key_name'] && $param['key_value']){
            if($param['key_name']=='username'){
                $param[$param['username']] = encryptPhone($param['key_value'],  C('PHONE_CODE_KEY'));
            }else{
                $param[$param['key_name']] = array('LIKE',$param['key_value']);
            }
        }
        unset($param['key_name']);unset($param['key_value']);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->getSystemUsersList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取员工详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUserInfo()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->getSystemUserInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 添加员工基本信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addSystemUser()
    {
        //获取请求？
        $param['realname'] = I('param.realname',null);
        $param['username'] = I('param.username',null);
        $param['email'] = I('param.email',null);
        $param['sex'] = I('param.sex',null);
        $param['zone_id'] = I('param.zone_id',null);
        $param['role_id'] = I('param.role_id',null);
        $param['usertype'] = I('param.usertype',null);
        $param['check_id'] = I('param.check_id',null);
        $param['entrytime'] = I('param.entrytime',null);
        $param['straightime'] = I('param.straightime',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->addSystemUser($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改员工基本信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editSystemUser()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        $param['realname'] = I('param.realname',null);
        $param['username'] = I('param.username',null);
        $param['email'] = I('param.email',null);
        $param['sex'] = I('param.sex',null);
        $param['zone_id'] = I('param.zone_id',null);
        $param['role_id'] = I('param.role_id',null);
        $param['usertype'] = I('param.usertype',null);
        $param['check_id'] = I('param.check_id',null);
        $param['entrytime'] = I('param.entrytime',null);
        $param['straightime'] = I('param.straightime',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->editSystemUser($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 调整员工下客户区域
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editUserZone()
    {
        //去除数组空值
        $param['system_user_id'] = I('param.system_user_id',null);
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->editUserZone($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改员工档案信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editSystemUserInfo()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        $param['birthday'] = I('param.birthday',null);
        $param['nativeplace'] = I('param.nativeplace',null);
        $param['education_id'] = I('param.education_id',null);
        $param['school'] = I('param.school',null);
        $param['plivatemail'] = I('param.plivatemail',null);
        $param['usertype'] = I('param.usertype',null);
        $param['check_id'] = I('param.check_id',null);
        $param['entrytime'] = I('param.entrytime',null);
        $param['straightime'] = I('param.straightime',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->editSystemUserInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 员工 离职
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function dimission()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->delSystemUser($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 员工 离线
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function offLineSystemUser()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->removeToken($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 员工 删除
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delSystemUser()
    {
        //获取请求？
        $param['system_user_id'] = I('param.system_user_id',null);
        $param['flag'] = 'del';
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUser','Service')->delSystemUser($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 呼叫号码设置列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getCallNumberList()
    {
        //获取数据
        $result = D('SystemUser','Service')->getCallNumber();
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加呼叫号码
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addCallNumber()
    { 
        $param['number'] = I('param.number', null);
        $param['number_type'] = I('param.number_type', null);
        $result = D('SystemUser','Service')->addCallNumber($param);
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 删除呼叫号码
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function delCallNumber()
    { 
        $param['call_number_id'] = I('param.call_number_id', null);
        $result = D('SystemUser','Service')->delCallNumber($param);
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 修改呼叫号码
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editCallNumber()
    {   
        $param['call_number_id'] = I('param.call_number_id', null);
        $param['number'] = I('param.number', null);
        $param['number_type'] = I('param.number_type', null);
        $result = D('SystemUser','Service')->editCallNumber($param);
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 启用呼叫号码
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function startCallNumber()
    { 
        $param['call_number_id'] = I('param.call_number_id', null);
        $param['number_start'] = I('param.number_start', null);
        $result = D('SystemUser','Service')->startCallNumber($param);
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }
    
}