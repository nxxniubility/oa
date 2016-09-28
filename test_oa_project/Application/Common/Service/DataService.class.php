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
    protected $statusArr,$statusName;
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
            '10'=>'callbacknum',  //已回访量
            '11'=>'attitudenum',  //跟进次数
            '12'=>'visitnum',  //到访量
            '13'=>'ordernum',  //订单量
            '14'=>'refundnum',  //退款量
        );
        $this->statusName = array(
            'addcount'=>'新增量',
            'acceptcount'=>'出库量',
            'switchcount'=>'转出量',
            'restartcount'=>'放弃量',
            'recyclecount'=>'系统回收量',
            'callbackcount'=>'已回访量',
            'attitudecount'=>'跟进次数',
            'allocationcount'=>'分配量',
            'visitcount'=>'到访量',
            'ordercount'=>'订单量',
            'refundcount'=>'退款量',
            'visitratio'=>'到访率',
            'conversionratio'=>'成交率',
            'chargebackratio'=>'退款率',
            'totalratio'=>'总转率',
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
        $userList = D('User')->field('user_id,status,createuser_id,updateuser_id,system_user_id,zone_id,course_id,attitude_id,channel_id,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(!empty($userList)){
            //获取记录数据集合 -> 批量添加
            $add_arr = array();
            $userIds = array();
            foreach($userList as $k=>$v){
                $add_arr[] = array(
                    'zone_id'=>$v['zone_id'],
                    'course_id'=>$v['course_id'],
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
                $temp_zone_id = $v['zone_id'];
                $userIds[] = $v['user_id'];

            }
            $addLog_flag = D('DataLogs')->addAll($add_arr);
            //添加营销统计---------------------
            $statusArr = $this->statusArr;
            $dataMarket['name'] = $statusArr[$data['operattype']];
            $dataMarket['system_user_id'] = $temp_system_user_id;
            $dataMarket['zone_id'] = $temp_zone_id;
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
    |  //新增量 addnum
    |  //出库量 addnum + acceptnum + directoroutnum + applynum
    |  //转出量 switchnum + switchmanagenum
    |  //放弃量 restartnum
    |  //系统回收量 recyclenum
    |  //赎回量 redeemnum
    |  //已回访量 callbacknum
    |  //跟进次数 attitudenum
    |  //分配量 出库量 - 转出量
    |  //到访量 visitnum
    |  //订单量 ordernum
    |  //退款量 refundnum
    |  //到访率  到访量/出库量
    |  //面转率 ordernum/visitnum
    |  //退单率 refundnum/ordernum
    |  //总转率 （ordernum-refundnum）/出库量
    | @author zgt
    */
    public function getDataMarket($where)
    {
        //时间格式转换
        if(!empty($where['daytime'])){
            $daytime = explode(',', $where['daytime']);
            if(count($daytime)>1){
                $where['daytime'] = array(array('EGT',date('Ymd', strtotime($daytime[0]))),array('ELT',date('Ymd', strtotime($daytime[1]))));
            }else{
                $where['daytime'] = date('Ymd', strtotime($daytime[0]));
            }
        }      
        //获取区域子集
        if(!empty($where['zone_id'])){
            $zoneIds = $this->getZoneIds($where['zone_id']);
            $_where['zone_id'] = array('IN',$zoneIds);
        }
        //获取区域子集
        if(!empty($where['role_id'])){
            $systemIds = $this->getRoleIds($where['role_id']);
            $_where['system_user_id'] = array('IN',$systemIds);
        }elseif(!empty($where['system_user_id'])){
            $_where['system_user_id'] = $where['system_user_id'];
        }
        $_where['daytime'] = $where['daytime'];
        $result = D('DataMarket')->where($_where)->select();
        $newArr = array();
        $SystemUserService = new SystemUserService();
        //补全天数内容
        if(!empty($daytime)){
            for($i = date('Ymd', strtotime($daytime[0]));$i<date('Ymd', strtotime($daytime[1]));$i++){
                if(empty($newArr['days'][$i])){
                    $newArr['days'][$i] = array(
                        'day' => mb_substr($i,0,4).'-'.mb_substr($i,4,2).'-'.mb_substr($i,6,2),
                        'addcount'=>0,
                        'acceptcount'=>0,
                        'switchcount'=>0,
                        'restartcount'=>0,
                        'recyclecount'=>0,
                        'redeemcount'=>0,
                        'callbackcount'=>0,
                        'attitudecount'=>0,
                        'allocationcount'=>0,
                        'visitcount'=>0,
                        'ordercount'=>0,
                        'refundcount'=>0,
                        'visitratio'=>0,
                        'conversionratio'=>0,
                        'chargebackratio'=>0,
                        'totalratio'=>0,
                    );
                }
            }
        }
        foreach($result as $k=>$v){
            $_count = count($result);
            //总数
            $newArr['count']['addcount'] = $newArr['count']['addcount']+$v['addnum'];
            $newArr['count']['acceptcount'] = $newArr['count']['acceptcount']+$v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum'];
            $newArr['count']['switchcount'] = $newArr['count']['switchcount']+$v['switchnum']+$v['switchmanagenum'];
            $newArr['count']['restartcount'] = $newArr['count']['restartcount']+$v['restartnum'];
            $newArr['count']['recyclecount'] = $newArr['count']['recyclecount']+$v['recyclenum'];
            $newArr['count']['redeemcount'] = $newArr['count']['redeemcount']+$v['redeemnum'];
            $newArr['count']['callbackcount'] = $newArr['count']['callbackcount']+$v['callbacknum'];
            $newArr['count']['attitudecount'] = $newArr['count']['attitudecount']+$v['attitudenum'];
            $newArr['count']['allocationcount'] = $newArr['count']['acceptcount']-$newArr['count']['switchcount'];           
            $newArr['count']['visitcount'] = $newArr['count']['visitcount']+$v['visitnum'];
            $newArr['count']['ordercount'] = $newArr['count']['ordercount']+$v['ordernum'];
            $newArr['count']['refundcount'] = $newArr['count']['refundcount']+$v['refundnum'];
            if(($_count-1)==$k){
                $newArr['count']['visitratio'] = round($newArr['count']['visitcount']/$newArr['count']['acceptcount'],4)*100;
                $newArr['count']['conversionratio'] = round($newArr['count']['ordercount']/$newArr['count']['visitcount'],4)*100;
                $newArr['count']['chargebackratio'] = round($newArr['count']['refundcount']/$newArr['count']['ordercount'],4)*100;
                $newArr['count']['totalratio'] = round(($newArr['count']['ordercount']-$newArr['count']['refundcount'])/$newArr['count']['acceptcount'],4)*100;
            }
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
            $newArr['days'][$v['daytime']]['allocationcount'] = $newArr['days'][$v['daytime']]['acceptcount'] - $newArr['days'][$v['daytime']]['switchcount'];
            $newArr['days'][$v['daytime']]['visitcount'] = $newArr['days'][$v['daytime']]['visitcount'] + $v['visitnum'];
            $newArr['days'][$v['daytime']]['ordercount'] = $newArr['days'][$v['daytime']]['ordercount'] + $v['ordernum'];
            $newArr['days'][$v['daytime']]['refundcount'] = $newArr['days'][$v['daytime']]['refundcount'] + $v['refundnum'];
            //-率
            $newArr['days'][$v['daytime']]['visitratio'] = round($newArr['days'][$v['daytime']]['visitcount']/$newArr['days'][$v['daytime']]['acceptcount'],4)*100;
            $newArr['days'][$v['daytime']]['conversionratio'] = round($newArr['days'][$v['daytime']]['ordercount']/$newArr['days'][$v['daytime']]['visitcount'],4)*100;
            $newArr['days'][$v['daytime']]['chargebackratio'] = round($newArr['days'][$v['daytime']]['refundcount']/$newArr['days'][$v['daytime']]['ordercount'],4)*100;
            $newArr['days'][$v['daytime']]['totalratio'] = round(($newArr['days'][$v['daytime']]['ordercount']-$newArr['days'][$v['daytime']]['refundcount'])/$newArr['days'][$v['daytime']]['acceptcount'],4)*100;

            //员工
            if(empty($newArr['systemuser'][$v['system_user_id']]['system_user_id'])){
                $sys_where['system_user_id'] = $v['system_user_id'];
                $sys_where['usertype'] = 1;
                $info = $SystemUserService->getListCache($sys_where);
                $info = $info['data'];
                $newArr['systemuser'][$v['system_user_id']]['realname'] = $info['realname'];
                $newArr['systemuser'][$v['system_user_id']]['role_id'] = $info['roles'][0]['role_id'];
                $newArr['systemuser'][$v['system_user_id']]['rolename'] = $info['role_names'];
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
            $newArr['systemuser'][$v['system_user_id']]['allocationcount'] = $newArr['systemuser'][$v['system_user_id']]['acceptcount']-$newArr['systemuser'][$v['system_user_id']]['switchcount'];
            $newArr['systemuser'][$v['system_user_id']]['visitcount'] = $newArr['systemuser'][$v['system_user_id']]['visitcount']+$v['visitnum'];
            $newArr['systemuser'][$v['system_user_id']]['ordercount'] = $newArr['systemuser'][$v['system_user_id']]['ordercount']+$v['ordernum'];
            $newArr['systemuser'][$v['system_user_id']]['refundcount'] = $newArr['systemuser'][$v['system_user_id']]['refundcount']+$v['refundnum'];
            //-率
            $newArr['systemuser'][$v['system_user_id']]['visitratio'] = round($newArr['systemuser'][$v['system_user_id']]['visitcount']/$newArr['systemuser'][$v['system_user_id']]['acceptcount'],4)*100;
            $newArr['systemuser'][$v['system_user_id']]['conversionratio'] = round($newArr['systemuser'][$v['system_user_id']]['ordercount']/$newArr['systemuser'][$v['system_user_id']]['visitcount'],4)*100;
            $newArr['systemuser'][$v['system_user_id']]['chargebackratio'] = round($newArr['systemuser'][$v['system_user_id']]['refundcount']/$newArr['systemuser'][$v['system_user_id']]['ordercount'],4)*100;
            $newArr['systemuser'][$v['system_user_id']]['totalratio'] = round(($newArr['systemuser'][$v['system_user_id']]['ordercount']-$newArr['systemuser'][$v['system_user_id']]['refundcount'])/$newArr['systemuser'][$v['system_user_id']]['acceptcount'],4)*100;
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
            'allocationcount'=>'1,2,3,4',//分配量 还要减'5,15'
            'restartcount'=>'6',
            'recyclecount'=>'7',
            'redeemcount'=>'9',
            'callbackcount'=>'10',
            'attitudecount'=>'11',
            'visitcount'=>'12',
            'ordercount'=>'13',
            'refundcount'=>'14',
        );
        //时间格式转换
        if(!empty($where['daytime'])){
            $daytime = explode(',', $where['daytime']);
            if(count($daytime)>1){
                $where['daytime'] = array(array('EGT',strtotime($daytime[0])),array('ELT',strtotime($daytime[1].'2359')));
            }else{
                $where['daytime'] = $daytime[0];
            }
        }
        //获取区域子集
        if(!empty($where['zone_id'])){
            $zoneIds = $this->getZoneIds($where['zone_id']);
            $_where['zone_id'] = array('IN',$zoneIds);
        }
        //获取职位员工
        if(!empty($where['role_id'])){
            $systemIds = $this->getRoleIds($where['role_id']);
            $_where['system_user_id'] = array('IN',$systemIds);
        }elseif(!empty($where['system_user_id'])){
            $_where['system_user_id'] = $where['system_user_id'];
        }
        $_where['operattype'] = array('IN',$arr[$where['type']]);
        $_where['logtime'] = $where['daytime'];
        $redata = D('DataLogs')->where($_where)->order('logtime asc')->select();
       
        $ChannelService = new ChannelService();
        $channel_list = $ChannelService->getAllChannel();
        $channel_list = $channel_list['data']['data'];
        $newArr = array();
        $channelArr = array();
        //补全空白天数内容
        if(!empty($daytime)){
            for($i = date('Ymd', strtotime($daytime[0]));$i<=date('Ymd', strtotime($daytime[1]));$i++){
                if(empty($newArr['days'][date('m-d',strtotime($i))])){
                    $newArr['days'][date('m-d',strtotime($i))] = 0;
                }
            }
        }
        $eArr = array('1'=>'A','2'=>'B','3'=>'C','4'=>'D');
        foreach($redata as $k=>$v){
            $channelArr[$v['channel_id']] = $channelArr[$v['channel_id']]+1;
            $newArr['days'][date('m-d',$v['logtime'])] = $newArr['days'][date('m-d',$v['logtime'])]+1;
            $newArr['infoquality'][$eArr[$v['infoquality']]] = $newArr['infoquality'][$eArr[$v['infoquality']]]+1;
            $newArr['course_id'][$v['course_id']] = $newArr['course_id'][$v['course_id']]+1;
        }
        $CourseService = new CourseService();
        $course_list = $CourseService->getList();
        foreach($newArr['course_id'] as $k=>$v){
            foreach($course_list['data'] as $v2){
                if($v2['course_id'] == $k){
                    $newArr['course_id'][$v2['coursename']] = $v;
                    unset($newArr['course_id'][$k]);
                }elseif($k==0){
                    $newArr['course_id']['无'] = $v;
                    unset($newArr['course_id'][$k]);
                }
            }
        }
        $_temp_pchannel = array();
        $_temp_channel = array();
        foreach($channel_list as $v){
            foreach($v['children'] as $v2){
                if( !empty($channelArr[$v2['channel_id']]) ){
                    $_temp_pchannel[$v['channelname']] = $_temp_pchannel[$v['channelname']]+$channelArr[$v2['channel_id']];
                    $_temp_channel[$v2['channelname'].'('.$v2['channel_id'].')'] = array('count'=>$channelArr[$v2['channel_id']],'name'=>$v2['channelname'],'pname'=>$v['channelname']);
                }
            }
        }
        $newArr['channel']['broad'] = $_temp_pchannel;
        $newArr['channel']['list'] = $_temp_channel;
        return array('code'=>0,'data'=>$newArr);
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
        $_time = time();
        //$where['num'] =  +1/-1
        $data_where['daytime'] = date('Ymd');
        $data_where['zone_id'] = $where['zone_id'];
        $data_where['system_user_id'] = $where['system_user_id'];
        $systemdata = D('DataMarket')->where($data_where)->find();
        if(empty($systemdata)){
            D('DataMarket')->add($data_where);
        }
        //添加跟进记录？
        if($where['name']=='attitudenum'){
            $dayCallback = D('DataLogs')->where(array('operattype'=>11,'system_user_id'=>$where['system_user_id'],'zone_id'=>$data_where['zone_id'],'user_id'=>array('IN',$where['user_id']),'logtime'=>array('GT',strtotime(date('Y-m-d')))))->count();
            if($dayCallback==1){
                //操作添加数据记录
                $dataLog['operattype'] = 10;
                $dataLog['operator_user_id'] = $where['system_user_id'];
                $dataLog['user_id'] = $where['user_id'];
                $dataLog['logtime'] = $_time;
                $this->addDataLogs($dataLog);
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

    /*
    |--------------------------------------------------------------------------
    | 添加合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addStandard($where)
    {
        //必须参数
        if(empty($where['standard_name'])) return array('code'=>201,'msg'=>'参数异常');
        if(empty($where['standard_remark'])) return array('code'=>202,'msg'=>'参数异常');
        if(empty($where['department_id'])) return array('code'=>202,'msg'=>'参数异常');

    }

    /*
    |--------------------------------------------------------------------------
    | 修改合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editStandard($where)
    {
        //必须参数
        if(empty($where['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getStandard()
    {
        $result = D('MarketStandard')->getList();
        if (!$result) {
            return array('code'=>1,'data'=>'','msg'=>'没有合格标准');
        }
        return array('code'=>0,'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getStandardInfos($where)
    {
        $result = D('MarketStandardInfo')->where($where)->select();
        if (!$result) {
            return array('code'=>1,'data'=>'','msg'=>'没有合格标准');
        }
        return array('code'=>0,'data'=>$result);
    }




    /**
     * 区域ID 获取子集包括自己的集合
     * @author zgt
     */
    protected function getZoneIds($zone_id)
    {
        $zoneIds = D('Zone')->getZoneIds($zone_id);
        $zoneIdArr = array();
        foreach($zoneIds as $k=>$v){
            $zoneIdArr[] = $v['zone_id'];
        }
        return $zoneIdArr;
    }

    /**
     * 职位ID  获取对应人员ID
     * @author zgt
     */
    protected function getRoleIds($role_id)
    {
        $reList = D('RoleUser')
            ->field('user_id')
            ->group("user_id")->Distinct(true)
            ->where(array('role_id'=>array('IN',$role_id)))
            ->select();
        $systemUserArr = array();
        foreach($reList as $v){
            $systemUserArr[] = $v['user_id'];
        }
        return $systemUserArr;
    }
}