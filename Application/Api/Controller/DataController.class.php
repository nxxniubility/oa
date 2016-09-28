<?php
/*
|--------------------------------------------------------------------------
| 所有数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Api\Controller;
use Common\Controller\ApiBaseController;
use Common\Service\ApiService;
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
    public function getDataLogs($requesr=null)
    {
        //外部调用？
        if($requesr===null){
            $where['logtime'] = I('param.logtime',null);
            $where['operattype'] = I('param.operattype',null);
            $where['system_user_id'] = I('param.system_user_id',null);
            $where['updateuser_id'] = I('param.updateuser_id',null);
            $where['createuser_id'] = I('param.createuser_id',null);
            $where['user_id'] = I('param.user_id',null);
            $where['operator_user_id'] = I('param.operator_user_id',null);
        }else{
            $where = $requesr;
        }
        $getService = function($where) {
            //去除数组空值
            $where = array_filter($where);
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->getDataLogs($where);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($where);
        if(!empty($request)){
            return $reData;
        }else{
            $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 添加统计数据记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataLogs($requesr=null)
    {
        //外部调用？
        if($requesr===null){
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
            $where = $requesr;
        }
        $getService = function($where) {
            //去除数组空值
            $where = array_filter($where);
            //参数添加
            $where['logtime'] = time();
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->addDataLogs($where);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($where);
        if(!empty($request)){
            return $reData;
        }else{
            $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
        }
    }




    /*
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @ role_id (可多选 ‘,’隔开)
    | @author zgt
    */
    public function getDataMarket($requesr=null)
    {
        //外部调用？
        if($requesr===null){
            $where['daytime'] = I('param.daytime',null);
            $where['role_id'] = I('param.role_id',null);
            $where['zone_id'] = I('param.zone_id',null);
            $where['system_user_id'] = I('param.system_user_id',null);
        }else{
            $where = $requesr;
        }
        $getService = function($where){
            //去除数组空值
            $where = array_filter($where);
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->getDataMarket($where);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($where);
        if(!empty($requesr)){
            return $reData;
        }else{
            $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 获取营销数据详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarketInfo($requesr=null)
    {
        //外部调用？
        if($requesr===null){
            $where['type'] = I('param.type',null);
            $where['daytime'] = I('param.daytime',null);
            $where['role_id'] = I('param.role_id',null);
            $where['zone_id'] = I('param.zone_id',null);
            $where['system_user_id'] = I('param.system_user_id',null);
        }else{
            $where = $requesr;
        }

        $getService = function($where){
            //去除数组空值
            $where = array_filter($where);
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->getDataMarketInfo($where);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($where);
        if(!empty($requesr)){
            return $reData;
        }else{
            $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataMarket($requesr=null)
    {
        //外部调用？
        if($requesr===null){
            $where['user_id'] = I('param.user_id',null);
            $where['system_user_id'] = I('param.system_user_id',null);
            $where['name'] = I('param.name',null);
        }else{
            $where = $requesr;
        }
        $getService = function($where) {
            //去除数组空值
            $where = array_filter($where);
            //必要参数？
            if (empty($where['user_id']) || empty($where['system_user_id']) || empty($where['name'])) {
                return array('code'=>1,'msg'=>'参数异常');
            }
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->addDataMarket($where);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($where);
        if(!empty($request)){
            return $reData;
        }else{
            $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
        }
    }

    /*
   |--------------------------------------------------------------------------
   | 添加营销数据
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addStandard()
    {
        $data['standard_name'] = I('param.standard_name',null);
        $data['standard_remark'] = I('param.standard_remark',null);
        $data['department_id'] = I('param.department_id',null);
        $data['option_objs'] = I('param.option_objs',null);
        //去除数组空值
        $data = array_filter($data);
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->addStandard($data);
        //返回参数
        if($result['code']==0){
            return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
        }
        return array('code'=>$result['code'],'msg'=>$result['msg']);
    }
}