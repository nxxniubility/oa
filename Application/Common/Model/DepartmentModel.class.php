<?php
/*
|--------------------------------------------------------------------------
| 部门数据表
|--------------------------------------------------------------------------
| createtime：2016-04-14
| updatetime：2016-04-14
| updatename：zgt
*/
namespace Common\Model;
use Common\Model\SystemModel;

class DepartmentModel extends SystemModel{

    protected $departmentDb;

    public function _initialize(){

    }

    /**
     * 获取所有部门-缓存
     * @author zgt
     * @return array
     */
    public function getAllDepartment($order='sort desc',$page=null,$where=array('status'=>1)){
        if( F('Cache/Personnel/department') ){
            $departmentAll = F('Cache/Personnel/department');
        }else{
            $departmentAll['data'] = $this->order('department_id asc')->select();
            $departmentAll['count'] = $this->count();
            F('Cache/Personnel/department', $departmentAll);
        }
        $departmentAll = $this->disposeArray($departmentAll, $order, $page, $where);
        return $departmentAll;
    }

    /**
     * 添加部门---清除缓存
     * @author zgt
     * @return array
     */
    public function addDepartment($data){
        $result = $this->field('departmentname,pid,sort')->data($data)->add();
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

    /**
     * 修改部门---清除缓存
     * @author zgt
     * @return array
     */
    public function editDepartment($department_id,$data){
        $result = $this->where("department_id=$department_id")->save($data);
        //更新数据成功执行清除缓存
        if ($result!==false){
            if (F('Cache/Personnel/department')) {
                $newInfo = $this->where("department_id=$department_id")->find();
                $cahceAll = F('Cache/Personnel/department');
                foreach($cahceAll['data'] as $k=>$v){
                    if($v['department_id'] == $department_id){
                        $cahceAll['data'][$k] = $newInfo;
                    }
                }
                F('Cache/Personnel/department', $cahceAll);
            }
            return true;
        }
        return false;
    }

    /**
     * 获取单个部门详情
     * @author zgt
     * @return array
     */
    public function getDepartment($department_id){
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
