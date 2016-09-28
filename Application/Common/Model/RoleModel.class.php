<?php
/*
|--------------------------------------------------------------------------
| 职位管理表（原用户权限组role）
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：2016-04-18
| updatename：zgt
*/
namespace Common\Model;
use Common\Model\SystemModel;

class RoleModel extends SystemModel{


    /**
     * 获取所有职位-缓存
     * @author zgt
     * @return array
     */
    public function getAllRole($order='sort desc',$page=null,$where=array('status'=>1)){
        if( F('Cache/Personnel/role') ) {
            $role = F('Cache/Personnel/role');
        }else{
            $role['data'] = $this
            ->field(array(
                C('DB_PREFIX').'role.id',
                C('DB_PREFIX').'role.name',
                C('DB_PREFIX').'role.superiorid',
                C('DB_PREFIX').'role.remark',
                C('DB_PREFIX').'role.sort',
                C('DB_PREFIX').'role.status',
                C('DB_PREFIX').'role.department_id',
                C('DB_PREFIX').'role.display',
                C('DB_PREFIX').'department.departmentname'
            ))
            ->join('LEFT JOIN __DEPARTMENT__ on __ROLE__.department_id=__DEPARTMENT__.department_id')
            ->select();
            $role['count'] = $this
                ->join('LEFT JOIN __DEPARTMENT__ on __ROLE__.department_id=__DEPARTMENT__.department_id')
                ->count();

            F('Cache/Personnel/role', $role);
        }
        $role = $this->disposeArray($role, $order, $page, $where);
        return $role;
    }
    /**
     * 获取所有职位-缓存
     * @author zgt
     * @return array
     */
    public function getAllRoleData(){
        if( F('Cache/Personnel/roledata') ) {
            $role = F('Cache/Personnel/roledata');
        }else{
            $role['data'] = $this->select();
            $role['count'] = $this ->count();

            F('Cache/Personnel/roledata', $role);
        }
      
        return $role;
    }

    /**
     * 新增职位-清除缓存
     * @author zgt
     * @return array
     */
    public function addRole($dataRole,$dataAccess){
        $result = $this->data($dataRole)->add();
        //插入数据成功执行清除缓存
        if($result!==false){
            $accessDb = M('access');
            foreach($dataAccess as $k=>$v){
                $v['role_id'] = $result;
                $accessDb->field('role_id,node_id,level,superiorid')->data($v)->add();
            }
            $data['role_id'] = $result;
            if (F('Cache/Personnel/role')) {
                F('Cache/Personnel/role', null);
            }
            return $result;
        }
        return false;
    }

    /**
     * 修改职位-清除缓存
     * @author zgt
     * @return array
     */
    public function editRole($dataRole=null,$dataAccess=null,$role_id){
        $result = $this->where(array('id'=>$role_id))->save($dataRole);
        //插入数据成功执行清除缓存
        if($result!==false || !empty($dataAccess)){
            $accessDb = M('access');
            $accessDb->where(array('role_id'=>$role_id))->delete();
            foreach($dataAccess as $k=>$v){
                $v['role_id'] = $role_id;
                $accessDb->data($v)->add();
            }
            if (F('Cache/Personnel/role')) {
                F('Cache/Personnel/role', null);
            }
            $result = true;
        }
        return true;
    }

    /**
     * 获取职位详情
     * @author zgt
     * @return array
     */
    public function getRoleInfo($role_id){
        if( F('Cache/Personnel/role') ){
            $roleAll = F('Cache/Personnel/role');
            foreach($roleAll['data'] as $k=>$v){
                if($v['id']==$role_id) $result = $v;
            }
        }else{
            $result = $this->where(array('id'=>$role_id))->find();
        }
        return $result;
    }
    /**
     * 获取职位权限节点列表
     * @author zgt
     * @return array
     */
    public function getRoleAccess($role_id){
        $result = M('access')
            ->where(array(C('DB_PREFIX').'access.role_id'=>$role_id,C('DB_PREFIX').'node.status'=>1))
            ->join("__NODE__ on __NODE__.id=__ACCESS__.node_id")
            ->select();
        return $result;
    }

    /**
     * userid - 获取对应权限ID
     * @author zgt
     * @return array
     */
    public function getRoleUser($user_id){
        $result = $this
            ->field(C('DB_PREFIX').'role.id,'.C('DB_PREFIX').'role.name')
            ->where(array(C('DB_PREFIX').'role_user.user_id'=>$user_id,C('DB_PREFIX').'role.status'=>1))
            ->join("__ROLE_USER__ on __ROLE__.id=__ROLE_USER__.role_id")
            ->find();
        return $result;
    }

    /**
     * userid - 获取对应节点
     * @author zgt
     * @return array
     */
    public function getRoleNode($role_id=null){
        $where[C('DB_PREFIX').'node.status'] = 1;
        if($role_id==null){
            $role_ids = array();
            $system_user_role = session('system_user_role');
            foreach($system_user_role as $k=>$v){
                $role_ids[] = $v['role_id'];
            }
            $where[C('DB_PREFIX').'access.role_id'] = array('IN',$role_ids);
        }else{
            $where[C('DB_PREFIX').'access.role_id'] = $role_id;
        }
        $result = M('access')
            ->where($where)
            ->join("__NODE__ on __NODE__.id = __ACCESS__.node_id")
            ->group("node_id")->Distinct(true)
            ->select();
        $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $sidebar = $arrayhelps->createTree($result, 2,'id','pid');
        return $sidebar;
    }

    /**
     * 获取所有节点
     * @author zgt
     * @return array
     */
    public function getRoleNodeAll(){
        $where[C('DB_PREFIX').'node.display'] = 3;
        $result = M('node')
            ->where($where)
            ->order('sort asc')
            ->select();
        $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $sidebar = $arrayhelps->createTree($result, 2,'id','pid');
        return $sidebar;
    }

	/*
	zone_id 获取想关联的ID
	@author Nixx
	*/
	public function getSubRoleIds($role_id = 0)
	{
		$roles=$this->getAllRoleData();
		
		//数组分级
		$Arrayhelps = new \Org\Arrayhelps\Arrayhelps();

		$newZoneList = $Arrayhelps->subFinds($roles['data'],$role_id,'id','superiorid');
        foreach($roles['data'] as $k=>$v){
            if($v['id']==$role_id){
                $newZoneList[] = $v;
            }
        }
	
		return $newZoneList;
	}

    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }
}
