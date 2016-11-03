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

class RoleController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获职位列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getRoleList()
    {
        //获取请求？
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        $param['department_id'] = I('param.department_id',null);
        //获取接口服务层
        $result = D('Role','Service')->getRoleList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,"获取成功",$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 添加职位
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addRole()
    {
        //获取请求？
        $param['name'] = I('param.name',null);
        $param['remark'] = I('param.remark',null);
        $param['department_id'] = I('param.department_id',null);
        $param['access'] = I('param.access',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Role','Service')->addRole($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 修改职位
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editRole()
    {
        //获取请求？
        $param['role_id'] = I('param.role_id',null);
        $param['name'] = I('param.name',null);
        $param['remark'] = I('param.remark',null);
        $param['department_id'] = I('param.department_id',null);
        $param['access'] = I('param.access',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Role','Service')->editRole($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 删除职位
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delRole()
    {
        //获取请求？
        $param['role_id'] = I('param.role_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Role','Service')->delRole($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获职位详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getRoleInfo()
    {
        //获取请求？
        $param['role_id'] = I('param.role_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Role','Service')->getRoleInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取职位对应节点
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getRoleNode()
    {
        //获取请求？
        $param['role_id'] = I('param.role_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取职位详情
        $result = D('Role','Service')->getRoleNode(array('role_id'=>$param['role_id'],'type'=>1));
        if(!empty($result['data'])){
            $roleAccess = '';
            foreach($result['data'] as $k=>$v){
                if($k==0){
                    $roleAccess.=$v['node_id'];
                }else{
                    $roleAccess.=','.$v['node_id'];
                }
            }
        }
        $this->ajaxReturn(0, '获取职位权成功', $roleAccess);
    }

}