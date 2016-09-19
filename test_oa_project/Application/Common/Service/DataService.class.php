<?php
/*
* 数据服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class DataService extends BaseService
{
    //初始化
    protected $statusArr;
    public function _initialize() {
        parent::_initialize();
        //统计数据状态
        $this->statusArr = array(
            '1'=>'addnum',   //新增量
            '2'=>'acceptnum',   //系统出库量
            '3'=>'directoroutnum',   //出库量
            '4'=>'applynum',   //申请转入量
            '5'=>'switchnum',   //转出量
            '15'=>'switchmanagenum',   //主管转出量
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
    | 获取数据记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataLogs($where)
    {
        return D('DataLogs')->where($where)->select();
    }

    /*
   |--------------------------------------------------------------------------
   | 添加统计记录数据
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function addDataLogs($data,$dataList=null,$dataType=null)
    {
        if(empty($data['logtime']))  $data['logtime'] = time();
        $userList = D('User')->field('user_id,status,createuser_id,updateuser_id,system_user_id,zone_id,attitude_id,channel_id,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(!empty($userList)){
            //获取记录数据集合 -> 批量添加
            $add_arr = array();
            $userIds = array();
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
                $userIds[] = $v['user_id'];

            }
            $addLog_flag = D('DataLogs')->addAll($add_arr);
            //添加营销统计---------------------
            $dataMarket['system_user_id'] = $temp_system_user_id;
            $statusArr = $this->statusArr;
            $dataMarket['name'] = $statusArr[$data['operattype']];
            $dataMarket['user_id'] = $userIds;
            $addMarket_flag = $this->addDataMarket($dataMarket);
        }
        if($addLog_flag!==false && $addMarket_flag!==false){
            return array('code'=>0,'msg'=>'数据添加成功');
        }
        return array('code'=>1,'msg'=>'数据添加失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarket($where)
    {
        $result = D('DataMarket')->where($where)->select();
        //新增量 addnum
        //出库量 addnum + acceptnum + directoroutnum + applynum
        //转出量 switchnum + switchmanagenum
        //放弃量 restartnum
        //系统回收量 recyclenum
        //赎回量 redeemnum
        //已回访量 callbacknum
        //跟进次数 attitudenum
        //分配量 出库量 - 转出量
        //到访量 visitnum
        //订单量 ordernum
        //退款量 refundnum
        //到访率
        //面转率
        //退单率
        //总转率
        $newArr = array();
        $SystemUserService = new SystemUserService();
        foreach($result as $k=>$v){
            //总数
            $newArr['count']['addcount'] = $newArr['count']['addcount']+$v['addnum'];
            $newArr['count']['acceptcount'] = $newArr['count']['acceptcount']+$v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum'];
            $newArr['count']['switchcount'] = $newArr['count']['switchcount']+$v['switchnum']+$v['switchmanagenum'];
            $newArr['count']['restartcount'] = $newArr['count']['restartcount']+$v['restartnum'];
            $newArr['count']['recyclecount'] = $newArr['count']['recyclecount']+$v['recyclenum'];
            $newArr['count']['redeemcount'] = $newArr['count']['redeemcount']+$v['redeemnum'];
            $newArr['count']['callbackcount'] = $newArr['count']['callbackcount']+$v['callbacknum'];
            $newArr['count']['attitudecount'] = $newArr['count']['attitudecount']+$v['attitudenum'];
            $newArr['count']['allocationcount'] = $newArr['count']['allocationcount']+$newArr['count']['acceptcount']-$newArr['count']['switchcount'];
            $newArr['count']['visitcount'] = $newArr['count']['visitcount']+$v['visitnum'];
            $newArr['count']['ordercount'] = $newArr['count']['ordercount']+$v['ordernum'];
            $newArr['count']['refundcount'] = $newArr['count']['refundcount']+$v['refundnum'];
            //天数单位
            $newArr['days'][$v['daytime']]['day'] = mb_substr($v['daytime'],0,4).'-'.mb_substr($v['daytime'],4,2).'-'.mb_substr($v['daytime'],6,2);
            $newArr['days'][$v['daytime']]['addcount'] = $newArr['days'][$v['daytime']]['addcount'] + $v['addnum'];
            $newArr['days'][$v['daytime']]['acceptcount'] = $newArr['days'][$v['daytime']]['acceptcount'] + $v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum'];
            $newArr['days'][$v['daytime']]['switchcount'] = $newArr['days'][$v['daytime']]['switchcount'] + $v['switchnum']+$v['switchmanagenum'];
            $newArr['days'][$v['daytime']]['restartcount'] = $newArr['days'][$v['daytime']]['restartcount'] + $v['restartnum'];
            $newArr['days'][$v['daytime']]['recyclecount'] = $newArr['days'][$v['daytime']]['recyclecount'] + $v['recyclenum'];
            $newArr['days'][$v['daytime']]['redeemcount'] = $newArr['days'][$v['daytime']]['redeemcount'] + $v['redeemnum'];
            $newArr['days'][$v['daytime']]['callbackcount'] = $newArr['days'][$v['daytime']]['callbackcount'] + $v['callbacknum'];
            $newArr['days'][$v['daytime']]['attitudecount'] = $newArr['days'][$v['daytime']]['attitudecount'] + $v['attitudenum'];
            $newArr['days'][$v['daytime']]['allocationcount'] = $newArr['days'][$v['daytime']]['allocationcount'] + $v['acceptcount']-$v['switchcount'];
            $newArr['days'][$v['daytime']]['visitcount'] = $newArr['days'][$v['daytime']]['visitcount'] + $v['visitnum'];
            $newArr['days'][$v['daytime']]['ordercount'] = $newArr['days'][$v['daytime']]['ordercount'] + $v['ordernum'];
            $newArr['days'][$v['daytime']]['refundcount'] = $newArr['days'][$v['daytime']]['refundcount'] + $v['refundnum'];
            //员工
            if(empty($newArr['systemuser'][$v['system_user_id']]['system_user_id'])){
                $where['system_user_id'] = $v['system_user_id'];
                $info = $SystemUserService->getListCache($where);
                $info = $info['data'];
                $newArr['systemuser'][$v['system_user_id']]['realname'] = $info['realname'];
                $newArr['systemuser'][$v['system_user_id']]['rolename'] = $info['rolename'];
                $newArr['systemuser'][$v['system_user_id']]['system_user_id'] = $v['system_user_id'];
            }
            $newArr['systemuser'][$v['system_user_id']]['addcount'] = $newArr['systemuser'][$v['system_user_id']]['addcount']+$v['addnum'];
            $newArr['systemuser'][$v['system_user_id']]['acceptcount'] = $newArr['systemuser'][$v['system_user_id']]['acceptcount']+$v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum'];
            $newArr['systemuser'][$v['system_user_id']]['switchcount'] = $newArr['systemuser'][$v['system_user_id']]['switchcount']+$v['switchnum']+$v['switchmanagenum'];
            $newArr['systemuser'][$v['system_user_id']]['restartcount'] = $newArr['systemuser'][$v['system_user_id']]['restartcount']+$v['restartnum'];
            $newArr['systemuser'][$v['system_user_id']]['recyclecount'] = $newArr['systemuser'][$v['system_user_id']]['recyclecount']+$v['recyclenum'];
            $newArr['systemuser'][$v['system_user_id']]['redeemcount'] = $newArr['systemuser'][$v['system_user_id']]['redeemcount']+$v['redeemnum'];
            $newArr['systemuser'][$v['system_user_id']]['callbackcount'] = $newArr['systemuser'][$v['system_user_id']]['callbackcount']+$v['callbacknum'];
            $newArr['systemuser'][$v['system_user_id']]['attitudecount'] = $newArr['systemuser'][$v['system_user_id']]['attitudecount']+$v['attitudenum'];
            $newArr['systemuser'][$v['system_user_id']]['allocationcount'] = $newArr['systemuser'][$v['system_user_id']]['allocationcount']+$newArr['systemuser'][$v['system_user_id']]['acceptcount']-$newArr['systemuser'][$v['system_user_id']]['switchcount'];
            $newArr['systemuser'][$v['system_user_id']]['visitcount'] = $newArr['systemuser'][$v['system_user_id']]['visitcount']+$v['visitnum'];
            $newArr['systemuser'][$v['system_user_id']]['ordercount'] = $newArr['systemuser'][$v['system_user_id']]['ordercount']+$v['ordernum'];
            $newArr['systemuser'][$v['system_user_id']]['refundcount'] = $newArr['systemuser'][$v['system_user_id']]['refundcount']+$v['refundnum'];
        }
        return array('code'=>0,'data'=>$newArr);
    }
    /*
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarketInfo($where)
    {
        $arr = array(
            'addcount'=>'1',
            'acceptcount'=>'1,2,3,4',
            'switchcount'=>'5,15',
            'restartcount'=>'6',
            'recyclecount'=>'7',
            'redeemcount'=>'9',
            'callbackcount'=>'10',
            'attitudecount'=>'11',
            'visitcount'=>'12',
            'ordercount'=>'13',
            'refundcount'=>'14',
        );
        $new_where['operattype'] = array('IN',$arr[$where['type']]);
        $new_where['logtime'] = $where['daytime'];
        $redata = D('DataLogs')->where($new_where)->select();
        return array('code'=>0,'data'=>$redata);
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
        $data_where['daytime'] = date('Ymd');
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
        $field = $where['name'];
        $exp = !empty($where['exp'])?$where['exp']:'+';   // + -
        $num = count($where['user_id']);
        $data_save[$field] = array('exp', $field.$exp.$num);
        $flag_save = D('DataMarket')->where($data_where)->save($data_save);
        if($flag_save!==false){
            return array('code'=>0,'msg'=>'数据添加成功');
        }
        return array('code'=>1,'msg'=>'数据添加失败');
    }
}