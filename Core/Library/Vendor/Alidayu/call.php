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
$req = new AlibabaAliqinFcVoiceNumDoublecallRequest;
$req->setSessionTimeOut("120");
$req->setExtend("12345");
$req->setCallerNum("15814043738");
$req->setCallerShowNum("057188773344");
$req->setCalledNum("18600975132");
$req->setCalledShowNum("057188773344");
$resp = $c->execute($req);
print_r($resp);