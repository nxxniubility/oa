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
            $addLog_flag = $addMarket_flag = true;
            $add_arr = array();
            $userIds = array();
            foreach($userList as $k=>$v){
                if($v['status']==160){
                    continue;
                }
                $add_arr[] = array(
                    'zone_id'=>!empty($v['zone_id'])?$v['zone_id']:0,
                    'course_id'=>!empty($v['course_id'])?$v['course_id']:0,
                    'attitude_id'=>!empty($v['attitude_id'])?$v['attitude_id']:0,
                    'channel_id'=>!empty($v['channel_id'])?$v['channel_id']:0,
                    'infoquality'=>!empty($v['infoquality'])?$v['infoquality']:0,
                    'createuser_id'=>!empty($v['createuser_id'])?$v['createuser_id']:0,
                    'updateuser_id'=>!empty($v['updateuser_id'])?$v['updateuser_id']:0,
                    'system_user_id'=>!empty($v['system_user_id'])?$v['system_user_id']:0,
                    'user_id'=>$v['user_id'],
                    'operattype'=>$data['operattype'],
                    'logtime'=>$data['logtime'],
                    'operator_user_id'=>$data['operator_user_id'],
                );
                $temp_system_user_id = $v['system_user_id'];
                $temp_zone_id = $v['zone_id'];
                $userIds[] = $v['user_id'];
            }
            if(!empty($add_arr) && count($add_arr)>0){
                $addLog_flag = D('DataLogs')->addAll($add_arr);
                //添加营销统计---------------------
                $statusArr = $this->statusArr;
                $dataMarket['name'] = $statusArr[$data['operattype']];
                $dataMarket['system_user_id'] = $temp_system_user_id;
                $dataMarket['zone_id'] = $temp_zone_id;
                $dataMarket['user_id'] = $userIds;
                $addMarket_flag = $this->addDataMarket($dataMarket);
            }
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
    |  //出库量 addnum + acceptnum + directoroutnum + applynum + redeemnum
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
    public function getDataMarket($param)
    {
        //必传参数
        if(empty($param['logtime'])) return array('code'=>301,'msg'=>'请选择搜索时间');
        //必传参数
        if(empty($param['department_id']) && empty($param['role_id'])) return array('code'=>302,'msg'=>'请选择部门或者职位');
        if(!empty($param['role_id'])){
            $_role_id = explode(',',$param['role_id']);
            D('Role','Service')
        }
        //获取部门相关公式
        $_department_config = D('DataFormula')->getFind(array('department_id'=>$param['department_id']));
        //是否有配置公式
        if(empty($_department_config)) return array('code'=>201,'msg'=>'该部门未设置统计公式');
        //时间区间转化格式
        $logtime = explode('@', $param['logtime']);
        //获取数据字段
        $_data_flied = C('FIELD_STATUS.DATA_FLIED');
        //获取显示项
        $_data_show = C('FIELD_STATUS.DATA_SHOW');
        //获取关联职位
        if(!empty($param['department_id'])){
            $_where_role_id = $this->getDepartmentRole($param['department_id']);
        }else{
            $_where_role_id = $param['role_id'];
        }
        if($_department_config['about_user']=='createuser_id'){
            $_where_log['create_role_id'] = array('IN', $_where_role_id);
        }elseif($_department_config['about_user']=='updateuser_id'){
            $_where_log['update_role_id'] = array('IN', $_where_role_id);
        }elseif($_department_config['about_user']=='system_user_id'){
            $_where_log['system_role_id'] = array('IN', $_where_role_id);
        }
        //获取条件 时间区间
        $_where_log['logtime'] = array(array('EGT',strtotime($logtime[0])),array('ELT',strtotime($logtime[1])));
        //查询时间段内产生数据的员工
        $_data_user = D('DataLogs')->field($_department_config['about_user'])->where($_where_log)->group($_department_config['about_user'])->select();
        //获取部门公式列表
        $_formula_list = D('DataFormula')->getList(array('department_id'=>$param['department_id']));
        //补全天数内容
        $_start = $logtime[0];
        $_end = $logtime[1];
        $_diff = strtotime($_end) - strtotime($_start);
        $_diffDay = $_diff / (24*60*60);
        for ($i = 0; $i <= $_diffDay; $i++){
            $_new_time = (strtotime($_start) + $i * 24 * 60 * 60 );
            if(empty($_days_count[date('Y-m-d',$_new_time)])) $_days_count[date('Y-m-d',$_new_time)] = array();
            //人员数据
            foreach($_data_user as $k=>$v){
                //显示列-数据运算
                foreach($_formula_list as $v2){
                    //获取运算结果内容
                    $_data_num = $this->setAnswer($v[$_department_config['about_user']],$v2['formula'],$v2['formula_user'],$_new_time);
                    $_data_num = (!empty($_data_num))?$_data_num:0;
                    $_data_user_show[$v2['statistics_type']] = $_data_num;
                }
                //获取涉案员工
                if(empty($_user_list[$v[$_department_config['about_user']]])) {
                    $_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v[$_department_config['about_user']]));
                    $_user_list[$v[$_department_config['about_user']]] = array(
                        'system_user_id'=>$_info['data']['system_user_id'],
                        'realname'=>$_info['data']['realname'],
                        'face'=>$_info['data']['face'],
                        'role_names'=>$_info['data']['role_names']
                    );
                }
                $_statistics[$v[$_department_config['about_user']]][$_new_time] = $_data_user_show;
            }
        }
        //组合输出数组
        foreach($_statistics as $k=>$v){
            foreach($v as $k2=>$v2){
                foreach($v2 as $k3=>$v3){
                    if(empty($_statistics_name[$k3])){
                        $_statistics_name[$k3] = array('name'=>$_data_show[$k3],'show_id'=>$k3);
                    }
                    if(empty($_days_count[date('Y-m-d',$k2)][$k3])){
                        $_days_count[date('Y-m-d',$k2)][$k3] = array('name'=>$_data_show[$k3],'show_id'=>$k3,'count'=>$v3);
                    }else{
                        $_days_count[date('Y-m-d',$k2)][$k3]['count'] = $_days_count[date('Y-m-d',$k2)][$k3]['count'] + $v3;
                    }
                    if(empty($_user_list[$k]['data'][$k3])){
                        $_user_list[$k]['data'][$k3] = array('name'=>$_data_show[$k3],'show_id'=>$k3,'count'=>$v3);
                    }else{
                        $_user_list[$k]['data'][$k3]['count'] = $_user_list[$k]['data'][$k3]['count'] + $v3;
                    }
                    if(empty($_data_count[$k3])){
                        $_data_count[$k3] = array('name'=>$_data_show[$k3],'show_id'=>$k3,'count'=>$v3);
                    }else{
                        $_data_count[$k3]['count'] = $_data_count[$k3]['count'] + $v3;
                    }
                }
            }
        }
        $_put_data['user_list'] = $_user_list;
        $_put_data['days'] = $_days_count;
        $_put_data['count'] = array_values($_data_count);
        $_put_data['statistics'] = array_values($_statistics_name);
        return array('code'=>0, 'msg'=>'获取成功', 'data'=>$_put_data);
    }

    /*
     * 公式计算
     */
    protected function setAnswer($user_id,$formula,$formula_user,$logtime)
    {
        //换取运运算符号
        $_reg = "/\+|\-|\*|\/|\)\*|\)\//";
        preg_match_all($_reg, $formula,$_regs);
        $_formula_symbol = $_regs[0];
        //获取参数type
        $_formula_arr = explode(',', preg_replace($_reg,',',$formula));
        $_formula_user = explode(',', $formula_user);
        //公式ID转化真实数量
        $_operator_mun = array();
        foreach($_formula_user as $k=>$v){
            $_is_dep = explode('-', $v);
            $_where_log[$v] = $user_id;
            $_where_log['operattype'] = $_formula_arr[$k];
            $_where_log['logtime'] = array(array('EGT',$logtime),array('ELT',strtotime(date('Y-m-d',$logtime).' 23:59:59')));
            $_data_num = D('DataLogs')->where($_where_log)->count();
            //获取总数
            $_operator_mun[] = $_data_num;
        }
        //计算公式得数 先乘除
        if(!empty($_formula_symbol)){
            foreach($_formula_symbol as $k=>$v){
                if($k==0 && $v != '*' && $v != '/'){
                    $_operator_mun_start[] = $_operator_mun[0];
                }
                if($v == '*'){
                    $_operator_mun_start[] = $_formula_symbol[($k+1)] = (int) $_operator_mun[$k] * (int) $_operator_mun[($k+1)];
                }elseif($v == '/'){
                    $_operator_mun_start[] = $_formula_symbol[($k+1)] = (int) $_operator_mun[$k] / (int) $_operator_mun[($k+1)];
                }else{
                    $_formula_symbol_start[] = $v;
                    $_operator_mun_start[] = $_operator_mun[$k];
                }
            }
        }else{
            $_operator_mun_start[] = $_operator_mun[0];
        }
        //计算公式得数 按顺序运算
        if(!empty($_formula_symbol_start)){
            $_formula_answer = '';
            foreach($_formula_symbol_start as $k=>$v){
                if($k == 0){
                    $_formula_answer = (int) $_operator_mun_start[0];
                }
                if($v=='+'){
                    $_formula_answer = $_formula_answer + (int) $_operator_mun_start[($k+1)];
                }elseif($v=='-'){
                    $_formula_answer = $_formula_answer - (int) $_operator_mun_start[($k+1)];
                }elseif($v=='*'){
                    $_formula_answer = $_formula_answer * (int) $_operator_mun_start[($k+1)];
                }elseif($v=='/'){
                    $_formula_answer = $_formula_answer / (int) $_operator_mun_start[($k+1)];
                }
            }
        }else{
            $_formula_answer = $_operator_mun_start[0];
        }
        return $_formula_answer;
    }

    protected function getDepartmentRole($department_id)
    {
        //获取关联职位
        $_role_list = D('Role','Service')->getRoleList(array('department_id'=>$department_id));
        foreach($_role_list['data']['data'] as $v){
            $_role_ids[] = $v['id'];
        }
        return $_role_ids;
    }


    public function getDataMarket3($where)
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
        //补全天数内容
        if(!empty($daytime)){
            $start = $daytime[0];
            $end = $daytime[1];
            $diff = strtotime($end) - strtotime($start);
            $diffDay = $diff / (24*60*60);
            for ($i = 0; $i <= $diffDay; $i++){
                $new_time = (strtotime($start) + $i * 24 * 60 * 60 );
                if(empty($newArr['days'][date('Ymd', $new_time)])){
                    $newArr['days'][date('Ymd', $new_time)] = array(
                        'day' => date('Y-m-d', $new_time),
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
            $newArr['count']['acceptcount'] = $newArr['count']['acceptcount']+$v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum']+$v['redeemnum'];
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
            $newArr['days'][$v['daytime']]['acceptcount'] = $newArr['days'][$v['daytime']]['acceptcount'] + $v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum']+$v['redeemnum'];
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
                if($v['system_user_id']==0){
                    $newArr['systemuser'][$v['system_user_id']]['realname'] = '系统创建';
                    $newArr['systemuser'][$v['system_user_id']]['role_id'] = '';
                    $newArr['systemuser'][$v['system_user_id']]['rolename'] = '';
                    $newArr['systemuser'][$v['system_user_id']]['system_user_id'] = 0;
                }else{
                    $sys_where['system_user_id'] = $v['system_user_id'];
                    $info = D('SystemUser','Service')->getSystemUsersInfo($sys_where);
                    $info = $info['data'];
                    $newArr['systemuser'][$v['system_user_id']]['realname'] = $info['realname'];
                    $newArr['systemuser'][$v['system_user_id']]['role_id'] = $info['roles'][0]['role_id'];
                    $newArr['systemuser'][$v['system_user_id']]['rolename'] = $info['role_names'];
                    $newArr['systemuser'][$v['system_user_id']]['system_user_id'] = $v['system_user_id'];
                }
            }
            $newArr['systemuser'][$v['system_user_id']]['addcount'] = $newArr['systemuser'][$v['system_user_id']]['addcount']+$v['addnum'];
            $newArr['systemuser'][$v['system_user_id']]['acceptcount'] = $newArr['systemuser'][$v['system_user_id']]['acceptcount']+$v['addnum']+$v['acceptnum']+$v['directoroutnum']+$v['applynum']+$v['redeemnum'];
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
            'acceptcount'=>'1,2,3,4,9',
            'switchcount'=>'5,15',
            'allocationcount'=>'1,2,3,4,9',//分配量 还要减'5,15'
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

        $channel_list = D('Channel','Service')->getChannelList();
        $channel_list = $channel_list['data']['data'];
        $newArr = array();
        $channelArr = array();
        //补全空白天数内容
        if(!empty($daytime)){
            $start = $daytime[0];
            $end = $daytime[1];
            $diff = strtotime($end) - strtotime($start);
            $diffDay = $diff / (24*60*60);
            for ($i = 0; $i <= $diffDay; $i++){
                $new_time = (strtotime($start) + $i * 24 * 60 * 60 );
                if(empty($newArr['days'][date('m-d',$new_time)])){
                    $newArr['days'][date('m-d',$new_time)] = 0;
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
        $course_list = $CourseService->getCourseList();
        foreach($newArr['course_id'] as $k=>$v){
            foreach($course_list['data'] as $v2){
                if($v2['course_id'] == $k){
                    $newArr['course_id'][$v2['coursename']] = $v;
                    unset($newArr['course_id'][$k]);
                }elseif($k==0 || $k==''){
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
        if(empty($where['system_user_id'])) $where['system_user_id'] = 0;
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
        if(empty($where['standard_name'])) return array('code'=>201,'msg'=>'名称不能为空');
        if(empty($where['department_id'])) return array('code'=>202,'msg'=>'部门ID不能为空');
        if(empty($where['option_objs'])) return array('code'=>202,'msg'=>'规则内容不能为空');
        $is_department = D('MarketStandard')->getFind(array('department_id'=>$where['department_id']));
        if(!empty($is_department))return array('code'=>100,'msg'=>'该部门合格标准已存在，无法重复添加！');
        $_standard_data['standard_name'] = $where['standard_name'];
        $_standard_data['department_id'] = $where['department_id'];
        $_standard_data['standard_remark'] = $where['standard_remark'];
        $_option_objs =  (array) json_decode(htmlspecialchars_decode($where['option_objs']));
        //获取
        $rolist = D('Role')->getList(array('department_id'=>$where['department_id']));
        if(!empty($rolist)){
            foreach($rolist as $k=>$v){
                if($k==0){
                    $role_ids = $v['id'];
                }else{
                    $role_ids += ','.$v['id'];
                }
            }
            $_standard_data['role_id'] = $role_ids;
        }
        D()->startTrans();
        $redata = D('MarketStandard')->addData($_standard_data);
        if($redata['code']==0){
            foreach($_option_objs as $k=>$v){
                $v = (array) $v;
                $_info_data = array(
                    'standard_id' => $redata['data'],
                    'option_name' => $v['option_name'],
                    'option_num' => $v['option_num'],
                    'option_warn' => $v['option_warn'],
                );
                $redata_info = D('MarketStandardInfo')->addData($_info_data);
                if($redata_info['code']!=0){
                    D()->rollback();
                    return $redata_info;exit();
                }
            }
            D()->commit();
            return array('code'=>0,'msg'=>'添加成功');
        }
        D()->rollback();
        return $redata;exit();
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
        if(empty($where['standard_id'])) return array('code'=>200,'msg'=>'参数异常');
        if(empty($where['standard_name'])) return array('code'=>201,'msg'=>'名称不能为空');
        if(empty($where['department_id'])) return array('code'=>202,'msg'=>'部门ID不能为空');
        if(empty($where['option_objs'])) return array('code'=>202,'msg'=>'规则内容不能为空');
        $is_department = D('MarketStandard')->getFind(array('standard_id'=>array('NEQ',$where['standard_id']),'department_id'=>$where['department_id']));
        if(!empty($is_department))return array('code'=>100,'msg'=>'该部门合格标准已存在，无法重复添加！');
        $_standard_id = $where['standard_id'];
        $_standard_data['standard_name'] = $where['standard_name'];
        $_standard_data['department_id'] = $where['department_id'];
        $_standard_data['standard_remark'] = $where['standard_remark'];
        $_option_objs =  (array) json_decode(htmlspecialchars_decode($where['option_objs']));
        //获取
        $rolist = D('Role')->getList(array('department_id'=>$where['department_id']));
        if(!empty($rolist)){
            foreach($rolist as $k=>$v){
                if($k==0){
                    $role_ids = $v['id'];
                }else{
                    $role_ids += ','.$v['id'];
                }
            }
            $_standard_data['role_id'] = $role_ids;
        }
        D()->startTrans();
        $redata = D('MarketStandard')->editData($_standard_data,$_standard_id);
        if($redata['code']==0){
            D('MarketStandardInfo')->delData($_standard_id);
            foreach($_option_objs as $k=>$v){
                $v = (array) $v;
                $_info_data = array(
                    'standard_id' => $_standard_id,
                    'option_name' => $v['option_name'],
                    'option_num' => $v['option_num'],
                    'option_warn' => $v['option_warn'],
                );
                $redata_info = D('MarketStandardInfo')->addData($_info_data);
                if($redata_info['code']!=0){
                    D()->rollback();
                    return $redata_info;exit();
                }
            }
            D()->commit();
            return array('code'=>0,'msg'=>'添加成功');
        }
        D()->rollback();
        return $redata;exit();
    }

    /*
   |--------------------------------------------------------------------------
   | 删除合格标准
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function delStandard($where=null)
    {
        //必须参数
        if(empty($where['standard_id'])) return array('code'=>200,'msg'=>'参数异常');
        $_standard_id = $where['standard_id'];
        D()->startTrans();
        $redata = D('MarketStandard')->delData($_standard_id);
        $redata_info = D('MarketStandardInfo')->delData($_standard_id);
        if($redata!==false && $redata_info!==false){
            D()->commit();
            return array('code'=>0,'msg'=>'删除成功');
        }
        D()->rollback();
        return $redata;exit();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getStandard($where=null)
    {
        $DepartmentService = new DepartmentService();
        $result = D('MarketStandard')->getList($where);
        $statusName = $this->statusName ;
        foreach($result as $k=>$v){
            //获取合格标准详情
            $info_list = D('MarketStandardInfo')->getList(array('standard_id'=>$v['standard_id']),'option_name,option_warn,option_num');
            $arr_status = '';
            foreach($info_list as $k2=>$v2){
                $info_list[$k2]['status_name'] = $statusName[$v2['option_name']];
                if($k2==0){
                    $arr_status =$statusName[$v2['option_name']];
                }else{
                    $arr_status .= '、'.$statusName[$v2['option_name']];
                }
            }
            $result[$k]['children'][] =$info_list;
            $result[$k]['status_names'] =$arr_status;
            $department = $DepartmentService->getDepartmentInfo(array('department_id'=>$result['department_id']));
            $result[$k]['department_name'] = $department['data']['departmentname'];
        }
        return array('code'=>0,'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getStandardInfo($where=null)
    {
        $DepartmentService = new DepartmentService();
        $result = D('MarketStandard')->getFind($where);
        $statusName = $this->statusName ;
        //获取合格标准详情
        $info_list = D('MarketStandardInfo')->getList($where,'option_name,option_warn,option_num');
        $arr_status = '';
        foreach($info_list as $k2=>$v2){
            $info_list[$k2]['status_name'] = $statusName[$v2['option_name']];
            if($k2==0){
                $arr_status =$statusName[$v2['option_name']];
            }else{
                $arr_status .= '、'.$statusName[$v2['option_name']];
            }
        }
        $result['children'] =$info_list;
        $result['status_names'] =$arr_status;
        $department = $DepartmentService->getDepartmentInfo(array('department_id'=>$result['department_id']));
        $result['department_name'] = $department['data']['departmentname'];
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
        $zoneIds = D('Zone','Service')->getZoneIds(array('zone_id'=>$zone_id));
        $zoneIdArr = array();
        foreach($zoneIds['data'] as $k=>$v){
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

    /**
     * 添加部门算法公式项
     * @author nxx
     */
    public function addDepartmentFormula($data)
    {
        foreach ($data['object'] as $key => $value) {
            $value['department_id'] = $data['department_id'];
            $value['about_user'] = $data['about_user'];
            $addData[$key] = $value;
        }
        $result = D('DataFormula')->getFind(array('department_id'=>$data['department_id']));
        if ($result) {
            return array('code'=>301,'msg'=>'已添加过该职位,如需重新添加请先确认后删除');
        }
        $result = M("data_formula")->addAll($addData);
        if ($result == false) {
            return array('code'=>201,'msg'=>'添加失败');
        }
        return array('code'=>0,'msg'=>'添加成功');
    }
}