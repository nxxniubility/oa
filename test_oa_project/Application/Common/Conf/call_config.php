<?php
/**
 * ����绰����
 * @author zgt
 * @return array
 */

if(!defined('THINK_PATH')) exit('�Ƿ�����');//��ֹ���ⲿϵͳ����

return array(
    //����绰����  1����������  2���������ͨ
    'CALLSERVE' => 1,
    //��������������
    'NETEASE' => array(
        'APP_KEY'=>'145dee3e0853d2bca995849901ee7e31',
        'APP_SECRET'=>'0488bc857ed9',
        'CALL_STATUS'=> array(
            'NORMAL_CLEARING'=>'��������',
            'CALL_REJECTED'=>'���б��ܾ�',
            'NONE'=>'����ʧ��',
            'ORIGINATOR_CANCEL'=>'����ʧ��',
            'NORMAL_TEMPORARY_FAILURE'=>'������·��ʱ',
            'NORMAL_UNSPECIFIED'=>'����δӦ��',
            'NO_ANSWER'=>'����δӦ��',
            'NO_USER_RESPONSE'=>'����δӦ��ʱ',
            'USER_BUSY'=>'�û�ռ�߷�æ',
            'UNALLOCATED_NUMBER'=>'��·��ͨ',
            'RECOVERY_ON_TIMER_EXPIRE'=>'ý�峬ʱ',
        )
    ),
);