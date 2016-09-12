<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
// start profiling
// xhprof_enable();
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);
// 定义应用目录
define('BIND_MODULE','Cmd');
define('APP_PATH',dirname(__FILE__).'/../Application/');
define('TMPL_PATH', 'Templets/');
//定义根目录路径
define('BASE_PATH', dirname(__FILE__));
//runtime 目录
define('RUNTIME_PATH',dirname(__FILE__).'/../Runtime/');

$ct = isset($argv[1]) ? $argv[1] : 'mail';
if($ct=="allot")
{
	$_GET['c']="Allot";
	$_GET['type']=10;
}else if($ct=="recover")
{
	$_GET['c']="Allot";
	$_GET['type']=20;
}else if($ct=="mail")
{
	$_GET['c']="Mail";
}
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../OACore/Core.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单

