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
        if (!$data['productname']) {
            $this->ajaxReturn(1,'产品名称不为空');
        }
        $data['price'] = round($data['price'], 2);
        if(preg_match("/.??/",$data['price']) || preg_match("/[^\d]/",$data['price'])){
            $this->ajaxReturn(3,"优惠金额不能包含其他字符或者'.'出现多次");
        }
        if ($data['price'] == 0) {
            $this->ajaxReturn(3,'产品价格不能过低');
        }
        if(empty($data['productplatform']) || $data['productplatform']==0) $this->ajaxReturn(4,'请选择产品类型');
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
        if(empty($data['productplatform']) || $data['productplatform']==0) $this->ajaxReturn(21,'请选择产品类型');
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