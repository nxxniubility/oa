<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class CourseController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    * 获取所有课程表-缓存
    * @author zgt
    * @return array
    */
    public function getList()
    {
        if (F('Cache/Promote/course')) {
            $courseAll = F('Cache/Promote/course');
        } else {
            $courseAll = D('Course')->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }

        return array('code'=>0, 'data'=>$courseAll);
    }

    /*
     * 获取所有课程表-缓存
     * @author zgt
     * @return array
     */
    public function getAllCourse()
    {
        if (F('Cache/Promote/course')) {
            $courseAll = F('Cache/Promote/course');
        } else {
            $courseAll = D('Course')->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }
        return array('code'=>0, 'data'=>$courseAll);
    }


    /*
     * 获取指定课程表-缓存
     * @author nxx
     * @return array
     */
    public function getCourse($course_id)
    {

        if( F('Cache/Promote/course') ){
            $courseAll = F('Cache/Promote/course');
            foreach ($courseAll as $key => $course) {
                if ($course_id == $course['course_id']) {
                    return $course['coursename'];
                }
            }
        }else{
            $course = D('Course')
                ->where("course_id = $course_id")
                ->find();
            $courseAll = D('Course')->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }
        return $course['coursename'];
    }
    /*
     * 获取指定课程表-缓存
     * @author nxx
     * @return array
     */
    public function getCourseInfo($course_id)
    {
        if( F('Cache/Promote/course') ){
            $courseAll = F('Cache/Promote/course');
            foreach ($courseAll as $key => $course) {
                if ($course_id == $course['course_id']) {
                    return $course;
                }
            }
        }else{
            $course = D('Course')
                ->where("course_id = $course_id")
                ->find();
            $courseAll = D('Course')->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }
        return $course;
    }
}