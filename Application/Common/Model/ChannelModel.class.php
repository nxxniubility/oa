<?php
/*
|--------------------------------------------------------------------------
| 渠道数据表
|--------------------------------------------------------------------------
| createtime：2016-04-12
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\SystemModel;

class ChannelModel extends SystemModel{

    /**
     * 获取所有渠道
     * @return array
     */
    public function getAllChannel($order='sort desc',$page=null){
        if( F('Cache/channel/channel') ) {
            $channel = F('Cache/channel/channel');
        }else{
            $channel['data'] = $this->where('system_user_id=0')->select();
            $channel['count'] = $this->where('system_user_id=0')->count();
            F('Cache/channel/channel', $channel);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $channel['data'] = $Arrayhelps->createTree($channel['data'], 0, 'channel_id', 'pid');
        $_channelAll = $this->disposeArray($channel, $order, $page);
        return $_channelAll;
    }

    /**
     * 获取渠道
     * @return array
     */
    public function getChannel($channel_id){

        $channelInfo = M("channel")->where("channel_id = $channel_id")->find();
        if (!$channelInfo) {
            return false;
        }
        return $channelInfo;
    }
    /*
	Channel 获取想关联的ID
	@author Nixx
	*/
    public function getChannelIds($channel_id = 0)
    {
        if( F('Cache/channel/channel') ) {
            $channelist = F('Cache/channel/channel');
        }else{
            $channelist['data'] = $this->where(array('system_user_id'=>0))->select();
            $channelist['count'] = $this->where('system_user_id=0')->count();
            F('Cache/channel/channel', $channelist);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $_channelist['data'] = $Arrayhelps->subFinds($channelist['data'],$channel_id,'channel_id','pid');
        foreach($channelist['data'] as $k=>$v){
            if($v['channel_id']==$channel_id){
                $_channelist['data'][] = $v;
            }
        }
        return $_channelist['data'];
    }
    /*
    |--------------------------------------------------------------------------
    | 获取渠道名称  包括上级
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getChannelNames($channel_id){
        if( F('Cache/channel/channel') ) {
            $channelist = F('Cache/channel/channel');
        }else{
            $channelist['data'] = $this->where(array('system_user_id'=>0))->select();
            $channelist['count'] = $this->where('system_user_id=0')->count();
        }
        foreach($channelist['data'] as $k=>$v){
            if($v['channel_id']==$channel_id){
                $_channeDetail = $v;
            }
        }

        if($_channeDetail['pid']==0){
            $renames = $_channeDetail['channelname'];
        }else{
            foreach($channelist['data'] as $k=>$v){
                if($v['channel_id']==$_channeDetail['pid']){
                    $_channeDetail2 = $v;
                }
            }
            $renames = $_channeDetail2['channelname'].'-'.$_channeDetail['channelname'];
        }
        return $renames;
    }
}