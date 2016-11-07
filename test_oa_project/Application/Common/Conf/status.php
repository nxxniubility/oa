<?php
/**
 * 用户状态常量设置
 * @author Sunles
 * @return array
 */
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    'FIELD_STATUS'=>array(
        //消息类型
        'MSG_TYPE'=>array(
            1 => '系统通知',
            10 => '消息通知',
        ),
        //学习平台
        'USER_LEARNINGTYPE'=>array(
            1 => '泽林',
            2 => '8点1课',
        ),
        //产品类型
        'COURSE_TYPE'=>array(
            1 => '系统培训班',
            2 => '进阶课程',
            3 => '视频课程',
        ),
        //员工状态
        'SYSTEMUSERSTATUS'=>array(
            10 => '离职',
            20 => '兼职',
            30 => '实习生',
            40 => '试用期',
            50 => '正式员工',
        ),
        //学历
        'EDUCATION_ARRAY'=>array(
            1=>'小学',
            2=>'初中',
            9=>'中专',
            3=>'高中',
            4=>'大专',
            5=>'本科',
            6=>'硕士',
            7=>'博士',
            8=>'博士后',
            10=>'其他',
        ),
        //婚姻状态
        'MARITAL'=>array(
            0 => '未婚',
            1 => '已婚',
            2 => '未知',
        ),
        //性别
        'SEX'=>array(
            0 => '未知',
            1 => '男',
            2 => '女',
        ),
        //客户跟进状态
        'USER_STATUS'=>array(
            20 => '待联系',
            30 => '待跟进',
//        '60' => '预报',
            70 => '交易',
//        '80' => '缴费',
            120 => '退费',
            160 => '回库',
        ),
        //跟进结果
        'USER_ATTITUDE'=>array(
            1 => '意向学习',
            2 => '承诺到访',
            3 => '明确拒绝',
            4 => '已培训',
            5 => '已入职',
            6 => '关机',
            7 => '空号',
            8 => '无人接听',
            9 => '考虑',
            11 => '年龄不符',
            12 => '学历不符',
            13 => '薪酬过高',
            14 => '非本地客户',
            10 => '其他',
        ),
        //信息质量转换
        'USER_INFOQUALITY'=>array(
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D'
        ),
        //用户申请状态转换
        'USER_APPLY_STATUS'=>array(
            10 => '审核中',
            20=> '审核失败',
            30 => '审核通过',
        ),
        //回访方式
        'USER_CALLBACK'=>array(
            10 => '电话',
            11 => 'QQ',
            12 => '面谈',
            13 => '邮箱',
            14 => '在线咨询',
            15 => '其他'
        ),
        //更新重复量时，设置保护期限
        'UPDATE_PROTECT'=>array(
            1=>'7',//单位：天
        ),
    ),
    //最近跟进结果
    'SYSTEM_USER_STATUS'=>array(
        '10' => array('num'=>10,'text'=>'离职'),
        '20' => array('num'=>20,'text'=>'兼职'),
        '30' => array('num'=>30,'text'=>'实习生'),
        '40' => array('num'=>40,'text'=>'试用期'),
        '50' => array('num'=>50,'text'=>'正式员工')
    ),

    //邀约状态
    'USER_STATUS'=>array(
        '20' => array('num'=>20,'text'=>'待联系'),
        '30' => array('num'=>30,'text'=>'待跟进'),
//        '60' => array('num'=>60,'text'=>'预报'),
        '70' => array('num'=>70,'text'=>'交易'),
//        '80' => array('num'=>80,'text'=>'缴费'),
        '120' => array('num'=>120,'text'=>'退费'),
        '160' => array('num'=>160,'text'=>'回库'),
    ),

    //用户申请状态
    'USER_APPLY_STATUS' =>array(
        '10' => array('num'=>10,'text'=>'审核中'),
        '20' => array('num'=>20,'text'=>'审核失败'),
        '30' => array('num'=>30,'text'=>'审核通过')
    ),
    //用户申请状态转换
    'USER_APPLY_STATUS_CONVERT'=>array(
        '10' => '审核中',
        '20' => '审核失败',
        '30' => '审核通过',
    ),

    //最近跟进结果
    'USER_ATTITUDE'=>array(
        '1' => array('num'=>1,'text'=>'意向学习'),
        '2' => array('num'=>2,'text'=>'承诺到访'),
        '3' => array('num'=>3,'text'=>'明确拒绝'),
        '4' => array('num'=>4,'text'=>'已培训'),
        '5' => array('num'=>5,'text'=>'已入职'),
        '6' => array('num'=>6,'text'=>'关机'),
        '7' => array('num'=>7,'text'=>'空号'),
        '8' => array('num'=>8,'text'=>'无人接听'),
        '9' => array('num'=>9,'text'=>'考虑'),
        '11' => array('num'=>11,'text'=>'年龄不符'),
        '12' => array('num'=>12,'text'=>'学历不符'),
        '13' => array('num'=>13,'text'=>'薪酬过高'),
        '14' => array('num'=>14,'text'=>'非本地客户'),
        '10' => array('num'=>10,'text'=>'其他'),
    ),

    //回访方式
    'USER_CALLBACK'=>array(
        '10' => array('num'=>10,'text'=>'电话'),
        '11' => array('num'=>11,'text'=>'QQ'),
        '12' => array('num'=>12,'text'=>'面谈'),
        '13' => array('num'=>13,'text'=>'邮箱'),
        '14' => array('num'=>14,'text'=>'在线咨询'),
        '15' => array('num'=>15,'text'=>'其他')
    ),

    //学习平台
    'USER_LEARNINGTYPE'=>array(
        '1' => array('num'=>1,'text'=>'泽林'),
        '2' => array('num'=>2,'text'=>'8点1课'),
    ),

    //学习方式
    'USER_STUDYTYPE'=>array(
        '1' => array('num'=>1,'text'=>'脱产'),
        '2' => array('num'=>2,'text'=>'业余'),
    ),

    //贷款机构
    'USER_LOAN_INSTITUTIONS'=>array(
        '1' => array('num'=>1,'text'=>'不贷款'),
        '10' => array('num'=>10,'text'=>'宜信'),
        '20' => array('num'=>20,'text'=>'玖富'),
        '30' => array('num'=>30,'text'=>'其他贷款'),
    ),

    //缴费方式
    'USER_RECEIVETYPE'=>array(
        '1' => array('num'=>1,'text'=>'现金'),
        '2' => array('num'=>2,'text'=>'刷卡'),
        '3' => array('num'=>3,'text'=>'转账'),
        '10' => array('num'=>10,'text'=>'贷款转账'),
    ),

    'USER_RECEIVETYPE_CONVERT'=>array(
        '1' => '现金',
        '2' => '刷卡',
        '3' => '转账',
    ),

    //信息质量转换
    'USER_INFOQUALITY'=>array(
        '1' => 'A',
        '2' => 'B',
        '3' => 'C',
        '4' => 'D'
    ),


    'ORDER_STATUS'=>array(
        '10'=>'待审核',
        '20'=>'审核不通过',
        '30'=>'审核通过',
        '40'=>'交易中',
        '50'=>'完成交易',
        '60'=>'部分退费',
        '70'=>'全额退款',
    ),

    'EDUCATION_ARRAY'=>array(
        '1'=>'小学',
        '2'=>'初中',
        '9'=>'中专',
        '3'=>'高中',
        '4'=>'大专',
        '5'=>'本科',
        '6'=>'硕士',
        '7'=>'博士',
        '8'=>'博士后',
        '10'=>'其他',
    ),

    //产品平台
    'PRODUCT_PROJECT'=>array(
        '1' => '泽林',
        '10' => '8点1课'
    ),


);