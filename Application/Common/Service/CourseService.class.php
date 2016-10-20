<?php
/*
* 课程服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\DataService;
use Common\Service\BaseService;

class CourseService extends BaseService
{
    //初始化
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /**
     * 获取课程列表
     * @return array
     */
    protected function _getCourseList()
    {
        $course['data'] = D('Course')->getList();
        $course['count'] = D('Course')->getCount();
        //转化状态
        $course['data'] = $this->_addStatus($course['data']);
        return $course;
    }

    /**
     * 添加状态
     * @return array
     */
    protected function _addStatus($array=null)
    {
        //添加多职位
        if (!empty($array)) {
            if ((count($array) == count($array, 1))) {
                $_array[] = $array;
            } else {
                $_array = $array;
            }
            $course_type = C('FIELD_STATUS.COURSE_TYPE');
            foreach($_array as $k=>$v){
                $_array[$k]['type_name'] = $course_type[$v['type']];
            }
        }
        //原格式返回
        if ((count($array) == count($array, 1))) {
            return $_array[0];
        } else {
            return $_array;
        }
    }

    /*
   |--------------------------------------------------------------------------
   | Course 获取所有课程
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getCourseList($param)
    {
        $param['order'] = 'sortrank desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/course') ) {
            $course = F('Cache/course');
        }else{
            $course = $this->_getCourseList();
            F('Cache/course', $course);
        }
        $course = $this->disposeArray($course,  $param['order'], $param['page'],  $param['where']);
        return array('code'=>'0', 'data'=>$course);
    }

    /*
   |--------------------------------------------------------------------------
   | Course 课程添加
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addCourse($param)
    {
        //必须参数
        if(empty($param['coursename'])) return array('code'=>300,'msg'=>'缺少课程名称');
        if(empty($param['type'])) return array('code'=>301,'msg'=>'缺少课程类型');
        $result = D('Course')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/course')) {
                $new_info = D('Course')->getFind(array("course_id"=>$result['data']));
                $new_info = $this->_addStatus($new_info);
                $cahce_all = F('Cache/course');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/course', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Course 课程修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editCourse($param)
    {
        //必须参数
        if(empty($param['course_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('Course')->editData($param,$param['course_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/course')) {
                $new_info = D('Course')->getFind(array("course_id"=>$param['course_id']));
                $cahce_all = F('Cache/course');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['course_id'] == $param['course_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/course', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Course 课程删除
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delCourse($param)
    {
        //必须参数
        if(empty($param['course_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('Course')->delData($param['course_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/course')) {
                $cahce_all = F('Cache/course');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['course_id'] == $param['course_id']){
                        unset($cahce_all['data'][$k]);
                    }
                }
                F('Cache/course', $cahce_all);
            }
        }
        return $result;
    }

    /*
   |--------------------------------------------------------------------------
   | Course 获取课程详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getCourseInfo($param)
    {
        //必须参数
        if(empty($param['course_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/course') ) {
            $channe_list = F('Cache/course');
        }else{
            $channe_list = $this->getList();
            F('Cache/course', $channe_list);
        }
        foreach($channe_list['data'] as $k=>$v){
            if($v['course_id']==$param['course_id']){
                $course_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$course_info);
    }


    /**
     * 获取产品列表
     * @return array
     */
    protected function _getCourseProductList()
    {
        $courseProduct['data'] = D('CourseProduct')->getList();
        $courseProduct['count'] = D('CourseProduct')->getCount();
        //转化状态
        $courseProduct['data'] = $this->_addProductStatus($courseProduct['data']);
        return $courseProduct;
    }

    /**
     * 添加状态-产品列表
     * @return array
     */
    protected function _addProductStatus($array=null)
    {
        //添加多职位
        if (!empty($array)) {
            if ( (count($array) == count($array, 1)) ) {
                $_array[] = $array;
            } else {
                $_array = $array;
            }
            //学习平台
            $user_learningtype = C('FIELD_STATUS.USER_LEARNINGTYPE');
            //产品类型
            $course_type = C('FIELD_STATUS.COURSE_TYPE');
            foreach($_array as $k=>$v){
                $_array[$k]['learningtype_name'] = $user_learningtype[$v['productplatform']];
                $_array[$k]['type_name'] = $course_type[$v['producttype']];
            }
        }
        //原格式返回
        if ((count($array) == count($array, 1))) {
            return $_array[0];
        } else {
            return $_array;
        }
    }

    /*
   |--------------------------------------------------------------------------
   | CourseProduct 获取所有产品列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getCourseProductList($param)
    {
        $param['order'] = 'course_product_id desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/courseProduct') ) {
            $course = F('Cache/courseProduct');
        }else{
            $course = $this->_getCourseProductList();
            F('Cache/courseProduct', $course);
        }
        $course = $this->disposeArray($course,  $param['order'], $param['page'],  $param['where']);
        return array('code'=>'0', 'data'=>$course);
    }
    /*
  |--------------------------------------------------------------------------
  | Course 课程添加
  |--------------------------------------------------------------------------
  | @author zgt
  */
    public function addCourseProduct($param)
    {
        //必须参数
        $param = array_filter($param);
        if(empty($param['productname'])) return array('code'=>300,'msg'=>'缺少课程名称');
        if(!isset($param['price'])) return array('code'=>301,'msg'=>'金额不能为空');
        if(!isset($param['productplatform']) || $param['productplatform']=='0') return array('code'=>302,'msg'=>'请选择产品类型');
        if($param['price']==0) return array('code'=>301,'msg'=>'金额不能 "0"');
        $result = D('CourseProduct')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/courseProduct')) {
                $new_info = D('CourseProduct')->getFind(array("course_product_id"=>$result['data']));
                $new_info = $this->_addProductStatus($new_info);
                $cahce_all = F('Cache/courseProduct');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/courseProduct', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | CourseProduct 课程修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editCourseProduct($param)
    {
        //必须参数
        $param = array_filter($param);
        if(empty($param['course_product_id'])) return array('code'=>300,'msg'=>'参数异常');
        if(empty($param['productname'])) return array('code'=>300,'msg'=>'缺少课程名称');
        if(!isset($param['price'])) return array('code'=>301,'msg'=>'金额不能为空');
        if(!isset($param['productplatform']) || $param['productplatform']=='0') return array('code'=>302,'msg'=>'请选择产品类型');
        if($param['price']==0) return array('code'=>301,'msg'=>'金额不能为 "0"');
        $result = D('CourseProduct')->editData($param,$param['course_product_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/courseProduct')) {
                $new_info = D('CourseProduct')->getFind(array("course_product_id"=>$param['course_product_id']));
                $new_info = $this->_addProductStatus($new_info);
                $cahce_all = F('Cache/courseProduct');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['course_product_id'] == $param['course_product_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/courseProduct', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | CourseProduct 课程删除
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delCourseProduct($param)
    {
        //必须参数
        if(empty($param['course_product_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('CourseProduct')->delData($param['course_product_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/courseProduct')) {
                $cahce_all = F('Cache/courseProduct');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['course_product_id'] == $param['course_product_id']){
                        unset($cahce_all['data'][$k]);
                        $cahce_all['count'] = $cahce_all['count']-1;
                    }
                }
                F('Cache/courseProduct', $cahce_all);
            }
        }
        return $result;
    }

}
