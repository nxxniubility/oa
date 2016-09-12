<?php
/*
|--------------------------------------------------------------------------
| 所有数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Api\Controller;
use Common\Controller\ApiBaseController;
use Common\Service\DataService;

class DataController extends ApiBaseController
{

    public function _initialize(){
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取数据记录详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataLogs($data=null)
    {
        //外部调用？
        if($data===null){
            $where['logtime'] = I('param.logtime',null);
            $where['operattype'] = I('param.operattype',null);
            $where['system_user_id'] = I('param.system_user_id',null);
            $where['updateuser_id'] = I('param.updateuser_id',null);
            $where['createuser_id'] = I('param.createuser_id',null);
            $where['user_id'] = I('param.user_id',null);
            $where['operator_user_id'] = I('param.operator_user_id',null);
        }else{
            $where = $data;
        }
        //去除数组空值
        $where = array_filter($where);
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->getDataLogs($where);
        //返回参数
        $this->ajaxReturn(0, '获取成功', $result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加统计数据记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataLogs($data=null)
    {
        //外部调用？
        if($data===null){
            $where['operattype'] = I('param.operattype',null);
            $where['operator_user_id'] = I('param.operator_user_id',null);
            $where['user_id'] = I('param.user_id',null);
            $where['createuser_id'] = I('param.createuser_id',null);
            $where['updateuser_id'] = I('param.updateuser_id',null);
            $where['system_user_id'] = I('param.system_user_id',null);
            $where['zone_id'] = I('param.zone_id',null);
            $where['channel_id'] = I('param.channel_id',null);
            $where['infoquality'] = I('param.infoquality',null);
        }else{
            $where = $data;
        }
        //去除数组空值
        $where = array_filter($where);
        //参数添加
        $where['logtime'] = time();
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->addDataLogs($where);
        //返回参数
        $this->ajaxReturn(0, '添加成功', $result);
    }




    /*
    |--------------------------------------------------------------------------
    | 获取营销数据详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarket($data=null)
    {
        //外部调用？
        if($data===null){
            $where['daytime'] = I('param.daytime',null);
            $where['system_user_id'] = I('param.system_user_id',null);
        }else{
            $where = $data;
        }
        //去除数组空值
        $where = array_filter($where);
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->getDataMarket($where);
        //返回参数
        $this->ajaxReturn(0, '获取成功', $result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataMarket($data=null)
    {
        //外部调用？
        if($data===null){
            $where['user_id'] = I('param.user_id',null);
            $where['system_user_id'] = I('param.system_user_id',null);
            $where['name'] = I('param.name',null);
        }else{
            $where = $data;
        }
        //去除数组空值
        $where = array_filter($where);
        //必要参数？
        if( empty($where['user_id']) || empty($where['system_user_id']) || empty($where['name']) ){
            $this->ajaxReturn(1, '缺少必要参数');
        }
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->addDataMarket($where);
        //返回参数
        $this->ajaxReturn(0, '添加成功', $result);
    }
}