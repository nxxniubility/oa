<?php
/*
* 部门服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class DepartmentService extends BaseService
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
        return array('code'=>0, 'data'=>$departmentAll);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取区域详情-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($department_id)
    {
        if( F('Cache/Personnel/department') ){
            $departmentList = F('Cache/Personnel/department');
        }else{
            $departmentList = D('Department')->where("status=1")->select();
            F('Cache/Personnel/department', $departmentList);
        }
        if(!empty($departmentList['data'])){
            foreach($departmentList['data'] as $k=>$v){
                if($v['department_id']==$department_id){
                    $newZoneList = $v;
                }
            }
        }
        return array('code'=>0,'data'=>$newZoneList);
    }
}