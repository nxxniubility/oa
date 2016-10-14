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
        $this->zoneDb = D("Common/zone");
    }

    /*
	添加区域
    @author Nixx
	*/
    public function addZone()
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
            $zone['addusr'] = $this->system_user_id;
            if(empty($zone['name'])) $this->ajaxReturn(1,'请选择区域名称', '', 'name');
            if(empty($zone['parentid'])) $this->ajaxReturn(2,'请选择所属区域', '', '');
            $parentZone = $this->zoneDb->where("zone_id = $zone[parentid] and status=1")->find();
            $zone['level']  = $parentZone['level']+1;
            $zList = D("zone")->where("level = $zone[level] and status=1")->select();
            foreach ($zList as $key => $zoneInfo) {
                if ($zoneInfo['name'] == $zone['name']) {
                    $this->ajaxReturn(3,'该区域已被创建，请重新选择父级区域或创建别的区域');
                }
            }
            $result = $this->zoneDb->createZone($zone);
            if($result=== false){
                $this->ajaxReturn(4,'创建失败');
            }
            $this->success('创建成功', 0, U('System/Zone/zoneList')); 
        }
        if ($zone_id) {
            $zone = $this->zoneDb->getZone($zone_id);        
        }else{
            $zoneList = $this->zoneDb->getZoneList();
        }
        $this->assign('zone', $zone);             
        $this->assign('zoneList', $zoneList);
        $this->display();   	
    }

    /*
    修改区域
    @author Nixx
    */
    public function editZone()
    {
        $zone['zone_id'] = I('get.zone_id');
        $zone_id = $zone['zone_id'];
        if(IS_POST) {           
            $zone['name'] = I('post.name');
            $zone['email'] = I('post.email');
            $zone['tel'] = I('post.tel');
            $zone['abstract'] = I('post.abstract');
            $zone['address'] = I('post.address');
            $zone['parentid'] = I("post.zone_id");
            $zone['addusr'] = $this->system_user_id;
            if(empty($zone['name'])) $this->ajaxReturn(1,'请选择区域名称', '', 'name');
            if(empty($zone['parentid'])) $this->ajaxReturn(2,'请选择所属区域', '', '');
            
            $zone_id = $this->zoneDb->editZone($zone);
            if(!$zone_id){
                $this->ajaxReturn(1,'修改失败');
            }
            $this->success('修改成功', 0, U('System/Zone/zoneList')); 
        }
        if ($zone['zone_id'] == 1) {
            $zoneList = $this->zoneDb->getZone($zone['zone_id']);
            $zoneList['mark'] = 1;
        }else{
            $zoneList = $this->zoneDb->getPidZone($zone['zone_id']);         
        }
        $zone = D("Zone")->getZone($zone_id);
        $this->assign("zone", $zone);
        $this->assign('zone_id', $zone_id);
        $this->assign('zoneList', $zoneList);
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
            $zone['addusr'] = $this->system_user_id;
            //中心标记 - 10 ，
            $zone['centersign'] = 10;
            $zone['level'] = 4;
            if(!$zone['parentid']){
                $this->ajaxReturn(1,'请选择父级区域', '');
            }
            if(empty($zone['name'])){
                $this->ajaxReturn(1,'请输入中心名称', '', 'name');
            } 
            if(empty($zone['parentid'])){
                $this->ajaxReturn(2,'请选择所属区域', '', '');
            }  
            if(empty($zone['address'])){
                $this->ajaxReturn(4,'请输入中心地址', '', 'address');
            }
            if(empty($zone['tel'])){
                $this->ajaxReturn(5,'请输入中心电话', '', 'tel');
            }
            if(empty($zone['email'])){
                $this->ajaxReturn(6,'请输入中心联系邮箱', '', 'email');
            }       
            $zone_id = $this->zoneDb->createZone($zone);
            if(!$zone_id){
                $this->ajaxReturn(7,'创建失败');
            }
            $this->success('创建成功', 0, U('System/Zone/zoneList'));   
        }
        if ($zone_id) {
            $zone = $this->zoneDb->getZone($zone_id);        
        }else{
            $zoneList = $this->zoneDb->getZoneList();
        }
       
        $this->assign('zone', $zone);      
        $this->assign('zoneList', $zoneList);
        $this->display();
        
                        
    }

    /*
    修改中心
    @author Nixx
    */
    public function editCenter()
    {

        $zone['zone_id'] = I('get.zone_id');
        $zone_id = $zone['zone_id'];
        if(IS_POST){
            $zone['name'] = I('post.name');
            $zone['email'] = I('post.email');
            $zone['tel'] = I('post.tel');
            $zone['abstract'] = I('post.abstract');
            $zone['address'] = I('post.address');
            $zone['addusr'] = $this->system_user_id;
            if(empty($zone['name'])){
                $this->ajaxReturn(1,'请输入中心名称', '', 'name');
            } 
            if(empty($zone['parentid'])){
                $this->ajaxReturn(2,'请选择所属区域', '', '');
            }
            $zone_id = $this->zoneDb->editZone($zone);
            if(!$zone_id){
                $this->ajaxReturn(3,'修改失败');
            }
            $this->success('修改成功', 0, U('System/Zone/zoneList'));   
        }

        $zoneList = $this->zoneDb->getZoneList($zone['zone_id']); 
        $this->assign('zone_id', $zone_id);            
        $this->assign('zoneList', $zoneList);

        $this->display();
                        
    }


    /*
    获取区域详情
    @author Nixx
    */
    public function getZoneInfo()
    {
        $zone_id = I("post.zone_id",0);
        $zone = $this->zoneDb->getZone($zone_id);           
        if ($zone === false) {
            $this->ajaxReturn(1,'没有可供管理的区域');
        }
        $this->assign('zone', $zone);

    }

    /*
    获取区域信息
    @author Nixx
    */
    public function zoneInfoList()
    {
        $zone_id = I("post.zone_id");
       
        $zoneList = $this->zoneDb->getZoneList($zone_id);               
        
        if ($zoneList === false) {
            $this->ajaxReturn(1,'没有可供管理的区域');
        }
        $this->ajaxReturn(0,'',$zoneList);
    }

    /*
    获取区域信息列表
    @author Nixx
    */
    public function zoneList()
    {
    	$zone_id = 1;//超级管理员  
    	$zoneList = $this->zoneDb->getZoneList($zone_id); 

        if ($zoneList === false) {
            $this->ajaxReturn(1,'没有可供管理的区域');
        }
        $this->assign('zoneList', $zoneList);
        $this->assign('urlDelZone', U("System/Zone/delZone"));

        $this->display();
    }

    /*
    删除区域信息 - 删除的同时是否删除此区域下的所有员工信息？？？？？待定！！
    @author Nixx
    */
    public function delZone()
    {
        $zone_id = I("post.zone_id");
        $zone = $this->zoneDb->getZone($zone_id);
        $level = $zone['level'];
        $backInfo = $this->zoneDb->deleteZoneList($zone_id,$level);
        if ($backInfo !== false) {
            $this->success('删除成功', 0, U('System/Zone/zoneList'));
        }
        $this->ajaxReturn(1,'删除失败');
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