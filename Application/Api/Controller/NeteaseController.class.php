<?php
/*
|--------------------------------------------------------------------------
| 网易云信相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class NeteaseController extends ApiBaseController
{
    protected $header;
    public function _initialize()
    {
        parent::_initialize();
        //网易云信Key Secret
        $AppKey = "";
        $AppSecret = "0488bc857ed9";
        $this->header = array(
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            'AppKey:'.$AppKey,
            'Nonce:'.'zelin1',
            'CurTime:'.time(),
            'CheckSum:'.sha1($AppSecret.'zelin1'.time())
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
            'maxDur'=>600
        );
        //调取接口
        $reData = $this->thisCurl($url,$post_data);

        dump($reData);exit();
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

        dump($reData);exit();
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
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_data));
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        curl_close ($ch);
        return json_decode($res);
    }
}
