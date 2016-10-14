<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class CourseProductController extends BaseController
{
    /*
    * 获取所有课产品表-缓存
    * @author zgt
    * @return array
    */
    public function getList()
    {
        if (F('Cache/Promote/courseProduct')) {
            $courseAll = F('Cache/Promote/courseProduct');
        } else {
            $courseAll = D('CourseProduct')->where(array('status'=>1))->select();
            F('Cache/Promote/courseProduct', $courseAll);
        }
        $courseList['data'] = $courseAll;
        $courseList = $this->disposeArray($courseList, 'course_product_id desc');
        return array('code'=>0, 'data'=>$courseList['data']);
    }

    /*
    * 创建课产品表
    * @author zgt
    * @return array
    */
    public function cerate_courseProduct($data)
    {
        //必须参数
        if(empty($data['productname'])) return array('code'=>2,'msg'=>'参数异常');
        $reflag = D('CourseProduct')->add($data);
        if($reflag!==false) {
            F('Cache/Promote/courseProduct', null);
            return array('code'=>0,'msg'=>'产品添加成功');
        }
        return array('code'=>1,'msg'=>'产品添加失败');
    }

    /*
    * 修改课产品表
    * @author zgt
    * @return array
    */
    public function edit_courseProduct($data)
    {
        //必须参数
        if(empty($data['course_product_id'])) return array('code'=>2,'msg'=>'参数异常');
        $reflag = D('CourseProduct')->where(array('course_product_id'=>$data['course_product_id']))->save($data);
        if($reflag!==false) {
            F('Cache/Promote/courseProduct', null);
            return array('code'=>0,'msg'=>'产品操作成功');
        }
        return array('code'=>1,'msg'=>'产品操作失败');
    }
}