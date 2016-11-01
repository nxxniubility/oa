<?php
/*
|--------------------------------------------------------------------------
| 渠道数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;

class ConfigController extends SystemApiController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取OSS配置
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getAliOss()
    {
        //获取请求？
        $param['bid'] = I('param.bid',null);
        $param['name'] = I('param.name',null);
        $param['size'] = I('param.size',null);
        //获取接口服务层
        $result = D('AliOss','Service')->osspolicy($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
   |--------------------------------------------------------------------------
   | 获取静态配置状态
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getStatusList()
    {
        //获取请求？
        $param['name'] = I('param.name',null);
        $field_list = C('FIELD_STATUS');
        if(!empty($param['name'])){
            $_re_arr = $field_list[strtoupper($param['name'])];
        }else{
            $_re_arr = $field_list;
        }

        //返回参数
        $this->ajaxReturn(0,'OK',$_re_arr);
    }
}