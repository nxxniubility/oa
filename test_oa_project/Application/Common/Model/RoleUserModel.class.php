<?php
namespace Common\Model;

use Common\Model\BaseModel;

class RoleUserModel extends SystemModel
{

    /**
     * @author cq
     */
    public function  _initialize()
    {

    }

    /**
     * 获取指定用户所属用户组
     * @author cq
     * @param string $where
     * @return array
     */
    public function getRoleUser($where = "")
    {
        $result = $this->field('role_id')->where($where)->select();
        $role_id = array();
        foreach ($result as $key => $value) {
            $role_id[] = $value['role_id'];
        }
        return $role_id;
    }

    /**
     * 删除指定的用户
     * @author cq
     * @param string $where
     * @return mixed
     */
    public function delRoleUser($where)
    {
        return $this->where($where)->delete();
    }

    /**
     * 获取指定用户组的用户
     * @author cq
     * @return array
     */
    public function getAllRoleUser($where = array(), $field = "*")
    {
        return $this->field($field)->where($where)->select();
    }

    /*
     * userid=>获取员工权限
     * @author zgt
     * @return array
     */
    public function getSystemUserRole($systemUserId){
        return $this
            ->field('name,role_id,departmentname')
            ->where(array('user_id'=>$systemUserId))
            ->join('__ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->select();
    }
}