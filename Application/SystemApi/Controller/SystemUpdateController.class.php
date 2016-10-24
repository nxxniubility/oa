<?php
/*
|--------------------------------------------------------------------------
| 系统更新相关的数据接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class SystemUpdateController extends SystemApiController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 系统更新列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateList()
    {
        //获取请求？
        $param['page'] = I('param.page',null);
        $param['order'] = I('param.order',null);
        //获取接口服务层
        $result = D('SystemUpdate','Service')->getSystemUpdateList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 添加系统更新
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addSystemUpdate()
    {
        //获取请求？
        $param['uptitle'] = I('param.uptitle',null);
        $param['upbody'] = I('param.upbody',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUpdate','Service')->addSystemUpdate($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 修改系统更新
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editSystemUpdate()
    {
        //获取请求？
        $param['system_update_id'] = I('param.system_update_id',null);
        $param['uptitle'] = I('param.uptitle',null);
        $param['upbody'] = I('param.upbody',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUpdate','Service')->editSystemUpdate($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 删除系统更新
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delSystemUpdate()
    {
        //获取请求？
        $param['system_update_id'] = I('param.system_update_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUpdate','Service')->delSystemUpdate($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'操作成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取系统更新详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateInfo()
    {
        //获取请求？
        $param['system_update_id'] = I('param.system_update_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('SystemUpdate','Service')->getSystemUpdateInfo($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }
}