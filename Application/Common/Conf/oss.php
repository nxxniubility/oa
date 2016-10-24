<?php
/**
 * 阿里云配置文件
 * @author luoyu
 * @return array
 */
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
return array(
    'ALIOSS_DRIVER'=>'Alioss',	
	'ALIOSS_CONFIG'=>array(
		//数据库设置
		'OSS_ACCESS_ID'   => 'yKCE6IRWLyp8pYVr',
		'OSS_ACCESS_KEY'   => 'if9ry62gTr49mjLnmcf4Jq1ifLM6IL',
		'OSS_BUCKET'   =>  'oazelin',
		//外网地址
		'OSS_DOMAIN'=>'http://oazelin.oss-cn-shenzhen.aliyuncs.com',
		'OSS_IMG_DOMAIN'=>'img-cn-shenzhen.aliyuncs.com',
		//内网地址
		'OSS_DOMAIN_INTERNAL'=>'http://oazelin.oss-cn-shenzhen-internal.aliyuncs.com',
		//是否记录日志
		"ALI_LOG" => true,
		//自定义日志路径，如果没有设置，则使用系统默认路径，在./logs/
		//'ALI_LOG_PATH'=>'',
		//是否显示LOG输出
		"ALI_DISPLAY_LOG" => "",
		//语言版本设置
		"ALI_LANG" => "zh",
		//用户允许上传的目录
		'ALIOSS_USER_DIR'=>array(
			1=>'user_image/photo/',
			2=>'user_image/',
			3=>'user_dir/',
			4=>'template/',
			5=>'user_file/',
			6=>'user-dir/',
			7=>'excel/'
		)
	)
);

