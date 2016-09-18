<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class ApiController extends BaseController
{
    /*
   |--------------------------------------------------------------------------
   | 获取IP地址
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getApiIplookup($ip)
    {
        if($ip=='127.0.0.1'){
            return array('code'=>0,'data'=>array('city'=>'本机地址','county'=>''));
        }
        $ch = curl_init();
        $url = C('SHOWAPI_IP_URL').'?ip='.$ip;
        $header = array(
            'apikey: '.C('API_STORE_KEY'),
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        curl_close ( $ch );
        $res = json_decode($res);
        if($res->showapi_res_code==0){
            return array('code'=>0,'data'=>array('city'=>$res->showapi_res_body->city,'county'=>$res->showapi_res_body->county));
        }
        return array('code'=>$res->showapi_res_code,'data'=>'','msg'=>$res->showapi_res_error);
    }

    /*
    |--------------------------------------------------------------------------
    | 发送短信-阿里大于
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function sendSms($smsType, $data)
    {
        //阿里大鱼短信模板
        $smspages = C('SMSDATA');
        $smsData = $smspages[$smsType]['smsdata'];
        //短信模版赋值
        foreach($smsData as $k=>$v){
            if($k=='code'){
                //生成随机的6位验证码
                $num = rand(100000, 999999);
                $strNum = strval($num);
                $smsData['code'] = $strNum;
                session('smsVerifyCode_'.$smsType, $strNum);
            }elseif($k=='time'){
                $smsData['time'] = $data['time'];
            }elseif($k=='city'){
                $smsData['city'] = $data['city'];
            }elseif($k=='realname'){
                $smsData['realname'] = $data['realname'];
            }elseif(!empty($data[$k])){
                $smsData[$k] = $data[$k];
            }
        }
        //发送短信验证码
        $result =  $this->sms($data['mobile'], $smsType.'_'.$data['mobile'], $smsData, $smspages[$smsType]['smsdata_id'], $smspages[$smsType]['signName']);
        $result = (array) $result;
        if($result['code']==0){
            return array('code'=>0, 'msg'=>'短信已经发送,请查收');
        }else{
            session('smsVerifyCode_'.$smsType, null);
            return array('code'=>$result['code'], 'msg'=>!empty($result['sub_msg'])?$result['sub_msg']:'发送验证码失败');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 发送短信-国宇·企信通
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function sendSmsGY($data)
    {
        //短信发送
        $query = array(
            'username'=>C('GYUSERNMAE'),
            'password'=>C('GYPASSWORD'),
            'mobile'=>trim($data['mobile']),
            'content'=>trim($data['content']),
        );
        $ch = curl_init();
        $url = C('GYURL_SENDSMS').'?'.http_build_query($query);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch , CURLOPT_URL , $url);
        $send_flag = curl_exec($ch);
        curl_close ( $ch );
        if(strpos($send_flag,'OK')!==false){
            return array('code'=>0, 'msg'=>'短信已经发送');
        }else{
            return array('code'=>1, 'msg'=>$send_flag);
        }
    }
}