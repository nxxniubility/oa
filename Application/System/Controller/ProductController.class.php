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
        //获取数据
        $courseProductController = new CourseProductController();
        $list = $courseProductController->getList();
        $data['list'] = $list['data'];
        //获取平台
        $data['proList'] =  C('PRODUCT_PROJECT');
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
        $data = I("post.");
        if(empty($data['productplatform']) || $data['productplatform']==0) $this->ajaxReturn(21,'请选择所属课程');
        //执行操作
        $courseProductController = new CourseProductController();
        $reflag = $courseProductController->cerate_courseProduct($data);
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
        $data = I("post.");
        if(empty($data['productplatform']) || $data['productplatform']==0) $this->ajaxReturn(21,'请选择所属课程');
        //执行操作
        $courseProductController = new CourseProductController();
        $reflag = $courseProductController->edit_courseProduct($data);
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
        $courseProductController = new CourseProductController();
        $data['status'] = 0;
        $reflag = $courseProductController->edit_courseProduct($data);
        if($reflag['code']==0){
            $this->ajaxReturn('0', '产品删除成功');
        }
        $this->ajaxReturn($reflag['code'], $reflag['msg']);
    }

}