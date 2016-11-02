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

    /**
     * 获取列表
     * @return array
     */
    protected function _getList()
    {
        $list['data'] = D('Role')->getList();
        $list['count'] = D('Role')->getCount();
        $list['data'] = $this->_addStatus($list['data']);
        return $list;
    }
    /**
     * 添加状态
     * @return array
     */
    protected function _addStatus($array=null){
        if (empty($array[0])) {
            $arrStr[0] = $array;
        } else {
            $arrStr = $array;
        }
        foreach($arrStr as $k=>$v){
            $department = D('Department', 'Service')->getDepartmentInfo(array('department_id'=>$v['department_id']));
            $arrStr[$k]['department_name'] = $department['data']['departmentname'];
        }
        if(empty($array[0])){
            return $arrStr[0];
        }else{
            return $arrStr;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有职位-文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleList($param)
    {
        $param['status'] = 1;
        $param['order'] = !empty($param['order'])?$param['order']:'sort desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/role') ){
            $roleAll = F('Cache/role');
        }else{
            $roleAll = $this->_getList();
            F('Cache/role', $roleAll);
        }
        $roleAll = $this->disposeArray($roleAll,  $param['order'], $param['page'],  $param);
        return array('code'=>0, 'data'=>$roleAll);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加职位---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addRole($param)
    {
        $param = array_filter($param);
        //必须参数
        if(empty($param['name'])) return array('code'=>300,'msg'=>'职位名称不能为空');
        if(empty($param['remark'])) return array('code'=>301,'msg'=>'请添加该职位描述');
        if(empty($param['department_id'])) return array('code'=>301,'msg'=>'请选择所属部门');
        if(empty($param['access'])) return array('code'=>301,'msg'=>'请先设置权限');
        $result = D('Role')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/role')) {
                $new_info = D('Role')->getFind(array("id"=>$result['data']));
                $new_info = $this->_addStatus($new_info);
                $cahce_all = F('Cache/role');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/role', $cahce_all);
            }
            foreach($param['access'] as $k=>$v){
                $v['role_id'] = $result['data'];
                D('Access')->addData($v);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改职位---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editRole($param)
    {
        $param = array_filter($param);
        //必须参数
        if(empty($param['role_id'])) return array('code'=>300,'msg'=>'参数异常');
        if(empty($param['access'])) return array('code'=>301,'msg'=>'请先设置权限');
        $result = D('Role')->editData($param,$param['role_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/role')) {
                $new_info = D('Role')->getFind(array("id"=>$param['role_id']));
                $new_info = $this->_addStatus($new_info);
                $cahce_all = F('Cache/role');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['id'] == $param['role_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/role', $cahce_all);
            }
        }
        if(!empty($param['access'])){
            D('Access')->delData($param['role_id']);
            foreach($param['access'] as $k=>$v){
                $v['role_id'] = $param['role_id'];
                D('Access')->addData($v);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 删除职位---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delRole($param)
    {
        //必须参数
        if(empty($param['role_id'])) return array('code'=>300,'msg'=>'参数异常');
        $param['status'] = 0;
        $result = D('Role')->editData($param,$param['role_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/role')) {
                $new_info = D('Role')->getFind(array("id"=>$param['role_id']));
                $cahce_all = F('Cache/role');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['id'] == $param['role_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/role', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取职位详情-文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleInfo($param)
    {
        //必须参数
        if(empty($param['role_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/role') ) {
            $role_list = F('Cache/role');
        }else{
            $role_list = $this->_getList();
            F('Cache/role', $role_list);
        }
        foreach($role_list['data'] as $k=>$v){
            if($v['id']==$param['role_id']){
                $role_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$role_info);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加职位关联员工---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addRoleUser($param)
    {
        //必须参数
        if(empty($param['role_id'])) return array('code'=>300,'msg'=>'参数异常');
        if(empty($param['system_user_id'])) return array('code'=>301,'msg'=>'缺少所属职位');
        $result = D('Role')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/role')) {
                $new_info = D('Role')->getFind(array("id"=>$result['data']));
                $cahce_all = F('Cache/role');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/role', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取职位对应节点
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleNode($param)
    {
        if($param['role_id']==null){
            $role_ids = array();
            $system_user_role = session('system_user_role');
            foreach($system_user_role as $k=>$v){
                $role_ids[] = $v['id'];
            }
            $_where['role_id'] = array('IN',$role_ids);
        }else{
            $_where['role_id'] = $param['role_id'];
        }
        $_where['A.status'] = 1;
        if(empty($param['type'])) {
            $_where['A.display'] = 3;
        }
        $join = '__NODE__ A on  A.id = __ACCESS__.node_id';
        $result = D('Access')->getList($_where,null,null,null,$join,'node_id');
        if(empty($param['type'])){
            $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $result = $arrayhelps->createTree($result, 2,'id','pid');
        }
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取超级管理员职位对应节点
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleNodeAll()
    {
        $_where['status'] = 1;
        $_where['display'] = 3;
        $result = D('Node')->getList($_where);
        $arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $sidebar = $arrayhelps->createTree($result, 2,'id','pid');
        return array('code'=>'0', 'data'=>$sidebar);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取职位对应员工
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleUser($param)
    {
        $_where['role_id'] = $param['role_id'];
        $redata = D('RoleUser')->getList($_where,'user_id');
        $_array = array();
        if(!empty($redata)){
            foreach($redata as $k=>$v){
                $_array[] = $v['user_id'];
            }
        }
        return array('code'=>'0', 'data'=>$_array);
    }
}
