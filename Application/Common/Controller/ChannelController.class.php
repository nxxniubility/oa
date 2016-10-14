<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class ChannelController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | Channel 获取所有渠道
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($order='sort desc',$page=null,$typle=null){
        if( F('Cache/channel/channel') ) {
            $channel = F('Cache/channel/channel');
        }else{
            $channel['data'] = D('Channel')->where('status=1')->select();
            $channel['count'] = D('Channel')->where('status=1')->count();
            F('Cache/channel/channel', $channel);
        }
        //是否需要分级
        if(empty($typle)){
            //数组分级
            $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $channel['data'] = $Arrayhelps->createTree($channel['data'], 0, 'channel_id', 'pid');
            $_channelAll = $this->disposeArray($channel, $order, $page);
        }else{
            $_channelAll = $channel;
        }

        return array('code'=>'0', 'data'=>$_channelAll);
    }

    /*
    |--------------------------------------------------------------------------
    | Channel 渠道添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function create_channel($data)
    {
        //必须参数
        if(empty($data['operator_id']) || empty($data['channelname'])) return array('code'=>2,'msg'=>'参数异常');
        $data['system_user_id'] = $data['operator_id'];
        $reflag = D('Channel')->add($data);
        if($reflag!==false) {
            F('Cache/channel/channel', null);
            return array('code'=>0,'msg'=>'渠道添加成功');
        }
        return array('code'=>1,'msg'=>'渠道添加失败');
    }

    /*
    |--------------------------------------------------------------------------
    | Channel 渠道修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function edit_channel($data)
    {
        //必须参数
        if(empty($data['channel_id'])) return array('code'=>2,'msg'=>'参数异常');
        $reflag = D('Channel')->where(array('channel_id'=>$data['channel_id']))->save($data);
        if($reflag!==false) {
            F('Cache/channel/channel', null);
            return array('code'=>0,'msg'=>'渠道操作成功');
        }
        return array('code'=>1,'msg'=>'渠道操作失败');
    }


    /*
    |--------------------------------------------------------------------------
    | Channel 获取想关联的ID
    |--------------------------------------------------------------------------
    | @author Nixx
    */
    public function getChannelIds($channel_id = 0)
    {
        if( F('Cache/channel/channel') ) {
            $channelist = F('Cache/channel/channel');
        }else{
            $channelist['data'] = D('Channel')->select();
            $channelist['count'] = D('Channel')->count();
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
        return array('code'=>'0', 'data'=>$_channelist['data']);
    }

    /*
    |--------------------------------------------------------------------------
    | Channel 获取渠道名称  包括上级
    |--------------------------------------------------------------------------
    | @author Nixx
    */
    public function getChannelNames($channel_id){
        if( F('Cache/channel/channel') ) {
            $channelist = F('Cache/channel/channel');
        }else{
            $channelist['data'] = D('Channel')->select();
            $channelist['count'] = D('Channel')->count();
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
        return array('code'=>'0', 'data'=>$renames);
    }


    /**
     * 获取所有渠道
     * @return array
     */
    public function getAllChannel($order='sort desc',$page=null){
        if( F('Cache/channel/channel') ) {
            $channel = F('Cache/channel/channel');
        }else{
            $channel['data'] = D('Channel')->select();
            $channel['count'] = D('Channel')->count();
            F('Cache/channel/channel', $channel);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $channel['data'] = $Arrayhelps->createTree($channel['data'], 0, 'channel_id', 'pid');
        $_channelAll = $this->disposeArray($channel, $order, $page);
        return array('code'=>'0', 'data'=>$_channelAll);
    }

    /**
     * 获取渠道
     * @return array
     */
    public function getChannel($channel_id){

        $channelInfo = D("Channel")->where("channel_id = $channel_id")->find();
        if (!$channelInfo) {
            return array('code'=>1, 'data'=>'无数据');
        }
        return array('code'=>'0', 'data'=>$channelInfo);
    }

}