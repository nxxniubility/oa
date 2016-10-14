<?php
/*
* 区域服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\DataService;
use Common\Service\BaseService;

class ZoneService extends BaseService
{
    //初始化
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 查找用户可管理的区域数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getZoneList($zone_id = 0)
    {
        if (F('Cache/Zone/zone')) {
            $zoneList = F('Cache/Zone/zone');
        }else{
            $zoneList = D('Zone')->where("status=1")->select();
            F('Cache/Zone/zone', $zoneList);
        }
        foreach($zoneList as $k=>$v){
            if($v['zone_id']==$zone_id){
                $newZoneList = $v;
            }
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $children_ZoneList = $Arrayhelps->createTree($zoneList,$zone_id,'zone_id','parentid');
        $newZoneList['children'] = $children_ZoneList;
        return array('code'=>0,'data'=>$newZoneList);
    }

    /*
    获取区域详情
    @author Nixx
    */
    public function getZoneInfo($zone_id)
    {
        if (F('Cache/Zone/zone')) {
            $zoneList = F('Cache/Zone/zone');
        }else{
            $zoneList = D('Zone')->where("status=1")->select();
            F('Cache/Zone/zone', $zoneList);
        }
        if(!empty($zoneList)){
            foreach($zoneList as $k=>$v){
                if($v['zone_id']==$zone_id){
                    $newZoneList = $v;
                }
            }
        }
        return array('code'=>0,'data'=>$newZoneList);
    }

    public function getZoneCenter($centersign)
    {
        $centerList = D("Zone")->where("centersign = $centersign")->select();
        return array('code'=>0,'data'=>$centerList);
    }
}