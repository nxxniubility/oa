<?php
/*
|--------------------------------------------------------------------------
| 所有数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\DataService;

class DataController extends SystemApiController
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
    public function addDataLogs()
    {
        //外部调用？
        $param['operator_user_id'] = I('param.operator_user_id',null);
        $param['operattype'] = I('param.operattype',null);
        $param['user_id'] = I('param.user_id',null);
        $getService = function($param) {
            //去除数组空值
            $param = array_filter($param);
            //参数添加
            $param['logtime'] = time();
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->addDataLogs($param);
            //返回参数
            if($result['code']==0){
                return array('code'=>0,'msg'=>'获取成功','data'=>$result['data']);
            }
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        };
        $reData = $getService($param);
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
    }




    /*
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @ role_id (可多选 ‘,’隔开)
    | @author zgt
    */
    public function getDataMarket()
    {
        //外部调用？
        $param['logtime'] = I('param.logtime',null);
        $param['department_id'] = I('param.department_id',null);
        $param['role_id'] = I('param.role_id',null);
        $param['zone_id'] = I('param.zone_id',null);
//        $param['system_user_id'] = I('param.system_user_id',null);
        $getService = function($param){
            //去除数组空值
            $param = array_filter($param);
            //获取接口服务层
            $DataService = new DataService();
            $result = $DataService->getDataMarket($param);
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
    | 获取营销数据详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarketInfo()
    {
        //外部调用？
        $where['type'] = I('param.type',null);
        $where['logtime'] = I('param.logtime',null);
        $where['role_id'] = I('param.role_id',null);
        $where['zone_id'] = I('param.zone_id',null);
        $where['system_user_id'] = I('param.system_user_id',null);

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
        $this->ajaxReturn($reData['code'], $reData['msg'], $reData['data']);
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
            $this->ajaxReturn(0, '操作成功', $result['data']);
        }
        $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改营销数据
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editStandard()
    {
        $data['standard_id'] = I('param.standard_id',null);
        $data['standard_name'] = I('param.standard_name',null);
        $data['standard_remark'] = I('param.standard_remark',null);
        $data['department_id'] = I('param.department_id',null);
        $data['option_objs'] = I('param.option_objs',null);
        //去除数组空值
        $data = array_filter($data);
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->editStandard($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '操作成功', $result['data']);
        }
        $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
    }

    /*
   |--------------------------------------------------------------------------
   | 删除营销数据
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delStandard()
    {
        $data['standard_id'] = I('param.standard_id',null);
        //去除数组空值
        $data = array_filter($data);
        //获取接口服务层
        $DataService = new DataService();
        $result = $DataService->delStandard($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '操作成功', $result['data']);
        }
        $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
    }

    /*
   |--------------------------------------------------------------------------
   | 添加部门算法公式项
   |--------------------------------------------------------------------------
   | @author nxx
   */
   public function createDepartmentFormula()
   {
        $dataJSON = I('param.jsonData', null);
        $data = json_decode($dataJSON,true);

        $result = D('Data', 'Service')->addDepartmentFormula($data);
        $this->ajaxReturn($result['code'], $result['msg']);

   }

}