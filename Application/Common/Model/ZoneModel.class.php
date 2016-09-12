<?php
/*
|--------------------------------------------------------------------------
| 区域模型
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\SystemModel;
class ZoneModel extends SystemModel{

 	protected $zoneDb;

    public function _initialize(){
        $max_depth = 1;
    }

    /*
    创建区域数据
    @author Nixx
    */
	public function createZone($zone)
	{
		$zone['status'] = 1;
		$zone['createtime'] = time();
		$zone_id = $this->data($zone)->add();
		if (!$zone_id) {
			return false;
		}
		$zoneAllList = $this->where("status=1")->select();
		F('Cache/Zone/zone', $zoneAllListgetZoneIds);
		return $zone_id;

	}

	/*
    获取区域详情
    @author Nixx
    */
	public function getZone($zone_id)
	{
		$zone = M("zone")->where("zone_id = $zone_id and status=1")->find();
		if (!$zone) {
			return false;
		}
		return $zone;
	}

	/*
    获取父级区域列表
    @author Nixx
    */
	public function getPidZone($zone_id)
	{
		$zone = M("zone")->where("zone_id = $zone_id and status=1")->find();
		if (!$zone) {
			return false;
		}
		$level = $zone['level']-1;
		$zones = M("zone")->where("level = $level and status=1")->select();
		return $zones;
	}

	/*
    修改区域
    @author Nixx
    */
	public function editZone($zone)
	{
		$zone['status'] = 1;
		$zone['createtime'] = time();
		$pid = $zone['parentid'];
		$name = $zone['name'];
		$zoneInfo = $this->where("name = '{$name}' and status=1")->find();
		if ($zoneInfo) {
			$zid = $zoneInfo['zone_id'];
			$backInfo = $this->where("zone_id = $zid and status=1")->save($zone);
			if ($backInfo === false) {
				return false;
			}
			$zoneAllList = $this->where("status=1")->select();
			F('Cache/Zone/zone', $zoneAllList);
			return $zoneInfo['zone_id'];
		}
		return false;
	}

	/*获取总部和大区
	@author Nixx
	*/
	public function getAreaList(){
		$zone_id = 1;//总部
		if (F('Cache/Zone/zone')) {
			$zoneList = F('Cache/Zone/zone');
			foreach ($zoneList as $zone) {
				if ($zone['parentid'] == $zone_id) {
					$areaList[] = $zone;
				}
			}
		}else{
			$zoneList = $this->where("zone_id = $zone_id and status=1")->select();
			foreach ($zoneList as $key => $zone) {
				$areaList1 = $this->where("parentid = $zone[zone_id] and status=1")->select();
				$areaList[$key] = $zone;
				foreach ($areaList1 as $z1) {
					$areaList[$key][] = $z1;
				}
			}
		}
		return $areaList;
	}



	/*
	查找用户可管理的区域数据
    @author Nixx

	*/			
	public function getZoneList($zone_id = 0)
	{
		if (F('Cache/Zone/zone')) {
			$zoneList = F('Cache/Zone/zone');
		}else{
			$zoneList = $this->where("status=1")->select();
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
		return $newZoneList;
	}

	/*
	若删除的id下面有子id，则同时删除所有的子id信息
	@author Nixx
	*/
	
	public function deleteZoneList($zone_id,$level)
	{
		$temp['status'] = 0;
		switch($level) {
			case 1: 
			$backInfo = $this->where("status=1")->save($temp);
			break;

			case 2: 
			$zoneArr = $this->where("parentid = $zone_id and status=1")->field("zone_id")->select();
			if (!empty($zoneArr)) {
				foreach ($zoneArr as $z) {
					$backInfo = $this->where("parentid = $z[zone_id] or zone_id = $z[zone_id] or zone_id = $zone_id and status=1")->save($temp);
				}
			}else{
				$backInfo = $this->where("zone_id = $zone_id and status=1")->save($temp);
			}
			break;

			case 3: //完成
			$zoneArr = $this->where("parentid = $zone_id and status=1")->field("zone_id")->select();
			if ($zoneArr) {
				foreach ($zoneArr as $z) {
					$backInfo = $this->where("parentid = $z[zone_id] or zone_id = $z[zone_id] or zone_id = $zone_id and status=1")->save($temp);
				}
			}else{
				$backInfo = $this->where("zone_id = $zone_id and status=1")->save($temp);
			}
			break;

			case 4: //完成
			$backInfo = $this->where("zone_id = $zone_id and status=1")->save($temp);
			break;

			default:
			break;
		}
		if ($backInfo !== false) {
			updateConfig('zone',null);
		}
        $zoneAllList = $this->where("status=1")->select();
		F('Cache/Zone/zone', $zoneAllList); 
		return $backInfo;
	}
	


	/*
	role_id 获取想关联的ID
	@author luoyu
	*/
	public function getZoneIds($zone_id = 0)
	{
		if (F('Cache/Zone/zone')) {
			$zoneList = F('Cache/Zone/zone');
		}else{
			$zoneList = $this->where("status=1")->select();
			F('Cache/Zone/zone', $zoneList);
		}
		//数组分级
		$Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
		$newZoneList = $Arrayhelps->subFinds($zoneList,$zone_id,'zone_id','parentid');
		foreach($zoneList as $k=>$v){
			if($v['zone_id']==$zone_id){
				$newZoneList[] = $v;
			}
		}
		return $newZoneList;
	}




	// public function array_depth($array, $max_depth) 
	// {
	// 	foreach ($array as $value)
	// 	{
	// 		if (is_array($value))
	// 		{
	// 			$max_depth=$max_depth+1;
	// 			$depth = $this->array_depth($value, $max_depth) ;
	// 		}
	// 	}        
	// 	return $max_depth;
	// }

}