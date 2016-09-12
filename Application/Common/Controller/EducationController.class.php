<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class EducationController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 学历表-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList(){
        if( F('Cache/Personnel/education') ){
            $educationAll = F('Cache/Personnel/education');
        }else{
            $educationAll = D('Education')
                ->where('status=1')
                ->select();
            F('Cache/Personnel/education', $educationAll);
        }
        return array('code'=>0, 'data'=>$educationAll);
    }

    /*
    |--------------------------------------------------------------------------
    | 学历表详情-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($education_id){
        if( F('Cache/Personnel/education') ){
            $educationList = F('Cache/Personnel/education');
            foreach ($educationList as $key => $education) {
                if ($education_id == $education['education_id']) {
                    return array('code'=>0, 'data'=>$education['educationname']);
                }
            }
        }else{
            $education = D('Education')
                ->where("education_id = $education_id and status=1")
                ->find();
            $educationList = D('Education')
                ->where('status=1')
                ->select();
            F('Cache/Personnel/education', $educationList);
        }
        return array('code'=>0, 'data'=>$education['educationname']);
    }
}