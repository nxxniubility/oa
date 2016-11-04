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
     | 获取列表
     |--------------------------------------------------------------------------
     | @author zgt
     | updata nxx
     */
    protected function _getDepartmentList()
    {
        $departmentAll['data'] = D('Department')->getList();
        $departmentAll['count'] = D('Department')->getCount();
        return $departmentAll;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取所有部门-文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    | updata nxx
    */
    public function getDepartmentList($param)
    {
        $param['status'] = 1;
        $param['order'] = !empty($param['order'])?$param['order']:'sort desc';
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/department') ){
            $departmentAll = F('Cache/department');
        }else{
            $departmentAll = $this->_getDepartmentList();
            F('Cache/department', $departmentAll);
        }
        $departmentAll = $this->disposeArray($departmentAll,  $param['order'], $param['page'], $param);
        return array('code'=>0, 'data'=>$departmentAll);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取部门及职位列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDepartmentRoleList()
    {
        $department_list = $this->getDepartmentList();
        if($department_list['data']['data']){
            foreach($department_list['data']['data'] as $k=>$v){
                $roel_list = D('Role','Service')->getRoleList(array('department_id'=>$v['department_id']));
                $department_list['data']['data'][$k]['children'] = $roel_list['data']['data'];
            }
        }
        return array('code'=>'0', 'data'=>$department_list['data']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加部门---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDepartment($param)
    {
        //必须参数
        if(empty($param['departmentname'])) return array('code'=>300,'msg'=>'缺少部门名称');
        $result = D('Department')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/department')) {
                $new_info = D('Department')->getFind(array("department_id"=>$result['data']));
                $cahce_all = F('Cache/department');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/department', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改部门---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editDepartment($param)
    {
        //必须参数
        if(empty($param['department_id'])) return array('code'=>300,'msg'=>'缺少参数');
        $result = D('Department')->editData($param,$param['department_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/department')) {
                $new_info = D('Department')->getFind(array("department_id"=>$param['department_id']));
                $cahce_all = F('Cache/department');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['department_id'] == $param['department_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/department', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 删除部门详情---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delDepartment($param)
    {
        //必须参数
        if(empty($param['department_id'])) return array('code'=>300,'msg'=>'参数异常');
        $param['status'] = 0;
        $result = D('Department')->editData($param,$param['department_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/department')) {
                $new_info = D('Department')->getFind(array("department_id"=>$param['department_id']));
                $cahce_all = F('Cache/department');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['department_id'] == $param['department_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/department', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取部门详情---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDepartmentInfo($param)
    {
        //必须参数
        if(empty($param['department_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/department') ) {
            $department_list = F('Cache/department');
        }else{
            $department_list = $this->_getDepartmentList();
            F('Cache/department', $department_list);
        }
        foreach($department_list['data'] as $k=>$v){
            if($v['department_id']==$param['department_id']){
                $department_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$department_info);
    }
}
