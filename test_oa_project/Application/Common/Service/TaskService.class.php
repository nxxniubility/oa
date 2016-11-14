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
        $_task_list = D('TaskDepartment')->getList(array('department_id'=>$_department_id));
        print_r($_task_list);
    }
}