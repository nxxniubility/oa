<?php
/*
* 课程接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class CourseService extends BaseService
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
}