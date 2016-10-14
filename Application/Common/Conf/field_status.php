<?php
/**
 * 用户状态常量设置
 * @author Sunles
 * @return array
 */
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    'FIELD_STATUS'=>array(
        'COURSE_TYPE'=>array(
            1 => '系统培训班',
            2 => '进阶课程',
            3 => '视频课程',
        ),

    )
);