<?php
/*
* 数据服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class RedisUserService extends BaseService
{
    protected $redis;
    //初始化
    public function _initialize() {
        parent::_initialize();
        $this->redis = new \Redis();
        $this->redis->connect('localhost', '6379');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取数据记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUser($data){
        if($data!==null){
            $arr = array();
            if(empty($data[0])){
                $arr[0] = $data;
            }else{
                $arr = $data;
            }
            foreach($arr as $v){
                //条件集合
                $this->redis->sAdd('set:user:status:'.$v['status'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:zone_id:'.$v['zone_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:channel_id:'.$v['channel_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:course_id:'.$v['course_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:attitude_id:'.$v['attitude_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:infoquality:'.$v['infoquality'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:system_user_id:'.$v['system_user_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:updateuser_id:'.$v['updateuser_id'], 'user_id:'.$v['user_id']);
                $this->redis->sAdd('set:user:createuser_id:'.$v['createuser_id'], 'user_id:'.$v['user_id']);
                //有序集合
                $this->redis->zAdd('zset:user:createtime', $v['createtime'], 'user_id:'.$v['user_id']);
                $this->redis->zAdd('zset:user:updatetime', $v['updatetime'], 'user_id:'.$v['user_id']);
                $this->redis->zAdd('zset:user:allocationtime', $v['allocationtime'], 'user_id:'.$v['user_id']);
                $this->redis->zAdd('zset:user:nextvisit', $v['nextvisit'], 'user_id:'.$v['user_id']);
                $this->redis->zAdd('zset:user:lastvisit', $v['lastvisit'], 'user_id:'.$v['user_id']);
                $this->redis->zAdd('zset:user:visittime', $v['visittime'], 'user_id:'.$v['user_id']);
                //哈希表
                $this->redis->hMset('hash:user:'.$v['user_id'], $v);
            }
        }
    }

    public function getList($where=null, $order=null, $limit='0,30'){
        $limit = explode(',', $limit);
        if($where!==null){
            if(!$this->redis->exists('zset:temp:list:'.http_build_query($where))){
                foreach($where as $k=>$v){
                    if($k=='zl_user.system_user_id|updateuser_id'){
                        if($this->redis->exists('zset:temp:list:'.http_build_query($where))){
                            $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('set:user:system_user_id:'.$v, 'set:user:updateuser_id:'.$v,'zset:temp:list:'.http_build_query($where)), null, 'min');
                        }else{
                            $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('set:user:system_user_id:'.$v, 'set:user:updateuser_id:'.$v,'zset:user:allocationtime'), null, 'min');
                        }
                    }elseif($k=='zl_user.status'){
                        if(!empty($v[0]) && $v[0]=='IN'){
                            foreach($v[1] as $v2){
                                $statusArr[] = 'set:user:status:'.$v2;
                            }
                            $this->redis->zUnion('temp:where:status', $statusArr, null, 'min');
                            if($this->redis->exists('zset:temp:list:'.http_build_query($where))){
                                $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('zset:temp:list:'.http_build_query($where), 'temp:where:status'), null, 'min');
                            }else{
                                $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('temp:where:status', 'zset:user:allocationtime'), null, 'min');
                            }
                        }else{
                            if($this->redis->exists('zset:temp:list:'.http_build_query($where))){
                                $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('set:user:status:'.$v,'zset:temp:list:'.http_build_query($where)), null, 'min');
                            }else{
                                $this->redis->zInter('zset:temp:list:'.http_build_query($where), array('set:user:status:'.$v,'zset:user:allocationtime'), null, 'min');
                            }
                        }
                    }
                }
                $this->redis->expire('zset:temp:list:'.http_build_query($where),60);
            }
        }
        $list = $this->redis->zRange('zset:temp:list:'.http_build_query($where), $limit[0], $limit[1]);
        $count = $this->redis->zCount('zset:temp:list:'.http_build_query($where),0,999999);
        $reArr = array();
        foreach($list as $v){
            $v = explode('user_id:',$v);
            $reArr[] = $this->redis->hGetAll('hash:user:'.$v[1]);
        }
        return array('data'=>$reArr,'count'=>$count);
    }
}