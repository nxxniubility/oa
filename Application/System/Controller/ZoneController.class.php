<?php
/*
|--------------------------------------------------------------------------
| zone控制器
|--------------------------------------------------------------------------
| createtime：2016-04-12
| updatetime：
| updatename：
*/
namespace System\Controller;
use Common\Controller\SystemController;
class ZoneController extends SystemController {

    protected $zoneDb;
    public function _initialize(){
        parent::_initialize();
    }

    /*
	| 添加区域
    | @author Nixx
	*/
    public function addZone()
    {
        $this->display();
    }

    /*
    修改区域
    @author Nixx
    */
    public function editZone()
    {
        $zone['zone_id'] = I('get.zone_id');
        $center = I("get.center");
        $sign = I('get.sign');
        if(IS_POST) {
            $zone['name'] = I('post.name');
            $zone['email'] = I('post.email');
            $zone['tel'] = I('post.tel');
            $zone['abstract'] = I('post.abstract');
            $zone['address'] = I('post.address');
            $zone['parentid'] = I("post.pid");
            $result = D('Zone', 'Service')->editZone($zone, $zone['zone_id'], $center);
            if($result['code'] != 0){
                $this->ajaxReturn($result['code'],$result['msg']);
            }
            $this->ajaxReturn(0, $result['msg'], U('System/Zone/zoneList'));
        }
        if ($zone['zone_id'] == 1) {
            $zoneList = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone['zone_id']));
            $zoneList['data']['mark'] = 1;
        }else{
            $zoneList = D('Zone', 'Service')->getPidZone(array('zone_id'=>$zone['zone_id']));
        }
        $zone = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone['zone_id']));
        $this->assign("zone", $zone['data']);
        $this->assign("center", $center);
        $this->assign('zone_id', $zone['zone_id']);
        $this->assign('zoneList', $zoneList['data']);
        $this->display();

    }

    /*
    添加中心
    @author Nixx
    */
    public function newCenter()
    {
        $zone_id = I("get.zone_id");
        if(IS_POST) {
            if ($zone_id) {
                $zone['parentid'] = $zone_id;
            }else{
                $zone['parentid'] = I('post.zone_id');
            }
            $zone['name'] = I('post.name');
            $zone['email'] = I('post.email');
            $zone['tel'] = I('post.tel');
            $zone['abstract'] = I('post.abstract');
            $zone['address'] = I('post.address');
            //中心标记 - 10
            $zone['centersign'] = 10;
            $zone['level'] = 4;
            $result = D('Zone', 'Service')->createZone($zone);
            if($result['code'] != 0 ){
                $this->ajaxReturn($result['code'],$result['msg']);
            }
            $this->ajaxReturn(0, $result['msg'], U('System/Zone/zoneList'));
        }
        if ($zone_id) {
            $zone = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone_id));
        }else{
            $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>0));
        }

        $this->assign('zone', $zone['data']);
        $this->assign('zoneList', $zoneList['data']);
        $this->display();

    }

    /*
    获取区域详情
    @author Nixx
    */
    public function getZoneInfo()
    {
        $where['zone_id'] = I("post.zone_id");
        $zone = D('Zone', 'Service')->getZoneInfo($where);
        if (!$zone['data']) {
            $this->ajaxReturn(1,'没有可供管理的区域');
        }
        $this->assign('zone', $zone['data']);

    }

    /*
    获取区域信息
    @author Nixx
    */
    public function zoneInfoList()
    {
        $zone_id = I("post.zone_id");
        $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>$zone_id));
        if (!empty($zoneList['children'])) {
            $this->ajaxReturn(301,'当前区域没有城市,请先添加城市');
        }
        $this->ajaxReturn(0,'',$zoneList['data']);
    }

    /*
    获取区域信息列表
    @author Nixx
    */
    public function zoneList()
    {
        // $zone_id = 1;//超级管理员
    	// $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>$zone_id));
     //    if (!$zoneList['data']) {
     //        $this->ajaxReturn(1,'没有可供管理的区域');
     //    }
     //    $this->assign('zoneList', $zoneList['data']);
     //    $this->assign('urlDelZone', U("System/Zone/delZone"));
        $this->display();
    }

    /*
    删除区域信息
    @author Nixx
    */
    public function delZone()
    {
        $zone_id = I("post.zone_id");
        $zone = D('Zone', 'Service')->getZoneInfo(array('zone_id'=>$zone_id));
        $level = $zone['data']['level'];
        $backInfo = D('Zone', 'Service')->deleteZone($zone_id,$level);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn(1,'删除失败');
        }
        $this->ajaxReturn(0, '删除成功', U('System/Zone/zoneList'));
    }


    // public function promoteToPromote()
    // {
    //     set_time_limit(0);
    //     $max_depth = 1;
    //     $array = M('channel_copy')->where()->field('channel_id, pid')->select();
    //       //数组分级
    //         $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
    //         $nodeAll = $Arrayhelps->createTree($array, 0, 'channel_id', 'pid');
    //     $a = D('Zone')->array_depth($nodeAll, $max_depth);

    // print_r($nodeAll);
    // dump($a);

    // }

}
