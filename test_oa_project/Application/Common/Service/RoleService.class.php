<?php
/*
* 职位服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class RoleService extends BaseService
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有职位-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getAllRole($where=array('status'=>1),$order='sort desc',$page=null)
    {
        if( F('Cache/Personnel/role') ) {
            $role = F('Cache/Personnel/role');
        }else{
            $role['data'] = D('Role')
                ->field(array(
                    $this->DB_PREFIX.'role.id',
                    $this->DB_PREFIX.'role.name',
                    $this->DB_PREFIX.'role.superiorid',
                    $this->DB_PREFIX.'role.remark',
                    $this->DB_PREFIX.'role.sort',
                    $this->DB_PREFIX.'role.status',
                    $this->DB_PREFIX.'role.department_id',
                    $this->DB_PREFIX.'role.display',
                    $this->DB_PREFIX.'department.departmentname'
                ))
                ->join('LEFT JOIN __DEPARTMENT__ on __ROLE__.department_id=__DEPARTMENT__.department_id')
                ->select();
            $role['count'] = D('Role')
                ->join('LEFT JOIN __DEPARTMENT__ on __ROLE__.department_id=__DEPARTMENT__.department_id')
                ->count();

            F('Cache/Personnel/role', $role);
        }
        $role = $this->disposeArray($role, $order, $page, $where);
        return array('code'=>0, 'data'=>$role);
    }
}