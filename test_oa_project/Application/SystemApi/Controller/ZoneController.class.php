<?php
/*
|--------------------------------------------------------------------------
| 区域数据相关的接口
|--------------------------------------------------------------------------
| @author nxx
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemZoneService;

class ZoneController extends SystemApiController
{
    /*
    |--------------------------------------------------------------------------
    | 区域列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getZoneList()
    {
        $param['zone_id'] = I("param.zone_id", null);
        //获取接口服务层
        $result = D('Zone','Service')->getZoneList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加区域
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addZone()
    {
        //获取请求？
        $param['name'] = I('param.name',null);
        $param['email'] = I('param.email',null);
        $param['tel'] = I('param.tel',null);
        $param['abstract'] = I('param.abstract',null);
        $param['address'] = I('param.address',null);
        $param['parentid'] = I('param.zone_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Zone','Service')->createZone($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }


    /*
    |--------------------------------------------------------------------------
    | 修改区域
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editZone()
    {
        //获取请求？
        $param['zone_id'] = I('param.zone_id',null);
        $param['name'] = I('param.name',null);
        $param['email'] = I('param.email',null);
        $param['tel'] = I('param.tel',null);
        $param['abstract'] = I('param.abstract',null);
        $param['address'] = I('param.address',null);
        $param['parentid'] = I("param.pid",null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Zone', 'Service')->editZone($param, $param['zone_id']);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    获取区域详情
    @author Nixx
    */
    public function getZoneInfo()
    {
        $zone_id = I('param.zone_id',null);
        $zone = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone_id));
        if ($zone['code'] != 0) {
            $this->ajaxReturn($zone['code'],'没有可供管理的区域');
        }
        $this->ajaxReturn($zone['code'], '', $zone['data']);

    }

    
    /*
    获取区域父级列表
    @author Nixx
    */
    public function getParentZoneList()
    {
        $zone_id = I('param.zone_id',null);
        $zoneList = D('Zone', 'Service')->getPidZone(array('zone_id'=>$zone_id));
        $this->ajaxReturn(0,'',$zoneList['data']);
    }


    /*
    获取区域信息
    @author Nixx
    */
    public function zoneInfoList()
    {
        $zone_id = I('param.zone_id',null);
        $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>$zone_id));
        if (!empty($zoneList['children'])) {
            $this->ajaxReturn(301,'当前区域没有城市,请先添加城市');
        }
        $this->ajaxReturn(0,'',$zoneList['data']);
    }

    /*
    删除区域信息
    @author nxx
    */
    public function delZone()
    {
        $zone_id = I('param.zone_id',null);
        $zone = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone_id));
        $level = $zone['data']['level'];
        $backInfo = D('Zone', 'Service')->deleteZone($zone_id,$level);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'],'删除失败');
        }
        $this->ajaxReturn(0, '删除成功', U('System/Zone/zoneList'));
    }
}