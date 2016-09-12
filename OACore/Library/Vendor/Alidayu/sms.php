<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/15
 * Time: 13:26
 */
include "TopSdk.class.php";
date_default_timezone_set('Asia/Shanghai');

$c = new TopClient;
$c->appkey = '23346633';
$c->secretKey = '90aed247091e217fb2891bae8ab82a3a';
$req = new AlibabaAliqinFcSmsNumSendRequest;
$req->setExtend("123456");
$req->setSmsType("normal");
$req->setSmsFreeSignName();
$req->setSmsParam("{\"code\":\"1234\",\"product\":\"alidayu\"}");
$req->setRecNum("15814043738");
$req->setSmsTemplateCode("SMS_5260550");
$resp = $c->execute($req);
print_r($resp);