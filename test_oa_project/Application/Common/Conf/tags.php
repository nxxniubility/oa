<?php
/**
 * 语言包/定时器
 * @author Sunles
 */
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    'app_begin' => array('Behavior\CheckLangBehavior'),
    'app_end' => array('Behavior\CronRunBehavior'),    //执行定时任务
);

