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
        if(empty($param['department_id']) && empty($param['role_id']) && empty($param['system_user_id']) ) return array('code'=>302,'msg'=>'请选择部门或者职位');
        if(!empty($param['role_id'])){
            $_role_id = explode(',',$param['role_id']);
            $_role_info = D('Role','Service')->getRoleInfo(array('role_id'=>$_role_id[0]));
            $_department_id = $_role_info['data']['department_id'];
        }else{
            $_department_id = $param['department_id'];
        }
        //获取部门相关公式
        $_department_config = D('DataFormula')->getFind(array('department_id'=>$_department_id));
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
        //获取关联地区
        if(!empty($param['zone_id'])){
            $_zone_arr = D('Zone','Service')->getZoneIds(array('zone_id'=>$param['zone_id']));
            if(!empty($_zone_arr['data'])){
                foreach($_zone_arr['data'] as $k=>$v){
                    $_zone_ids[] = $v['zone_id'];
                }
            }
            $_where_log['zone_id'] = array('IN', $_zone_ids);
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
        if(empty($param['system_user_id'])){
            $_data_user = D('DataLogs')->field($_department_config['about_user'])->where($_where_log)->group($_department_config['about_user'])->select();
        }else{
            $_data_user[][$_department_config['about_user']] = $param['system_user_id'];
        }
        if(empty($_data_user)) return array('code'=>0, 'msg'=>'找不到统计数据');
        //获取部门公式列表
        $_formula_list = D('DataFormula')->getList(array('department_id'=>$_department_id));
        //补全天数内容
        $_start = $logtime[0];
        $_end = $logtime[1];
        $_diff = strtotime($_end) - strtotime($_start);
        $_diffDay = $_diff / (24*60*60);
        $_diffDay = (int)$_diffDay;
        for ($i = 0; $i <= $_diffDay; $i++){
            if($i==$_diffDay){
                $_new_time = strtotime($_end);
            }else{
                $_new_time = (strtotime($_start) + $i * 24 * 60 * 60 );
            }
            if(empty($_days_count[date('Y-m-d',$_new_time)])) $_days_count[date('Y-m-d',$_new_time)] = array();
            //人员数据
            foreach($_data_user as $k=>$v){
                //显示列-数据运算
                foreach($_formula_list as $v2){
                    //获取运算结果内容
                    $_data_num = $this->setAnswer($v[$_department_config['about_user']],$v2,$_new_time,$param['role_id'],$param['zone_id']);
                    $_data_num = (!empty($_data_num))?$_data_num:0;
                    $_data_user_show[$v2['statistics_type']] = $_data_num;
                }
                //获取涉案员工
                if(empty($_user_list[$v[$_department_config['about_user']]])) {
                    if($v[$_department_config['about_user']]==0){
                        $_user_list[$v[$_department_config['about_user']]] = array(
                            'system_user_id'=>0,
                            'realname'=>'系统所属',
                            'face'=>'',
                            'role_names'=>''
                        );
                    }else{
                        $_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v[$_department_config['about_user']]));
                        $_user_list[$v[$_department_config['about_user']]] = array(
                            'system_user_id'=>$_info['data']['system_user_id'],
                            'realname'=>$_info['data']['realname'],
                            'face'=>$_info['data']['face'],
                            'role_names'=>$_info['data']['role_names'],
                            'role_id'=>$_info['data']['role_id']
                        );
                    }
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
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarketInfo($param)
    {
        if(empty($param['type'])) return array('code'=>300,'msg'=>'参数异常');
        if(empty($param['logtime'])) return array('code'=>301,'msg'=>'请选择搜索时间');
        if(empty($param['role_id'])) return array('code'=>302,'msg'=>'请选择相关职位');
        //获取职位部门
        if(!empty($param['role_id'])){
            $_role_id = explode(',',$param['role_id']);
            $_role_info = D('Role','Service')->getRoleInfo(array('role_id'=>$_role_id[0]));
            $_department_id = $_role_info['data']['department_id'];
        }else{
            $_department_id = $param['department_id'];
        }
        //获取部门相关公式
        $_department_config = D('DataFormula')->getFind(array('statistics_type'=>$param['type'],'department_id'=>$_department_id));
        //是否有配置公式
        if(empty($_department_config)) return array('code'=>201,'msg'=>'该部门未设置统计公式');
        //获取关联地区
        if(!empty($param['zone_id'])){
            $_zone_arr = D('Zone','Service')->getZoneIds(array('zone_id'=>$param['zone_id']));
            if(!empty($_zone_arr['data'])){
                foreach($_zone_arr['data'] as $k=>$v){
                    $_zone_ids[] = $v['zone_id'];
                }
            }
            $_where_log['zone_id'] = array('IN', $_zone_ids);
        }
        if($_department_config['about_user']=='createuser_id'){
            $_where_log['create_role_id'] = array('IN', $param['role_id']);
        }elseif($_department_config['about_user']=='updateuser_id'){
            $_where_log['update_role_id'] = array('IN', $param['role_id']);
        }elseif($_department_config['about_user']=='system_user_id'){
            $_where_log['system_role_id'] = array('IN', $param['role_id']);
        }
        //时间区间转化格式
        $_logtime = explode('@', $param['logtime']);
        //获取条件 时间区间
        $_where_log['logtime'] = array(array('EGT',strtotime($_logtime[0])),array('ELT',strtotime($_logtime[1])));
        //查询时间段内产生数据的员工
        if(empty($param['system_user_id'])){
            $_data_user = D('DataLogs')->field($_department_config['about_user'])->where($_where_log)->group($_department_config['about_user'])->select();
            foreach($_data_user as $k=>$v){
                $user_ids[] = $v[$_department_config['about_user']];
            }
        }else{
            $user_ids[] = $param['system_user_id'];
        }
        $_put_data = array();
        //补全空白天数内容
        if(!empty($_logtime)){
            $_start = $_logtime[0];
            $_end = $_logtime[1];
            $_diff = strtotime($_end) - strtotime($_start);
            $_diffDay = $_diff / (24*60*60);
            for ($i = 0; $i <= $_diffDay; $i++){
                $_new_time = (strtotime($_start) + $i * 24 * 60 * 60 );
                //公式运算结果
                $_data_num = $this->setAnswer($user_ids,$_department_config,$_new_time,$param['role_id'],$param['zone_id']);
                $_put_data['days'][date('m-d',$_new_time)] = $_data_num;
            }
        }
        //公式运算结果
        $_put_data_two = $this->setAnswerTwo($user_ids,$_department_config,$_logtime,$param['role_id'],$param['zone_id']);
        $_put_data = array_merge($_put_data,$_put_data_two);
        return array('code'=>0,'data'=>$_put_data);
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


    /*
     * 公式计算
     */
    protected function setAnswer($user_id,$department_config,$logtime,$role_id=null,$zone_id=null)
    {
        //获取关联地区
        if(!empty($zone_id)){
            $_zone_arr = D('Zone','Service')->getZoneIds(array('zone_id'=>$zone_id));
            if(!empty($_zone_arr['data'])){
                foreach($_zone_arr['data'] as $k=>$v){
                    $_zone_ids[] = $v['zone_id'];
                }
            }
            $_where_log['zone_id'] = array('IN', $_zone_ids);
        }
        if(!empty($role_id)){
            if($department_config['about_user']=='createuser_id'){
                $_where_log['create_role_id'] = array('IN', $role_id);
            }elseif($department_config['about_user']=='updateuser_id'){
                $_where_log['update_role_id'] = array('IN', $role_id);
            }elseif($department_config['about_user']=='system_user_id'){
                $_where_log['system_role_id'] = array('IN', $role_id);
            }
        }
        //换取运运算符号
        $_reg = "/\+|\-|\*|\/|\)\*|\)\//";
        preg_match_all($_reg, $department_config['formula'],$_regs);
        $_formula_symbol = $_regs[0];
        //获取参数type
        $_formula_arr = explode(',', preg_replace($_reg,',',$department_config['formula']));
        $_formula_user = explode(',', $department_config['formula_user']);
        //获取时间
        $_logtime_start = strtotime(date('Y-m-d',$logtime));
        if(date('Y-m-d', $logtime).'0:0:0' == date('Y-m-d H:i:s', $logtime)){
            $_logtime_end = strtotime(date('Y-m-d',$logtime).'23:59:59');
        }else{
            $_logtime_end = $logtime;
        }
        //公式ID转化真实数量
        $_operator_mun = array();
        foreach($_formula_user as $k=>$v){
            $_is_dep = explode('-', $v);
            $_where_log[$v] = array('IN',$user_id);
            $_where_log['operattype'] = $_formula_arr[$k];
            $_where_log['logtime'] = array(array('EGT',$_logtime_start),array('ELT',$_logtime_end));
            $_data_num = D('DataLogs')->where($_where_log)->count();
            //获取总数
            $_operator_mun[] = $_data_num;
        }
        //计算公式得数 先乘除
        if(!empty($_formula_symbol)){
            foreach($_formula_symbol as $k=>$v){
                if($v == '*'){
                    $_operator_mun_start[] = $_formula_symbol[($k+1)] = (int) $_operator_mun[$k] * (int) $_operator_mun[($k+1)];
                }elseif($v == '/'){
                    $_operator_mun_start[] = $_formula_symbol[($k+1)] = (int) $_operator_mun[$k] / (int) $_operator_mun[($k+1)];
                }else{
                    $_formula_symbol_start[] = $v;
                    $_operator_mun_start[] = $_operator_mun[$k];
                    $_operator_mun_start[] = $_operator_mun[$k+1];
                }
            }
        }else{
            $_operator_mun_start[] = $_operator_mun[0];
            $_operator_mun_start[] = $_operator_mun[1];
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
    /*
    * 公式计算
    */
    protected function setAnswerTwo($user_id=null,$department_config,$logtime,$role_id=null,$zone_id=null)
    {
        //获取关联地区
        if(!empty($zone_id)){
            $_zone_arr = D('Zone','Service')->getZoneIds(array('zone_id'=>$zone_id));
            if(!empty($_zone_arr['data'])){
                foreach($_zone_arr['data'] as $k=>$v){
                    $_zone_ids[] = $v['zone_id'];
                }
            }
            $_where_log['zone_id'] = array('IN', $_zone_ids);
        }
        if(!empty($role_id)){
            if($department_config['about_user']=='createuser_id'){
                $_where_log['create_role_id'] = array('IN', $role_id);
            }elseif($department_config['about_user']=='updateuser_id'){
                $_where_log['update_role_id'] = array('IN', $role_id);
            }elseif($department_config['about_user']=='system_user_id'){
                $_where_log['system_role_id'] = array('IN', $role_id);
            }
        }
        if(!empty($user_id)){
            $_where_log[$department_config['about_user']] = array('IN', $user_id);
        }
        //换取运运算符号
        $_reg = "/\+|\-|\*|\/|\)\*|\)\//";
        preg_match_all($_reg, $department_config['formula'],$_regs);
        $_formula_symbol = $_regs[0];
        //获取参数type
        $_formula_arr = explode(',', preg_replace($_reg,',',$department_config['formula']));
        $_formula_user = explode(',', $department_config['formula_user']);
        //获取条件 时间区间
        $_where_log['logtime'] = array(array('EGT',strtotime($logtime[0])),array('ELT',strtotime($logtime[1])));
        //获取信息质量
        $_where_log['operattype'] = array('IN', $_formula_arr);
        $_where_log['infoquality'] = 1;
        $_put_data['infoquality']['A'] = D('DataLogs')->where($_where_log)->count();
        $_where_log['infoquality'] = 2;
        $_put_data['infoquality']['B'] = D('DataLogs')->where($_where_log)->count();
        $_where_log['infoquality'] = 3;
        $_put_data['infoquality']['C'] = D('DataLogs')->where($_where_log)->count();
        $_where_log['infoquality'] = 4;
        $_put_data['infoquality']['D'] = D('DataLogs')->where($_where_log)->count();
        unset($_where_log['infoquality']);
        //获取课程信息
        $course_list = D('Course','Service')->getCourseList();
        foreach($course_list['data']['data'] as $v){
            $_where_log['course_id'] = $v['course_id'];
            $_put_data['course_id'][$v['coursename']] = D('DataLogs')->where($_where_log)->count();
        }
        $_where_log['course_id'] = array('IN','0,is null');
        $_put_data['course_id']['无'] = D('DataLogs')->where($_where_log)->count();
        unset($_where_log['course_id']);
        //获取渠道
        $channel_list = D('Channel','Service')->getChannelList();
        $_temp_pchannel = array();
        $_temp_channel = array();
        foreach($channel_list['data']['data'] as $v){
            foreach($v['children'] as $v2){
                $_where_log['channel_id'] = $v2['channel_id'];
                $_count_channel = D('DataLogs')->where($_where_log)->count();
                $_temp_pchannel[$v['channelname']] = $_temp_pchannel[$v['channelname']] = $_count_channel;
                $_temp_channel[$v2['channelname'].'('.$v2['channel_id'].')'] = array('count'=>$_count_channel,'name'=>$v2['channelname'],'pname'=>$v['channelname']);
            }
        }
        $_put_data['channel']['broad'] = $_temp_pchannel;
        $_put_data['channel']['list'] = $_temp_channel;
        return $_put_data;
    }

    /*
     * 部门获取关联职位
     */
    protected function getDepartmentRole($department_id)
    {
        //获取关联职位
        $_role_list = D('Role','Service')->getRoleList(array('department_id'=>$department_id));
        foreach($_role_list['data']['data'] as $v){
            $_role_ids[] = $v['id'];
        }
        return $_role_ids;
    }
}