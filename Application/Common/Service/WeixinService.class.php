<?php
/*
|--------------------------------------------------------------------------
| 微信数据相关
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Common\Service;
use Common\Service\BaseService;

class WeixinService extends BaseService
{
    protected $api_appid = 'wxf39b82a9b82c14bb';
    protected $api_secret = '947bce75228c8f8bbce27a9d60ea6835';
    protected $api_url = array(
        //获取微信token
        'GET_TOKEN'=>'https://sz.api.weixin.qq.com/cgi-bin/token'
    );

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取获取微信Token
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getToken()
    {
        //获取access_token 时效6000秒
        if(!S('wx_access_token')){
            $_data['appid'] = $this->api_appid;
            $_data['secret'] = $this->api_secret;
            $_data['grant_type'] = 'client_credential';
            $_redata = $this->send_curl($this->api_url['GET_TOKEN'],$_data);
            S('wx_access_token',$_redata->access_token,6000);
        }
        return array('code'=>0,'msg'=>'获取成功','data'=>S('wx_access_token'));
    }


    /*
    |--------------------------------------------------------------------------
    | 微信自定义菜单
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function settingmenu()
    {

    }


    /*
     * 发送CRUL请求
     */
    protected function send_curl($url,$post_data=null,$header=null)
    {
        //开启curl
        $ch = curl_init();
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        //发送参数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post_data));
        // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $res = curl_exec($ch);
        //关闭curl
        curl_close ($ch);
        //转化json格式
        $res = json_decode($res);
        return $res;
    }
}