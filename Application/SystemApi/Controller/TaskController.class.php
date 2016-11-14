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
    | 获取今日任务
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getTaskList()
    {
        //获取参数
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
}