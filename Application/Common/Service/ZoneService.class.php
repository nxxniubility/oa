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
    public function getZoneList($param)
    {
        if (!$param['zone_id']) {
            $param['zone_id'] = $this->system_user['zone_id'];
        }
        if (F('Cache/zone')) {
            $zoneList = F('Cache/zone');
        }else{
            $zoneList = D('Zone')->getList(array('status'=>1));
            F('Cache/zone', $zoneList);
        }
        foreach($zoneList as $k=>$v){
            if($v['zone_id']==$param['zone_id']){
                $newZoneList = $v;
            }
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $children_ZoneList = $Arrayhelps->createTree($zoneList,1,'zone_id','parentid');
        $newZoneList['zoneinfo'] = $zoneInfo;
        $newZoneList['children'] = $children_ZoneList;
        return array('code'=>0,'data'=>$newZoneList);
    }

    /*
    |--------------------------------------------------------------------------
    | role_id 获取想关联的ID
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getZoneIds($param)
    {
        if (F('Cache/zone')) {
            $zoneList = F('Cache/zone');
        }else{
            $zoneList = D('Zone')->getList(array('status'=>1));
            F('Cache/zone', $zoneList);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $newZoneList = $Arrayhelps->subFinds($zoneList,$param['zone_id'],'zone_id','parentid');
        foreach($zoneList as $k=>$v){
            if($v['zone_id']==$param['zone_id']){
                $newZoneList[] = $v;
            }
        }
        return array('code'=>0,'data'=>$newZoneList);
    }

    /*
    获取区域详情
    @author nxx
    */
    public function getZoneInfo($param)
    {
        if (F('Cache/zone')) {
            $zoneList = F('Cache/zone');
        }else{
            $zoneList = D('Zone')->getList(array('status'=>1));
            F('Cache/zone', $zoneList);
        }
        foreach($zoneList as $k=>$v){
            if($v['zone_id']==$param['zone_id']){
                $newZoneList = $v;
                break;
            }
        }
        if ($param['zone_id'] != 1 && $param['zone_id']) {
            $pid = $newZoneList['parentid'];
            $parent = D('Zone')->where("zone_id = $pid")->field('zone_id,name')->find();
            $newZoneList['parentname'] = $parent['name'];
        }elseif($param['zone_id'] == 1){
            $parent = D('Zone')->where("zone_id = 1")->field('zone_id,name')->find();
            $newZoneList['parentname'] = $parent['name'];
        }
        return array('code'=>0,'data'=>$newZoneList);
    }

    /*
    获取中心列表
    @author nxx
    */
    public function getZoneCenter($param)
    {
        $centerList = D('Zone')->getList($param);
        return array('code'=>0,'data'=>$centerList);
    }

    /*
    创建区域
    @author nxx
    */
    public function createZone($param)
    {
        $param['addusr'] = $this->system_user_id;
        $param['status'] = 1;
        $param['createtime'] = time();
        if(!(str_replace(' ','',$param['name']))) {
            return array('code'=>301,'msg'=>'区域名称不能为空');
        }
        if(empty($param['parentid'])) {
            return array('code'=>302,'msg'=>'请选择所属区域');
        }
        $result1 = $this->checkMobile($param['tel']);
        $result2 = $this->checkTel($param['tel']);
        if ($param['tel'] && $result1==false && $result2==false) {
            return array('code'=>303,'msg'=>'请输入正确的联系方式');
        }
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ($param['email'] && !preg_match($pattern,$param['email']))
        {
            return array('code'=>304,'msg'=>'请输入正确的邮箱');
        }
        $parentZone = D("Zone")->getFind(array('zone_id'=>$param['parentid'],'status'=>1));
        $param['level']  = $parentZone['level']+1;
        if ($param['level'] == 4) {
            $param['centersign'] = 10;
        }
        $sameZone = D("Zone")->getFind(array('name'=>$param['name'],'status'=>1));
        if ($sameZone) {
            $zoneAllList = D('Zone')->getList(array('status'=>1));
            F('Cache/zone', $zoneAllList);
            return array('code'=>201,'msg'=>'该区域已被创建');
        }
        $result = D("Zone")->addData($param);
        if ($result['code'] != 0) {
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        }
        $zoneAllList = D('Zone')->getList(array('status'=>1));
        F('Cache/zone', $zoneAllList);
        
        return array('code'=>0,'msg'=>'创建成功','data'=>$result['data']);
    }

    /*
    修改区域
    @author nxx
    */
    public function editZone($param, $zone_id)
    {
        $param = array_filter($param);
        $param['addusr'] = $this->system_user_id;
        $zoneInfo = D("Zone")->getFind(array('zone_id'=>$zone_id));
        if ($zoneInfo['addusr'] != $param['addusr']) {
            return array('code'=>401,'msg'=>'无权限修改');
        }
        if(empty($param['name'])) {
            return array('code'=>301,'msg'=>'区域名称不能为空');
        }
        if($zone_id != 1 && empty($param['parentid'])) {
            return array('code'=>302,'msg'=>'请选择所属区域');
        }
        $result1 = $this->checkMobile($param['tel']);
        $result2 = $this->checkTel($param['tel']);
        if ($param['tel'] && $result1==false && $result2==false) {
            return array('code'=>303,'msg'=>'请输入正确的联系方式');
        }
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ($param['email'] && !preg_match($pattern,$param['email']))
        {
            return array('code'=>304,'msg'=>'请输入正确的邮箱');
        }
        $result = D("Zone")->editData($param, $zone_id);
        if ($result['code'] != 0) {
            return array('code'=>$result['code'],'msg'=>$result['msg']);
        }
        $zoneAllList = D('Zone')->getList(array('status'=>1));
        F('Cache/zone', $zoneAllList);
        
        return array('code'=>0,'msg'=>'修改成功','data'=>$result['data']);

    }

    /*
    获取父级区域列表
    @author Nixx
    */
    public function getPidZone($param)
    {
        $zone = D("Zone")->getFind($param);
        if (!$zone) {
            return array('code'=>301,'msg'=>'参数有误');
        }
        $level = $zone['level']-1;
        $zones = D("Zone")->getList(array('level'=>$level,'status'=>1));
        return array('code'=>0,'data'=>$zones);
    }

    /*
    获取父级区域列表
    @author Nixx
    */
    public function deleteZone($zone_id,$level)
    {
        $temp['status'] = 0;
        if ($level == 1 || $level == 4) {
            $backInfo = D('Zone')->editData($temp, $zone_id);
        }else{
            $zoneArr = D("Zone")->getList(array('parentid'=>$zone_id,'status'=>1));
            if ($zoneArr) {
                foreach ($zoneArr as $key => $value) {
                    $backInfo = D('Zone')->where("parentid = $value[zone_id] or zone_id = $value[zone_id] or zone_id = $zone_id and status=1")->save($temp);
                }
            }
            $backInfo = D('Zone')->editData($temp, $zone_id);
            
        }
        $zoneAllList = D('Zone')->where("status=1")->select();
        F('Cache/zone', $zoneAllList);
        return $backInfo;
    }

}
