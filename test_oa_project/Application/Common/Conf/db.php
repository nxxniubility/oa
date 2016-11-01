<?php
/**
 * 数据库配置文件
 * @author Sunles
 * @return array
 */

if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    // 数据库设置
    'DB_TYPE'   => 'mysql',
    'DB_HOST'   => 'rds0q0x59aut678p041j.mysql.rds.aliyuncs.com',
    'DB_NAME'   => 'zelininfo_test',
    'DB_USER'   => 'sunles',
    'DB_PWD'    => 'sunles_Aaa',
    'DB_PORT'   => '3306',
    'DB_PREFIX' => 'zl_',


	// 'DB_TYPE'   => 'mysql',
	// 'DB_HOST'   => 'rds0q0x59aut678p041j.mysql.rds.aliyuncs.com',
	// 'DB_NAME'   => 'zelininfo',
	// 'DB_USER'   => 'zelininfo',
	// 'DB_PWD'    => 'zelin007A',
	// 'DB_PORT'   => '3306',
	// 'DB_PREFIX' => 'zl_',

);
