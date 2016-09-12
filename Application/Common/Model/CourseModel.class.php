<?php
/*
|--------------------------------------------------------------------------
| 课程表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class CourseModel extends SystemModel
{
    protected $courseDb;

    public function _initialize()
    {
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
            $courseAll = $this->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }

        return $courseAll;
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
            $course = $this
                ->where("course_id = $course_id")
                ->find();
            $courseAll = $this->order('sortrank DESC')->select();
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
            $course = $this
                ->where("course_id = $course_id")
                ->find();
            $courseAll = $this->order('sortrank DESC')->select();
            F('Cache/Promote/course', $courseAll);
        }
        return $course;
    }


}