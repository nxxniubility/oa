<?php
/*
|--------------------------------------------------------------------------
| 课程数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class CourseController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取课程列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getCourseList()
    {
        //获取接口服务层
        $result = D('Course','Service')->getCourseList();
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
    public function addCourse()
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
        $result = D('Course','Service')->addCourse($param);
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
    public function editCourse()
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
        $result = D('Course','Service')->editCourse($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 删除课程详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delCourse()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Course','Service')->delCourse($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取课程详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getCourseInfo()
    {
        //获取请求？
        $param['channel_id'] = I('param.channel_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Course','Service')->getCourseInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

}