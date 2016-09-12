<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class DataController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | 添加数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataMarket($where)
    {
        //必须参数
        if(empty($where['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        //$where['num'] =  +1/-1
        $data_where['daytime'] = strtotime(date('Y-m-d'));
        $data_where['system_user_id'] = $where['system_user_id'];
        $systemdata = D('DataMarket')->where($data_where)->find();
        if(empty($systemdata)){
            D('DataMarket')->add($data_where);
        }
        $dataMarket_arr = explode(',',$where['user_id']);
        $field = $where['name'];
        $exp = !empty($where['exp'])?$where['exp']:'+';   // + -
        $num = count($dataMarket_arr);
        $data_save[$field] = array('exp', $field.$exp.$num);
        $flag_save = D('DataMarket')->where($data_where)->save($data_save);
        if($flag_save!==false){
            return array('code'=>0,'msg'=>'数据添加成功');
        }
        return array('code'=>1,'msg'=>'数据添加失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataLogs($data)
    {
        if(empty($data['logtime']))  $data['logtime'] = time();
        $userList = D('User')->field('user_id,createuser_id,updateuser_id,system_user_id,zone_id,channel_id,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        $add_arr = array();
        foreach($userList as $k=>$v){
            $add_arr[] = array(
                'zone_id'=>$v['zone_id'],
                'channel_id'=>$v['channel_id'],
                'infoquality'=>$v['infoquality'],
                'createuser_id'=>$v['createuser_id'],
                'updateuser_id'=>$v['updateuser_id'],
                'system_user_id'=>$v['system_user_id'],
                'user_id'=>$v['user_id'],
                'logtime'=>$data['logtime'],
                'operator_user_id'=>$data['operator_user_id'],
            );
        }
        return D('DataLogs')->addAll($add_arr);
    }
}