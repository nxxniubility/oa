<?php
/**
 * 阿里大鱼配置项
 * @author Sunles
 * @return array
 */

if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    //'配置项'=>'配置值'
    'AlidayuAppKey'    => '23346633',  // app key
    'AlidayuAppSecret' => '90aed247091e217fb2891bae8ab82a3a',  // app secret

    //国宇·企信通
    'GYURL_SENDSMS'=>'http://www.gysoft.cn/smspost_utf8/send.aspx',
    'GYUSERNMAE'=>'zelin',
    'GYPASSWORD'=>'1qaz@WSX3edc',

    //短信模版
    'SMSDATA' => array(
        //注册验证
        'register' => array(
            'signName' => '注册验证',
            'smsdata_id' => 'SMS_8971047',
            'smsdata' => array("code"=>"","product"=>"泽林信息")
        ),
        //身份验证
        "authentication" => array(
            "signName" => "身份验证",
            "smsdata_id" => "SMS_5260548",
            "smsdata" => array("code"=>"","product"=>"泽林信息")
        ),
        //登录异常提醒
        "alarm" => array(
            "signName" => "异常登录提醒",
            "smsdata_id" => "SMS_14215231",
            "smsdata" => array("city"=>"","time"=>"","code"=>"","product"=>"OA系统")
        ),
        //登录异常提醒（上级提示）
        "alarmsuperior" => array(
            "signName" => "异常登录提醒",
            "smsdata_id" => "SMS_13460608",
            "smsdata" => array("city"=>"","realname"=>"",'time'=>"","product"=>"OA系统")
        ),
        //深夜登录短信提醒(上级)
        "alarmlatesuperior" => array(
            "signName" => "登录提醒",
            "smsdata_id" => "SMS_14761824",
            "smsdata" =>  array("realname"=>"",'time'=>"","product"=>"OA系统")
        ),
        //员工发送短信到客户
        "sendUser" => array(
            "signName" => "信息通知",
            "smsdata_id" => "SMS_14270261",
            "smsdata" => array("txt"=>"")
        )
    )
);