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

class DepartmentController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获部门列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getDepartmentList()
    {
        //获取请求？
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        //获取接口服务层
        $result = D('Department','Service')->getDepartmentList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 获取部门及职位列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getDepartmentRoleList()
    {
        //获取接口服务层
        $result = D('Department','Service')->getDepartmentRoleList();
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 添加部门
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addDepartment()
    {
        //获取请求？
        $param['departmentname'] = I('param.departmentname',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Department','Service')->addDepartment($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 修改部门详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editDepartment()
    {
        //获取请求？
        $param['department_id'] = I('param.department_id',null);
        $param['departmentname'] = I('param.departmentname',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Department','Service')->editDepartment($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获部门详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delDepartment()
    {
        //获取请求？
        $param['department_id'] = I('param.department_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Department','Service')->delDepartment($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获部门详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getDepartmentInfo()
    {
        //获取请求？
        $param['department_id'] = I('param.department_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Department','Service')->getDepartmentInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 修改部门排序
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editDepartmentSort()
    {
        //获取请求？
        $param['sort_data'] = I('param.sort_data',null);
        //去除数组空值
        $param = array_filter($param);
        if(empty($param['sort_data'])) $this->ajaxReturn(301, '请输入要修改的排序值');
        $sort_data = $param['sort_data'];
        if($sort_data!='') {
            $sort_data=explode(',',$sort_data);
            $new_sort_data=array();
            foreach($sort_data  as $k=>$v) {
                $tmp=explode('-',$v);
                $new_sort_data[$tmp[0]]=$tmp[1];
            }
            $result = D('Department')->batch_update($new_sort_data,'department_id','sort');
            //排序修改
            if($result!==false){
                F('Cache/department', null);
                $this->ajaxReturn(0, '排序修改成功');
            }else{
                $this->ajaxReturn(100, '排序修改失败');
            }
        }
    }
}