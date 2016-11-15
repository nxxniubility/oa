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
    | 获取任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getTask()
    {
        $_task_list = D('Task')->getList();

        return array('code'=>'0', 'msg'=>'获取成功', 'data'=>$_task_list);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取任务设置列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getTaskList()
    {
        //获取已设置的部门列表
        $_department_list = D('TaskDepartment')->group('department_id')->order('department_id desc')->select();
        if(!empty($_department_list)){
            foreach($_department_list as $k=>$v){
                //获取设置项
                $_join = '__TASK__ on __TASK__.task_id=__TASK_DEPARTMENT__.task_id';
                $_task_name_list = D('TaskDepartment')->getList(array('department_id'=>$v['department_id']),'task_name',null,null,$_join);
                $_task_names = '';
                if(!empty($_task_name_list)){
                    foreach($_task_name_list as $k2=>$v2){
                        $_prefix = ($k2==0)?'':',';
                        $_task_names .= $_prefix.$v2['task_name'];
                    }
                }
                $_department_list[$k]['task_names'] = $_task_names;
                //获取
                $_department_info = D('Department','Service')->getDepartmentInfo(array('department_id'=>$v['department_id']));
                $_department_list[$k]['department_name'] = $_department_info['data']['departmentname'];
            }
        }
        return array('code'=>'0', 'msg'=>'获取成功', 'data'=>$_department_list);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取自己今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMyTask($param)
    {
        $system_user_id = $this->system_user_id;
        //获取部门ID
        if(empty($param['role_id'])){
            $_user_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$system_user_id));
            //获取默认第一个职位的部门ID
            $_department_id = $_user_info['data']['roles'][0]['department_id'];
        }else{
            $_role_info = D('Role','Service')->getRoleInfo(array('role_id'=>$param['role_id']));
            $_department_id = $_role_info['data']['department_id'];
        }
        //获取配置
        $_join = '__TASK__ on __TASK__.task_id=__TASK_DEPARTMENT__.task_id';
        $_task_list = D('TaskDepartment')->getList(array('department_id'=>$_department_id),'*',null,null,$_join);
        foreach($_task_list as $k=>$v){
            $_where = ' and (system_user_id='.$system_user_id.' or '.'updateuser_id='.$system_user_id.')';
            $_task_where_select = str_replace('{Ymd}',date('Y-m-d'),$v['task_where']);
            $_task_where_select = explode('&',$_task_where_select);
            $_task_list[$k]['task_where'] = '';
            foreach($_task_where_select as $k2=>$v2){
                $_prefix = ($k2==0)?'':' and ';
                $_prefix_url = ($k2==0)?'':'&';
                if(strpos($v2,'@')!==false){
                    $_key = explode('=',$v2);
                    $_value = explode('@',$_key[1]);
                    //获取匹配项
                    $_value = $this->regval($_value);
                    if(count($_value)>1){
                        $_task_list[$k]['task_where_select'] .= $_prefix.$_key[0].'>'.$_value[0].' and '.$_key[0].'<'.$_value[1];
                        $_task_list[$k]['task_where'] .= $_prefix_url.$_key[0].'='.date('Y-m-d',$_value[0]).'@'.date('Y-m-d',$_value[1]);
                    }else{
                        $_task_list[$k]['task_where_select'] .= $_prefix.$_key[0].'='.$_value[0];
                        $_task_list[$k]['task_where'] .= $_prefix_url.$_key[0].'='.date('Y-m-d',$_value[0]);
                    }
                }else{
                    $_task_list[$k]['task_where_select'] .= $_prefix.$v2;
                    $_task_list[$k]['task_where'] .= $_prefix_url.$v2;
                }
            }
            $_task_list[$k]['task_where_select'] .= $_where;
            $_task_list[$k]['count'] = D('User')->where($_task_list[$k]['task_where_select'])->count();
        }
        return array('code'=>'0', 'msg'=>'获取成功', 'data'=>$_task_list);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addTask($param)
    {
        //必传参数
        if(empty($param['department_id'])) return array('code'=>300, 'msg'=>'请选择部门');
        if(empty($param['task_id'])) return array('code'=>301, 'msg'=>'任务不能为空');
        //是否添加多任务
        $_add_task = array();
        $_task_ids = explode(',', $param['task_id']);
        foreach($_task_ids as $v){
            $_add_task[] = array(
                'task_id' => $v,
                'department_id' => $param['department_id']
            );
        }
        //更新数据
        D()->startTrans();
        D('TaskDepartment')->delData($param['department_id']);
        $_add_flag = D('TaskDepartment')->addAll($_add_task);
        if($_add_flag!==false){
            D()->commit();
            return array('code'=>0,'msg'=>'添加成功');
        }
        D()->rollback();
        return array('code'=>100,'msg'=>'添加失败');
    }


    /*
     * 转换条件
     */
    protected function regval($array){
        foreach($array as $k=>$v){
            if(strpos($v,'{time}')!==false){
                $array[$k] = time();
            }elseif(strpos($v,'{day')!==false){
                preg_match_all('/\{day(.*)?\}/',$v,$result);
                $array[$k] = strtotime(($result[1][0]).' day');
            }
        }
        return $array;
    }
}