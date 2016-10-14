<?php
/*
|--------------------------------------------------------------------------
| 区域表
|--------------------------------------------------------------------------
| createtime：2016-06-04
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\SystemModel;

class AreaModel
{
    protected $channel;//渠道数据

    public function __construct(){

    }

    public function getArea($pid='0',$deep='1'){
        if( F('Cache/channel/deep'.$deep) ) {
            $areaAll = F('Cache/channel/deep'.$deep);
        }else{
            $areaAll = M('area')->where("deep={$deep}")->select();
            F('Cache/channel/deep'.$deep, $areaAll);
        }
        if(!empty($areaAll) && $pid!=0){
            $area_new = array();
            foreach($areaAll as $k=>$v){
                if($v['reid']==$pid){
                    $area_new[] = $v;
                }
            }
        }else{
            $area_new = $areaAll;
        }

        return $area_new;
    }
}