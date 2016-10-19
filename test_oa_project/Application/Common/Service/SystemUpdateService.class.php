<?php
/*
* ϵͳ���½ӿ�
* @author zgt
*
*/
namespace Common\Service;
use Common\Service\DataService;
use Common\Service\BaseService;

class SystemUpdateService extends BaseService
{
    //��ʼ��
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
   |--------------------------------------------------------------------------
   | ��ȡϵͳ������Ϣ
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateList($param)
    {
        $param = array_filter($param);
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/systemUpdate') ) {
            $list = F('Cache/systemUpdate');
        }else{
            $list = $this->_getSystemUpdateList();
            F('Cache/systemUpdate', $list);
        }
        $list = $this->disposeArray($list,  $param['order'], $param['page'],  $param['where']);
        return array('code'=>'0', 'data'=>$list);
    }

    /*
    |--------------------------------------------------------------------------
    | SystemUpdate ϵͳ��������
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addSystemUpdate($param)
    {
        //�������
        $param = array_filter($param);
        $param['system_user_id'] = $this->system_user_id;
        if(empty($param['uptitle'])) return array('code'=>300,'msg'=>'ȱ��ϵͳ��������');
        $result = D('SystemUpdate')->addData($param);
        //�������ݳɹ�ִ���������
        if ($result['code']==0){
            if (F('Cache/systemUpdate')) {
                $new_info = D('SystemUpdate')->getFind(array("system_update_id"=> $result['data']));
                $cahce_all = F('Cache/systemUpdate');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/systemUpdate', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | SystemUpdate ϵͳ�����޸�
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editSystemUpdate($param)
    {
        //�������
        $param = array_filter($param);
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'�����쳣');
        $result = D('SystemUpdate')->editData($param,$param['system_update_id']);
        //�������ݳɹ�ִ���������
        if ($result['code']==0){
            if (F('Cache/systemUpdate')) {
                $new_info = D('SystemUpdate')->getFind(array("system_update_id"=>$param['system_update_id']));
                $cahce_all = F('Cache/systemUpdate');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['system_update_id'] == $param['system_update_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/systemUpdate', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | ɾ��ϵͳ��������---�����ļ�����
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delSystemUpdate($param)
    {
        //�������
        $param = array_filter($param);
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'�����쳣');
        $result = D('SystemUpdate')->delData($param['system_update_id']);
        //�������ݳɹ�ִ���������
        if ($result['code']==0){
            if (F('Cache/systemUpdate')) {
                $cahce_all = F('Cache/systemUpdate');
                if(!empty($cahce_all)){
                    foreach($cahce_all['data'] as $k=>$v){
                        if($v['system_update_id'] == $param['system_update_id']){
                            unset($cahce_all['data'][$k]);
                        }
                    }
                    $cahce_all['count'] =  $cahce_all['count']-1;
                }
                F('Cache/systemUpdate', $cahce_all);
            }
        }
        return $result;
    }

    /*
   |--------------------------------------------------------------------------
   | systemUpdate ��ȡϵͳ��������
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateInfo($param)
    {
        //�������
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'�����쳣');
        if( F('Cache/systemUpdate') ) {
            $channe_list = F('Cache/systemUpdate');
        }else{
            $channe_list = $this->_getSystemUpdateList();
            F('Cache/systemUpdate', $channe_list);
        }
        foreach($channe_list['data'] as $k=>$v){
            if($v['system_update_id']==$param['system_update_id']){
                $channel_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$channel_info);
    }

    /**
     * ��ȡ�����б� + ״̬ת��
     * @return array
     */
    protected function _getSystemUpdateList()
    {
        $list['data'] = D('SystemUpdate')->getList();
        $list['count'] = D('SystemUpdate')->getCount();
        if(!empty($list['data'])){
            foreach($list['data'] as $k=>$v){
                if(!empty($v['system_user_id'])){
                    $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
                    $list[$k]['system_realname'] = $systemUser['data']['realname'];
                }
                $list[$k]['create_time'] = date('Y-m-d H:i', $v['createtime']);
            }
        }
        return $list;
    }

}