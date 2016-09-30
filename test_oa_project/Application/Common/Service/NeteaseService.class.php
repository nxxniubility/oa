<?php
/*
|--------------------------------------------------------------------------
| 网易云信相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Common\Service;

use Common\Service\BaseService;

class NeteaseService extends BaseService
{
    //http header
    protected $header;

    public function _initialize()
    {
        parent::_initialize();
        $this->header = array(
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            'AppKey:'.C('NETEASE.APP_KEY'),
            'Nonce:'.'zelin1',
            'CurTime:'.time(),
            'CheckSum:'.sha1(C('NETEASE.APP_SECRET').'zelin1'.time())
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 发起单人专线电话
    |--------------------------------------------------------------------------
    | caller：主叫方电话号码 callee：被叫方电话号码 callerAcc：发起本次请求的用户的accid
    | @author zgt
    */
    public function startcall($caller,$callee)
    {
        $url = 'https://api.netease.im/call/ecp/startcall.action';
        $post_data = array(
            'callerAcc'=>'zelin1',
            'caller'=>$caller,
            'callee'=>$callee,
            'record'=>'true',
            'maxDur'=>1800
        );
        //调取接口
        $reData = $this->thisCurl($url,$post_data);
        if($reData->code==200){
            return array('code'=>0,'msg'=>'呼叫成功','data'=>$reData->obj->session);
        }else{
            return array('code'=>$reData->code,'msg'=>'呼叫失败:'.$reData->msg);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 查询专线电话或会议的详情
    |--------------------------------------------------------------------------
    | session：本次通话的id号 type：通话类型，1:专线电话，2:专线会议
    | @author zgt
    */
    public function queryBySession($session,$type=1)
    {
        $url = 'https://api.netease.im/call/ecp/queryBySession.action';
        $post_data = array(
            'session'=>$session,
            'type'=>$type
        );
        //调取接口
        $reData = $this->thisCurl($url,$post_data);
        if($reData->code==200){
            return array('code'=>0,'msg'=>'查询成功','data'=>$reData->obj);
        }else{
            return array('code'=>$reData->code,'msg'=>'查询失败','data'=>json_encode($reData));
        }
    }


    /**
     * Curl
     * @author zgt
     */
    protected function thisCurl($url,$post_data=null)
    {
        $ch = curl_init();
        // 执行HTTP请求
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_data));
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        curl_close ($ch);
        return json_decode($res);
    }
}