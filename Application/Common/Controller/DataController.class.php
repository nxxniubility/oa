<?php

namespace Common\Controller;

use Common\Controller\BaseController;

class DataController extends BaseController
{
    protected $statusArr;

    public function _initialize(){
        parent::_initialize();
        //统计数据状态
        $this->statusArr = array(
            '1'=>'addnum',   //新增量
            '2'=>'acceptnum',   //系统出库量
            '3'=>'directoroutnum',   //出库量
            '4'=>'applynum',   //申请转入量
            '5'=>'switchnum',   //转出量
            '6'=>'restartnum',   //放弃量
            '7'=>'recyclenum',   //系统回收量
            '8'=>'directorrecovernum',   //主管回收
            '9'=>'redeemnum',   //赎回量
//            '10'=>'callbacknum',  //已回访量
            '11'=>'attitudenum',  //跟进次数
            '12'=>'visitnum',  //到访量
            '13'=>'ordernum',  //订单量
            '14'=>'refundnum',  //退款量
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 添加统计记录数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataLogs($data)
    {
        if(empty($data['logtime']))  $data['logtime'] = time();
        $userList = D('User')->field('user_id,status,createuser_id,updateuser_id,system_user_id,zone_id,attitude_id,channel_id,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(!empty($userList)){
            //获取记录数据集合 -> 批量添加
            $add_arr = array();
            foreach($userList as $k=>$v){
                $add_arr[] = array(
                    'zone_id'=>$v['zone_id'],
                    'attitude_id'=>$v['attitude_id'],
                    'channel_id'=>$v['channel_id'],
                    'infoquality'=>$v['infoquality'],
                    'createuser_id'=>$v['createuser_id'],
                    'updateuser_id'=>$v['updateuser_id'],
                    'system_user_id'=>$v['system_user_id'],
                    'user_id'=>$v['user_id'],
                    'operattype'=>$data['operattype'],
                    'logtime'=>$data['logtime'],
                    'operator_user_id'=>$data['operator_user_id'],
                );
                $temp_system_user_id = $v['system_user_id'];
            }
            $addLog_flag = D('DataLogs')->addAll($add_arr);
            //添加营销统计---------------------
            $dataMarket['system_user_id'] = $temp_system_user_id;
            $statusArr = $this->statusArr;
            $dataMarket['name'] = $statusArr[$data['operattype']];
            $dataMarket['user_id'] = $data['user_id'];
            $addMarket_flag = $this->addDataMarket($dataMarket);
        }
        if($addLog_flag!==false && $addMarket_flag!==false){
            return true;
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销统计数据
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
        //添加跟进记录？
        if($where['name']=='attitudenum'){
            $dayCallback = D('DataLogs')->where(array('system_user_id'=>$where['system_user_id'],'user_id'=>array('IN',$where['user_id']),'logtime'=>array('GT',$data_where['daytime'])))->count();
            if($dayCallback==0){
                $save_callback['callbacknum'] = array('exp','callbacknum+1');
                D('DataMarket')->where($data_where)->save($save_callback);
            }
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
}