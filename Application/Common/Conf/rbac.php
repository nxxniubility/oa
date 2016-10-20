<?php
return array(/*'配置项'=>'配置值'*/

    /*权限管理配置*/
    'USER_AUTH_ON'  =>    true,//是否需要认证
    'USER_AUTH_TYPE'    =>  1,//认证类型
    'USER_AUTH_KEY' =>  'system_user_id',//认证识别号
    'ROLEID_AUTH_KEY' =>  'system_role_id',//认证识别号
    'REQUIRE_AUTH_MODULE'   =>  '',//需要认证模块
    'NOT_AUTH_MODULE'   =>  '',//无需认证模块
    'NOT_AUTH_ACTION'           =>  'addSystemUserInfoTwo,addSystemUserInfo,getOssSign,loginAlarm',		// 默认无需认证操作
    'USER_AUTH_GATEWAY' =>  '/System/Admin/reLogin',//认证网关
    'RBAC_DB_DSN'   =>  C('DB_TYPE').'://'.C('DB_USER').':'.C('DB_PWD').'@'.C('DB_HOST').':'.C('DB_PORT').'/'.C('DB_NAME'),//数据库连接DSN
    'RBAC_ROLE_TABLE'   =>  'zl_role',//角色表名称
    'RBAC_USER_TABLE'   =>  'zl_role_user',//用户表名称
    'RBAC_ACCESS_TABLE' =>  'zl_access',//权限表名称
    'RBAC_NODE_TABLE'   =>  'zl_node',//节点表名称
    'RBAC_ERROR_PAGE'   =>  'System/Admin/accessError',//提示无权限页面
    'GUEST_AUTH_ON'     =>  false, //游客权限功能是否开启
    'GUEST_AUTH_ID'     =>  0,//游客
);