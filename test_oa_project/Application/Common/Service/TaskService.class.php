<?php
/*
|--------------------------------------------------------------------------
| 今日任务数据相关
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Common\Service;
use Common\Service\BaseService;

class TaskService extends BaseService
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getTaskList()
    {
        $system_user_id = $this->system_user_id;
        //获取部门ID
        $_user_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$system_user_id));
        //获取默认第一个职位的部门ID
        $_department_id = $_user_info['data']['roles'][0]['department_id'];
        //获取配置
        $join = '__TASK__ on __TASK__.task_id=__TASK_DEPARTMENT__.task_id';
        $_task_list = D('TaskDepartment')->getList(array('department_id'=>$_department_id),'*',null,null,$join);
        foreach($_task_list as $k=>$v){

        }
        print_r($_task_list);
        return array('code'=>'0', 'data'=>$_task_list);
    }


    /*
    |--------------------------------------------------------------------------
    | 获取今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addTask($param)
    {

        //获取部门ID
        $_add_flag = D('TaskDepartment')->addData($param);
        if($_add_flag!==false){
            return array('code'=>0,'msg'=>'添加成功');
        }
        return array('code'=>100,'msg'=>'添加失败');
    }
}