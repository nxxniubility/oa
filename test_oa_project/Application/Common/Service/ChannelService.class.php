<?php
/*
* 渠道服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\DataService;
use Common\Service\BaseService;

class ChannelService extends BaseService
{
    //初始化
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /**
     * 获取所有渠道
     * @return array
     */
    public function getAllChannel($order='sort desc',$page=null){
        if( F('Cache/channel/channel') ) {
            $channel = F('Cache/channel/channel');
        }else{
            $channel['data'] = D('Channel')->where('system_user_id=0')->select();
            $channel['count'] = D('Channel')->where('system_user_id=0')->count();
            F('Cache/channel/channel', $channel);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $channel['data'] = $Arrayhelps->createTree($channel['data'], 0, 'channel_id', 'pid');
        $_channelAll = $this->disposeArray($channel, $order, $page);
        return array('code'=>'0', 'data'=>$_channelAll);
    }
}