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
        //获取请求？
        $param['role_ids'] = I('param.role_ids',null);
        $param['zone_id'] = I('param.zone_id',null);
        $realname = I('param.realname');
        $param['realname'] = !empty($realname)?array('LIKE', $realname):null;
        $param['usertype'] = I('param.usertype',null);
        $param['order'] = I('param.role_ids',null);
        $param['page'] = I('param.page',null);
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
        $result = D('SystemUser','Service')->getSystemUsersInfo($param);
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
            $this->ajaxReturn(0,'获取成功',$result['data']);
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
        $param['username'] = I('param.username',null);
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
            $this->ajaxReturn(0,'获取成功',$result['data']);
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

}