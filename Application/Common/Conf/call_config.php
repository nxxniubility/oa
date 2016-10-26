<?php
/**
 * 网络电话服务
 * @author zgt
 * @return array
 */

if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

return array(
    //网络电话服务方  1：网易云信  2：电信翼呼通
    'CALLSERVE' => 1,
    //网易云信配置项
    'NETEASE' => array(
        'APP_KEY'=>'145dee3e0853d2bca995849901ee7e31',
        'APP_SECRET'=>'0488bc857ed9',
        'CALL_STATUS'=> array(
            'NORMAL_CLEARING'=>'呼叫正常',
            'CALL_REJECTED'=>'呼叫被拒绝',
            'NONE'=>'呼叫失败',
            'ORIGINATOR_CANCEL'=>'呼叫失败',
            'NORMAL_TEMPORARY_FAILURE'=>'呼叫线路超时',
            'NORMAL_UNSPECIFIED'=>'呼叫未应答',
            'NO_ANSWER'=>'呼叫未应答',
            'NO_USER_RESPONSE'=>'呼叫未应答超时',
            'USER_BUSY'=>'用户占线繁忙',
            'UNALLOCATED_NUMBER'=>'线路不通',
            'RECOVERY_ON_TIMER_EXPIRE'=>'媒体超时',
        )
    ),
);