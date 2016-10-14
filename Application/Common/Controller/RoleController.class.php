<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class RoleController extends BaseController
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
        return $role;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取职位详情-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($role_id){
        if( F('Cache/Personnel/role') ){
            $roleAll = F('Cache/Personnel/role');
            foreach($roleAll['data'] as $k=>$v){
                if($v['id']==$role_id) $result = $v;
            }
        }else{
            $result = D('Role')->where(array('id'=>$role_id))->find();
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 新增职位-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function create($dataRole,$dataAccess){
        $result = D('Role')->data($dataRole)->add();
        //插入数据成功执行清除缓存
        if (F('Cache/Personnel/role')) {
            F('Cache/Personnel/role', null);
        }
        if($result!==false){
            $accessDb = D('Access');
            foreach($dataAccess as $k=>$v){
                $v['role_id'] = $result;
                $accessDb->field('role_id,node_id,level,superiorid')->data($v)->add();
            }
            $data['role_id'] = $result;
            return $result;
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改职位-缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function edit($dataRole=null,$dataAccess=null,$role_id){
        $result = D('Role')->where(array('id'=>$role_id))->save($dataRole);
        //插入数据成功执行清除缓存
        if (F('Cache/Personnel/role')) {
            F('Cache/Personnel/role', null);
        }
        if($result!==false || !empty($dataAccess)){
            $accessDb = D('Access');
            $accessDb->where(array('role_id'=>$role_id))->delete();
            foreach($dataAccess as $k=>$v){
                $v['role_id'] = $role_id;
                $accessDb->data($v)->add();
            }
            return true;
        }
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取职位权限节点列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleAccess($role_id)
    {
        $where[$this->DB_PREFIX.'access.role_id'] = $role_id;
        $where[$this->DB_PREFIX.'node.status'] = 1;
        $join = 'INNER JOIN __NODE__ on __NODE__.id=__ACCESS__.node_id';
        return D('Access')->getList($where, null, null, '*', $join);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工对应职位关系列表
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getRoleUserList($system_user_id=null)
    {
        if( F('Cache/Personnel/roleUser') ) {
            $role = F('Cache/Personnel/roleUser');
        }else{
            $role['data'] = D('RoleUser')->select();
            $role['count'] = D('RoleUser')->count();
            F('Cache/Personnel/roleUser', $role);
        }
        if($system_user_id!==null){
            $user_role = array();
            foreach($role['data'] as $k=>$v){
                if($v['user_id']==$system_user_id){
                    $user_role['data'][] = $v;
                }
            }
            $user_role['count'] = count($user_role['data']);
            return array('code'=>'0', 'msg'=>'操作成功', 'data'=>$user_role);
        }
        return array('code'=>'0', 'msg'=>'操作成功', 'data'=>$role);
    }
}