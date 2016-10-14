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
   | 添加课程列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addDepartment()
    {
        //获取请求？
        $param['coursename'] = I('param.coursename',null);
        $param['type'] = I('param.type',null);
        $param['keywords'] = I('param.keywords',null);
        $param['description'] = I('param.description',null);
        $param['pic'] = I('param.pic',null);
        $param['litpic'] = I('param.litpic',null);
        $param['sortrank'] = I('param.sortrank',null);
        $param['body'] = I('param.body',null);
        $param['tpl'] = I('param.tpl',null);
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
   | 修改课程列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editDepartment()
    {
        //获取请求？
        $param['course_id'] = I('param.course_id',null);
        $param['coursename'] = I('param.coursename',null);
        $param['type'] = I('param.type',null);
        $param['keywords'] = I('param.keywords',null);
        $param['description'] = I('param.description',null);
        $param['pic'] = I('param.pic',null);
        $param['litpic'] = I('param.litpic',null);
        $param['sortrank'] = I('param.sortrank',null);
        $param['body'] = I('param.body',null);
        $param['tpl'] = I('param.tpl',null);
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
        $param['channel_id'] = I('param.channel_id',null);
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
        $param['channel_id'] = I('param.channel_id',null);
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
}