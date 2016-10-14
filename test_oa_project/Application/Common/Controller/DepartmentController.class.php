<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class DepartmentController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有部门-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=array('status'=>1),$order='sort desc',$page=null)
    {
        if( F('Cache/Personnel/department') ){
            $departmentAll = F('Cache/Personnel/department');
        }else{
            $departmentAll['data'] = D('department')->where(array('status'=>1))->order('department_id asc')->select();
            $departmentAll['count'] = D('department')->count();
            F('Cache/Personnel/department', $departmentAll);
        }
        $departmentAll = $this->disposeArray($departmentAll, $order, $page, $where);
        return $departmentAll;
    }

    /*
    |--------------------------------------------------------------------------
    | 添加部门-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function create($data)
    {
        $result = D('Department')->field('departmentname,pid,sort')->data($data)->add();
        //插入数据成功执行清除缓存
        if ($result!==false){
            $data['pages_id'] = $result;
            if (F('Cache/Personnel/department')) {
                $data['department_id'] = $result;
                $data['status'] = 1;
                $cahceAll = F('Cache/Personnel/department');
                $cahceAll['data'][] = $data;
                F('Cache/Personnel/department', $cahceAll);
            }
            return $result;
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改部门-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function edit($data)
    {
        $result = D('Department')->where("department_id={$data['department_id']}")->save($data);
        //更新数据成功执行清除缓存
        if ($result!==false){
            if (F('Cache/Personnel/department')) {
                $newInfo = D('Department')->where("department_id={$data['department_id']}")->find();
                $cahceAll = F('Cache/Personnel/department');
                foreach($cahceAll['data'] as $k=>$v){
                    if($v['department_id'] == $data['department_id']){
                        $cahceAll['data'][$k] = $newInfo;
                    }
                }
                F('Cache/Personnel/department', $cahceAll);
            }
            return true;
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取部门-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($department_id){
        if (F('Cache/Personnel/department')) {
            $departmentAll = F('Cache/Personnel/department');
            foreach($departmentAll['data'] as $k=>$v){
                if($v['department_id']==$department_id){
                    $getDepartment = $v;
                }
            }
        } else {
            $getDepartment = $this->where(array('department_id'=>$department_id))->find();
        }
        return $getDepartment;
    }
}