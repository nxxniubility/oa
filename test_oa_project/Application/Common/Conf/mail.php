<?php
/**
*自动读取邮件程序的邮件地址配置
*@authorEcho
*@returnarray
*/

if(!defined('THINK_PATH'))exit('非法调用');//防止被外部系统调用

$mail=array();
$mail['mail']=array();
//User name off the mail box
$mail['mail']['username']='zelinedu@zelinonline.com';  //zelinedu@zelinonline.com guo_haiming@zelinonline.com
//Password of mailbox
$mail['mail']['password']='Zelin@)!)0826Online';  //  Guohaiming12  Zelin@)!)0826Online
//Email address of that mailbox some time the uname and email address are identical
$mail['mail']['emailAddress']='zelinedu@zelinonline.com';  //  
//Ip or name of the POP or IMAP mail server
$mail['mail']['mailserver']='pop.exmail.qq.com'; 
//if this server is imap or pop default is pop
$mail['mail']['servertype']='imap'; 
//Server port for pop or imap Default is 110 for pop and 143 for imap
$mail['mail']['port']='143'; 
//每次启动脚本时读取的邮件条数
$mail['mail']['maxread']='50';
//是否删除邮件
$mail['mail']['is_del']=true;

//返回数组
return $mail;
