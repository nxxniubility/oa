<?php

/**
 * 别名对应表，仅起到参照作用
 */
return array(
    'TABLE_ALIAS' => array(
        //数据库设置
        'D' =>  'department',
        'R' =>  'role',
        'RU' => 'role_user',
        'SU' => 'system_user',
    ),
    'VIEW_FIELDS' => array(
        'DepartmentRoleSystemUserView' => array(
            //部门管理表 Department
            "department_id" => "D.department_id", //部门管理ID
            "pid" => "D.pid", //对应父级ID
            "departmentname" => "D.departmentname", //部门名称
            "sort" => "D.sort", //排序
            "status" => "D.status", //数据状态（0无效,1有效）
            //角色表 Role
            "id" => "R.id", //角色ID
            "name" => "R.name", //名次=称
            "pid" => "R.pid", //
            "superiorid" => "R.superiorid", //上级ID
            "status" => "R.status", //状态
            "remark" => "R.remark", //备注
            "sort" => "R.sort", //排序
            "department_id" => "R.department_id", //部门ID
            "display" => "R.display", //显示状态（0：禁用，1：启用）
            //角色与员工关联表 RoleUser
            "role_user_id" => "RU.role_user_id",
            "role_id" => "RU.role_id", //角色ID
            "user_id" => "RU.user_id", //用户ID
            //员工表 SystemUser
            "system_user_id" => "SU.system_user_id", //用户ID
            "zone_id" => "SU.zone_id", //所属区域ID
            "username" => "SU.username", //用户名
            "password" => "SU.password", //密码
            "realname" => "SU.realname", //真实姓名
            "sign" => "SU.sign", //姓氏首字首字母，用于排序
            "nickname" => "SU.nickname", //昵称
            "sex" => "SU.sex", //性别（0：未知，1男，2女）
            "face" => "SU.face", //头像
            "email" => "SU.email", //邮箱
            "emailpassword" => "SU.emailpassword", //邮箱密码
            "check_id" => "SU.check_id", //考勤编号
            "isuserinfo" => "SU.isuserinfo",//是否已添加员工档案（0无，1有）
            "usertype" => "SU.usertype", //用户类型（10离职员工，20兼职，30实习生，40试用期，50正式员工）
            "status" => "SU.status", //账号状态（0：无效，1有效，2封停账号）
            "logintime" => "SU.logintime", //登录时间
            "loginip" => "SU.loginip", //登录IP
            "createtime" => "SU.createtime", //创建时间
            "createip" => "SU.createip", //创建IP
            "departuretime" => "SU.departuretime", //离职时间
        )
    )
);
