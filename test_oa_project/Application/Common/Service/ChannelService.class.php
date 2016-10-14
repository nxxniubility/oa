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
     * 获取渠道列表
     * @return array
     */
    protected function _getChannelList()
    {
        $channel['data'] = D('Channel')->getList();
        $channel['count'] = D('Channel')->getCount();
        return $channel;
    }

    /*
   |--------------------------------------------------------------------------
   | Channel 获取所有渠道
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelList($param)
    {
        $param['where']['status'] = 1;
        $param['order'] = 'sort desc';
        $param['page'] = !empty($param['page'])?$param['page']:'1,30';
        if( F('Cache/channel') ) {
            $channel = F('Cache/channel');
        }else{
            $channel = $this->_getChannelList();
            F('Cache/channel', $channel);
        }
        $channel = $this->disposeArray($channel,  $param['order'], $param['page'],  $param['where']);
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $channel['data'] = $Arrayhelps->createTree($channel['data'], 0, 'channel_id', 'pid');
        return array('code'=>'0', 'data'=>$channel);
    }

    /*
   |--------------------------------------------------------------------------
   | Channel 渠道添加
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addChannel($param)
    {
        //必须参数
        $param['system_user_id'] = $this->system_user_id;
        if(empty($param['channelname'])) return array('code'=>300,'msg'=>'缺少渠道名称');
        $result = D('Channel')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/channel')) {
                $new_info = D('Channel')->getFind(array("channel_id"=> $result['data']));
                $cahce_all = F('Cache/channel');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/channel', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Channel 渠道修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editChannel($param)
    {
        //必须参数
        if(empty($param['channel_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('Channel')->editData($param,$param['channel_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/channel')) {
                $new_info = D('Channel')->getFind(array("channel_id"=>$param['channel_id']));
                $cahce_all = F('Cache/channel');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['channel_id'] == $param['channel_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/channel', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 删除渠道详情---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delChannel($param)
    {
        //必须参数
        if(empty($param['channel_id'])) return array('code'=>300,'msg'=>'参数异常');
        $param['status'] = 0;
        $result = D('Channel')->editData($param,$param['channel_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/channel')) {
                $new_info = D('Channel')->getFind(array("channel_id"=>$param['channel_id']));
                $cahce_all = F('Cache/channel');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['channel_id'] == $param['channel_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/channel', $cahce_all);
            }
        }
        return $result;
    }

    /*
   |--------------------------------------------------------------------------
   | Channel 获取渠道详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelInfo($param)
    {
        //必须参数
        if(empty($param['channel_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/channel') ) {
            $channe_list = F('Cache/channel');
        }else{
            $channe_list = $this->getList();
            F('Cache/channel', $channe_list);
        }
        foreach($channe_list['data'] as $k=>$v){
            if($v['channel_id']==$param['channel_id']){
                $channel_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$channel_info);
    }

    /*
   |--------------------------------------------------------------------------
   | Channel 获取渠道 及 下级渠道集合
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getChannelChildren($param)
    {
        if( F('Cache/channel') ) {
            $channe_list = F('Cache/channel');
        }else{
            $channe_list = $this->getList();
            F('Cache/channel', $channe_list);
        }
        //数组分级
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $_channe_list['data'] = $Arrayhelps->subFinds($channe_list['data'],$param['channel_id'],'channel_id','pid');
        foreach($channe_list['data'] as $k=>$v){
            if($v['channel_id']==$param['channel_id']){
                $_channe_list['data'][] = $v;
            }
        }
        return array('code'=>'0', 'data'=>$_channe_list['data']);
    }
}
