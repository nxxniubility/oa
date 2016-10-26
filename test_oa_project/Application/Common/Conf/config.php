<?php
return array(
    /*'配置项'=>'配置值'*/
    'LOAD_EXT_CONFIG' => 'db,rbac,system,status,alidayu,email,oss,site,var,mail,baidu,ordername,apistore,rds,call_config',//加载配置文件
    'URL_MODEL'=>'2',
    'DEFAULT_MODULE'=> 'System', //默认模块
    'PHONE_CODE_KEY'=>'123456',  //手机号码的密钥
    //拓展标签
    'AUTOLOAD_NAMESPACE' => array('Extend' =>'../Extend'),
    // 标签
    'TAGLIB_PRE_LOAD' => 'Extend\\TagLib\\Zelin',
    /*URL*/
    'URL_CASE_INSENSITIVE'  =>  false,  // URL区分大小写
    'PER_PAGE_NUM' => 30,    //每页的数据条数
    /*语言包*/
    'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
    /*数据库切换*/
    'DB_CONFIG1' => array(
        'DB_TYPE'   => 'mysql',
        'DB_HOST'   => 'rds0q0x59aut678p041j.mysql.rds.aliyuncs.com',
        'DB_NAME'   => 'zelininfo',
        'DB_USER'   => 'zelininfo',
        'DB_PWD'    => 'zelin007A',
        'DB_PORT'   => '3306',
    ),
);