<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\CourseController;
use Common\Controller\CourseProductController;

class ProductController extends SystemController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 产品线管理
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function courseProductList()
    {
        //获取参数 页码
        $request = I('get.');
        $where['usertype'] = 10;
        $re_page = isset($request['page'])?$request['page']:1;
        $_param['page'] = $re_page.',30';
        $list = D('Course', 'Service')->getCourseProductList($_param);
        $data['list'] = $list['data']['data'];
        //获取平台
        $data['proList'] = C('FIELD_STATUS.USER_LEARNINGTYPE');
        //加载分页类
        $paging_data = $this->Paging($re_page, 30, $list['data']['count'], $request);
        $data['paging'] = $paging_data;
        //模版赋值
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加课程产品
     * @author zgt
     */
    public function createCourseProduct()
    {
        //获取数据
        $_param = I("post.");
        $_param['productname'] = trim($_param['productname']);
        $reflag = D('Course', 'Service')->addCourseProduct($_param);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '产品添加成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 修改课程产品
     * @author zgt
     */
    public function editCourseProduct()
    {
        //获取数据
        $_param = I("post.");
        $_param['productname'] = trim($_param['productname']);
        //执行操作
        $reflag = D('Course', 'Service')->editCourseProduct($_param);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '产品修改成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

    /**
     * 删除课程产品
     * @author zgt
     */
    public function delCourseProduct()
    {
        //获取数据
        $data = I("post.");
        //执行操作
        $data['status'] = 0;
        $reflag = D('Course', 'Service')->delCourseProduct($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '产品删除成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

}