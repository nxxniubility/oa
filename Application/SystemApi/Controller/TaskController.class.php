<?php
/*
|--------------------------------------------------------------------------
| 今日任务相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\DataService;
use Common\Service\TaskService;

class TaskController extends SystemApiController
{
    public function _initialize(){
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
        $getService = function(){
            //获取接口服务层
            $TaskService = new TaskService();
            $result = $TaskService->getTask();
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>$result['msg'],'data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService();
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取任务设置列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getTaskList()
    {
        $getService = function(){
            //获取接口服务层
            $TaskService = new TaskService();
            $result = $TaskService->getTaskList();
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>$result['msg'],'data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService();
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
    }


    /*
    |--------------------------------------------------------------------------
    | 获取自己今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMyTask()
    {
        //获取参数
        $param['role_id'] = I('param.role_id',null);
        //获取服务层
        $getService = function($param){
            //获取接口服务层
            $TaskService = new TaskService();
            $result = $TaskService->getMyTask($param);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>$result['msg'],'data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($param);
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addTask()
    {
        //获取参数
        $param['task_id'] = I('param.task_id',null);
        $param['department_id'] = I('param.department_id',null);
        //获取服务层
        $getService = function($param){
            //获取接口服务层
            $TaskService = new TaskService();
            $result = $TaskService->addTask($param);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>$result['msg'],'data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($param);
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
    }
}