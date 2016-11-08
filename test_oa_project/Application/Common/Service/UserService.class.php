<?php
/*
* 客户服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Model\UserModel;
use Common\Service\DataService;
use Common\Service\BaseService;

class UserService extends BaseService
{
    //初始化
    protected $DB_PREFIX;
    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 用户到访信息查询
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserVisitInfo($param)
    {
        $param = array_filter($param);
        //获取操作人ID与区域ID
        $zone_id = $this->system_user['zone_id'];
        $system_user_id = $this->system_user_id;
        if(empty($param['user_id'])) return array('code'=>300,'msg'=>'参数异常');
        //获取销售与教务 配置项ID
        $market_arr = C('ADMIN_MARKET_ROLE');
        $educational_arr = C('ADMIN_EDUCATIONAL_ROLE');
        //合并教务与销售ID集合
        $market_arr = explode(',', $market_arr);
        $educational_arr = explode(',', $educational_arr);
        $roles_arr = array_merge($market_arr, $educational_arr);
        //获取客户信息
        $user_info = D('User', 'Service')->getFind(array('user_id'=>$param['user_id']),'status,zone_id,system_user_id');
        //客户是否回库状态？
        if($user_info['status']==160){
            $where_visitlist['zone_id'] = $zone_id;
            $visit_list = D('SystemUser', 'Service')->getSystemVisitList($where_visitlist);
            return array('code'=>602, 'msg'=>'该客户属于回库状态', 'data'=>$visit_list['data']);
        }
        //获取所属人职位
        $system_info = D('SystemUser', 'Service')->getSystemUsersInfo(array('system_user_id'=>$user_info['system_user_id']));
        $system_role_arr = array();
        if(!empty($system_info['data']['role_id'])){
            $system_role_arr = explode(',', $system_info['data']['role_id']);
        }
        //是否销售教务？
        if(array_intersect($system_role_arr, $roles_arr)){
            //是否本中心销售
            if($zone_id==$system_info['data']['zone_id']){
                return array('code'=>0, 'msg'=>'该客户属于 '.$system_info['data']['rolename'].' '.$system_info['data']['realname'], 'data'=>$system_info['data']);
            }else{
                return array('code'=>601, 'msg'=>'该客户属于非本中心 销售/教务', 'data'=>$system_info['data']);
            }
        }else{
            $where_visitlist['zone_id'] = $zone_id;
            $visit_list = D('SystemUser', 'Service')->getSystemVisitList($where_visitlist);
            return array('code'=>603, 'msg'=>'该客户不属于 销售/教务', 'data'=>$visit_list['data']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 添加客户到访 并分配到员工
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUserVisit($param){
        $param = array_filter($param);
        $system_user_id = $this->system_user_id;
        $user_id = $param['user_id'];
        $tosystem_user_id = $param['tosystem_user_id'];
        //客户分配
        $userInfo = D('User')->getFind(array('user_id'=>$user_id), 'system_user_id,status,zone_id,realname,username');
        //客户是否已上门
        if(!empty($userInfo['visittime']) && $userInfo['visittime']>0){
            return array('code'=>201,'msg'=>'客户已经确认上门过，无需重复操作');
        }
        //该客户是否在申请转入审核中
        $userApply = $this->_isApply($user_id);
        if(!empty($userApply)) return array('code'=>203,'msg'=>'客户 '.$userInfo['realname'].' 正在审核转入中，请联系本中心主管');
        //获取所属人职位
        $tosystem_info = D('SystemUser', 'Service')->getSystemUsersInfo(array('system_user_id'=>$tosystem_user_id));
        if($userInfo['status']=='70') return array('code'=>204,'msg'=>'客户在交易状态无法分配，请联系本中心主管');
        //客户转出
        $alloca_param['user_id'] = $user_id;
        $alloca_param['tosystem_user_id'] = $tosystem_user_id;
        $alloca_param['system_user_id'] = $system_user_id;
        $reflag_allocation = $this->allocationUser($alloca_param, 10);
        if($reflag_allocation['code']==0){
            D()->startTrans();
            $add_msg['system_user_id'] = $tosystem_user_id;
            $add_msg['title'] = '客户 '.$userInfo['realname'].' 上门到访';
            $add_msg['content'] = '客户姓名：'.$userInfo['realname'].'<br/>客户手机号码：'.(!empty($userInfo['username'])?(decryptPhone($userInfo['username'],C('PHONE_CODE_KEY'))):'无').'<br/>请到前台进行接待！';
            $add_msg['href'] = '/System/User/detailUser/id/'.$user_id;
            $add_msg['msgtype'] = 1;
            $add_msg['readtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
            //重置zone_id 与 更新上门时间
            $save_user['zone_id'] = $tosystem_info['data']['zone_id'];
            $save_user['visittime'] = $save_user['lastvisit'] = time();
            $flag_user_save = D('user')->editData($save_user, $user_id);
            //添加今日上门数量
            $visitLogs = D('UserVisitLogs')->where(array('date'=>date('Ymd'),'system_user_id'=>$tosystem_user_id))->find();
            if(!empty($visitLogs)){
                $data['visitnum'] = array('exp','visitnum+1');
                $visitLogs_flag = D('UserVisitLogs')->where(array('date'=>date('Ymd'),'system_user_id'=>$tosystem_user_id))->save($data);
            }else{
                $data['date'] = date('Ymd');
                $data['system_user_id'] = $tosystem_user_id;
                $data['visitnum'] = 1;
                $visitLogs_flag = D('UserVisitLogs')->data($data)->add();
            }
            //添加回访记录
            $callbackDate['attitude_id'] = 0;
            $callbackDate['remark'] = '前台操作: 客户于 '.date('Y-m-d',time()).' 上门到访！';
            $callbackDate['nexttime'] = time();
            $callbackDate['user_id'] = $user_id;
            $callbackDate['system_user_id'] = $system_user_id;
            $this->_addCallback($callbackDate);
            //添加数据记录
            $dataLog['operattype'] = 12;
            $dataLog['operator_user_id'] = $system_user_id;
            $dataLog['user_id'] = $user_id;
            $dataLog['logtime'] = time();
            D('Data', 'Service')->addDataLogs($dataLog);
            if($flag_user_save['code']==0 && $visitLogs_flag!==false){
                D()->commit();
                return  array('code'=>0,'msg'=>'分配到访客户成功');
            }else{
                D()->rollback();
                return  array('code'=>0,'msg'=>'分配操作失败');
            }
        }else{
            return  array('code'=>201,'msg'=>$reflag_allocation['msg']);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | 查找用户列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserList($where, $order=null, $limit=null)
    {
        //参数处理
        $where = $this->_dispostWhere($where);
        $field = array(
            "{$this->DB_PREFIX}user.user_id",
            "{$this->DB_PREFIX}user.username",
            "{$this->DB_PREFIX}user.tel",
            "{$this->DB_PREFIX}user.qq",
            "{$this->DB_PREFIX}user.phonevest",
            "{$this->DB_PREFIX}user.realname",
            "{$this->DB_PREFIX}user.email",
            "{$this->DB_PREFIX}user.status",
            "{$this->DB_PREFIX}user.mark",
            "{$this->DB_PREFIX}user.zone_id",
            "{$this->DB_PREFIX}user.learningtype",
            "{$this->DB_PREFIX}user.mark",
            "{$this->DB_PREFIX}user.searchkey",
            "{$this->DB_PREFIX}user.interviewurl",
            "{$this->DB_PREFIX}user.callbacknum",
            "{$this->DB_PREFIX}user.infoquality",
            "{$this->DB_PREFIX}user.updatetime",
            "{$this->DB_PREFIX}user.lastvisit",
            "{$this->DB_PREFIX}user.course_id",
            "{$this->DB_PREFIX}user.nextvisit",
            "{$this->DB_PREFIX}user.visittime",
            "{$this->DB_PREFIX}user.createtime",
            "{$this->DB_PREFIX}user.allocationtime",
            "{$this->DB_PREFIX}user.updatetime",
            "{$this->DB_PREFIX}user.attitude_id",
            "{$this->DB_PREFIX}user.introducermobile",
            "{$this->DB_PREFIX}user.learningtype",
            "{$this->DB_PREFIX}user.system_user_id",
            "{$this->DB_PREFIX}user.updateuser_id",
            "{$this->DB_PREFIX}user.createuser_id",
            "{$this->DB_PREFIX}user.channel_id",
            "{$this->DB_PREFIX}user.reservetype",
            "{$this->DB_PREFIX}user.selftype",
        );
        $result['data'] = D('User')->getList($where,$field,$order,$limit);
        $result['count'] = D('User')->getCount($where);
        if(!empty($result['data'])){
            //转换客户状态
            $result['data'] = $this->userStatus($result['data']);
        }

        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserInfo($data)
    {
        $field = array(
            "{$this->DB_PREFIX}user.user_id",
            "{$this->DB_PREFIX}user.username",
            "{$this->DB_PREFIX}user.tel",
            "{$this->DB_PREFIX}user.qq",
            "{$this->DB_PREFIX}user.realname",
            "{$this->DB_PREFIX}user.email",
            "{$this->DB_PREFIX}user.status",
            "{$this->DB_PREFIX}user.mark",
            "{$this->DB_PREFIX}user.learningtype",
            "{$this->DB_PREFIX}user.searchkey",
            "{$this->DB_PREFIX}user.interviewurl",
            "{$this->DB_PREFIX}user.infoquality",
            "{$this->DB_PREFIX}user.createtime",
            "{$this->DB_PREFIX}user.updatetime",
            "{$this->DB_PREFIX}user.allocationtime",
            "{$this->DB_PREFIX}user.lastvisit",
            "{$this->DB_PREFIX}user.nextvisit",
            "{$this->DB_PREFIX}user.visittime",
            "{$this->DB_PREFIX}user.attitude_id",
            "{$this->DB_PREFIX}user.introducermobile",
            "{$this->DB_PREFIX}user.course_id",
            "{$this->DB_PREFIX}user.channel_id",
            "{$this->DB_PREFIX}user_info.remark",
            "{$this->DB_PREFIX}user_info.sex",
            "{$this->DB_PREFIX}user_info.birthday",
            "{$this->DB_PREFIX}user_info.identification",
            "{$this->DB_PREFIX}user_info.homeaddress",
            "{$this->DB_PREFIX}user_info.address",
            "{$this->DB_PREFIX}user_info.urgentname",
            "{$this->DB_PREFIX}user_info.urgentmobile",
            "{$this->DB_PREFIX}user_info.postcode",
            "{$this->DB_PREFIX}user_info.education_id",
            "{$this->DB_PREFIX}user_info.major",
            "{$this->DB_PREFIX}user_info.school",
            "{$this->DB_PREFIX}user_info.workyear",
            "{$this->DB_PREFIX}user_info.lastposition",
            "{$this->DB_PREFIX}user_info.lastcompany",
            "{$this->DB_PREFIX}user_info.lastsalary",
            "{$this->DB_PREFIX}user_info.wantposition",
            "{$this->DB_PREFIX}user_info.wantsalary",
            "{$this->DB_PREFIX}user_info.workstatus",
            "{$this->DB_PREFIX}user_info.englishstatus",
            "{$this->DB_PREFIX}user_info.englishlevel",
            "{$this->DB_PREFIX}user_info.computerlevel",
            "{$this->DB_PREFIX}zone.zone_id",
            "{$this->DB_PREFIX}zone.name as zonename",
            "{$this->DB_PREFIX}course.course_id",
            "{$this->DB_PREFIX}course.coursename",
            "{$this->DB_PREFIX}system_user.system_user_id",
            "{$this->DB_PREFIX}system_user.realname as system_realname",
            "A.system_user_id as updateuser_id",
            "A.realname as updateuser_realname",
            "B.system_user_id as createuser_id",
            "B.realname as createuser_realname"
        );
        $where[$this->DB_PREFIX.'user.user_id'] = $data['user_id'];
        $result = D('User')
            ->field($field)
            ->join('LEFT JOIN __USER_INFO__ ON __USER_INFO__.user_id=__USER__.user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->join('LEFT JOIN __SYSTEM_USER__ B ON B.system_user_id=__USER__.createuser_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER__.zone_id')
            ->join('LEFT JOIN __COURSE__ ON __COURSE__.course_id=__USER__.course_id')
            ->where($where)
            ->find();
        if(!empty($result)){
            //转换客户状态
            $result = $this->userStatus($result);
        }
        return array('code'=>'0', 'data'=>$result);
    }


    /*
    |--------------------------------------------------------------------------
    | 客户转出/批量转出（分配）
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function allocationUser($data, $rank=1)
    {
        $_time = time();
        $data = array_filter($data);
        $data['system_user_id'] = $this->system_user_id;
        //获取客户信息与被转出人信息
        $userList = D('User')->getList(array('user_id'=>array('IN',$data['user_id'])),'user_id,status,channel_id,system_user_id,realname,infoquality');
        $_systemInfo = D('SystemUser')->getFind(array('system_user_id'=>$data['tosystem_user_id']));
        if(empty($userList)) return array('code'=>200,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>201,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //普通员工判断归属人
            if($rank==1){
                if($data['system_user_id']!=$v['system_user_id']) return array('code'=>202,'msg'=>'只有归属人才能分配该客户信息');
                if($data['tosystem_user_id']==$v['system_user_id']) return array('code'=>203,'msg'=>'无法将客户转给自己哦');
            }
            //该客户是否在申请转入审核中
            $userApply = $this->_isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>203,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        $save_user['mark'] = 1;
        $save_user['nextvisit'] = $_time;
        $save_user['attitude_id'] = 0;
        $save_user['callbacknum'] = 0;
        $save_user['lastvisit'] = $_time;
        $save_user['allocationtime'] = $_time;
        $save_user['system_user_id'] = $data['tosystem_user_id'];
        //数据更新
        D()->startTrans();
        //获取各状态
        if($rank==2){
            //批量
            if(count(explode(',',$data['user_id']))>1){
                $data_callback['remark'] = '批量客户转出(管理操作)';
                $data_callback['callbacktype'] = 11;
            }else{
                $data_callback['remark'] = '客户转出(管理操作)';
                $data_callback['callbacktype'] = 10;
            }
            $dataLog['operattype'] = 15;
        }elseif($rank==1){
            $data_callback['remark'] = '客户转出';
            $data_callback['callbacktype'] = 1;
            $dataLog['operattype'] = 5;
        }
        //操作添加数据记录
        $dataLog['operattype'] = 3;
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = $_time;
        D('Data','Service')->addDataLogs($dataLog);
        //添加数据记录
        $save_user['zone_id'] = $_systemInfo['zone_id'];
        $where['user_id'] = array('IN',$data['user_id']);
        $result = D('User')->where($where)->save($save_user);
        if($result!==false){
            //添加分配记录
            $data_callback['status'] = 0;
            $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $data_callback['user_id'] = $data['user_id'];
            $data_callback['nexttime'] = $_time;
            $data_callback['system_user_id'] = $data['system_user_id'];
            $this->_addCallback($data_callback);
            //操作添加数据记录
            $dataLog2['operattype'] = 3;
            $dataLog2['operator_user_id'] = $data['system_user_id'];
            $dataLog2['user_id'] = $data['user_id'];
            $dataLog2['logtime'] = $_time;
            D('Data','Service')->addDataLogs($dataLog2);
            //系统消息提醒
            $add_msg['system_user_id'] = $data['tosystem_user_id'];
            $_system_user_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$data['system_user_id']));
            $add_msg['title'] = $_system_user_info['data']['realname'].' 将'.count(explode(',',$data['user_id'])).'名客户转出到您库中';
            $add_msg['content'] = $_system_user_info['data']['realname'].' 将 ';
            //客户
            foreach($userList as $k=>$v){
                $add_msg['content'] .= "<span class='user_realname' data-id='".$v['user_id']."'> ".$v['realname']." </span>";
            }
            $add_msg['content'] .=' 转出到您库中，请及时跟进。';
            $add_msg['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
            D()->commit();
            return array('code'=>0,'msg'=>'数据分配成功');
        }else{
            D()->rollback();
            return array('code'=>1,'msg'=>'数据分配失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 客户出库/批量出库（分配）
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function restartUser($data, $rank=1)
    {
        $_time = time();
        $data = array_filter($data);
        $data['system_user_id'] = $this->system_user_id;
        if(empty($data['user_id']) || empty($data['tosystem_user_id'])) return array('code'=>300,'msg'=>'参数异常');
        //获取客户信息与被转出人信息
        $userList = D('User')->getList(array('user_id'=>array('IN',$data['user_id'])),'user_id,status,channel_id,system_user_id,realname,infoquality');
        $_systemInfo = D('SystemUser')->getFind(array('system_user_id'=>$data['tosystem_user_id']));
        if(empty($userList)) return array('code'=>201,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>202,'msg'=>'客户'.$v['realname'].'状态不予许出库');
            //普通员工判断归属人
            if($rank==1){
                if($data['system_user_id']!=$v['system_user_id']) return array('code'=>203,'msg'=>'只有归属人才能分配该客户信息');
                if($data['tosystem_user_id']==$v['system_user_id']) return array('code'=>204,'msg'=>'无法将客户转给自己哦');
            }
            //该客户是否在申请转入审核中
            $userApply = $this->_isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>205,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        //出库参数
        $save_user['status'] = 20;
        $save_user['mark'] = 1;
        $save_user['nextvisit'] = $_time;
        $save_user['attitude_id'] = 0;
        $save_user['callbacknum'] = 0;
        $save_user['lastvisit'] = $_time;
        $save_user['allocationtime'] = $_time;
        $save_user['system_user_id'] = $data['tosystem_user_id'];
        $save_user['updateuser_id'] = $data['tosystem_user_id'];
        $save_user['updatetime'] = $_time;
        $save_user['remark'] = null;
        //数据更新
        D()->startTrans();
        if($rank==2){
            //批量
            if(count(explode(',',$data['user_id']))>1){
                $data_callback['remark'] = '批量客户出库(管理操作)';
                $data_callback['callbacktype'] = 13;
            }else{
                $data_callback['remark'] = '客户出库(管理操作)';
                $data_callback['callbacktype'] = 12;
            }
            $dataLog['operattype'] = 15;
        }else{
            $data_callback['remark'] = '客户出库';
            $data_callback['callbacktype'] = 4;
            $dataLog['operattype'] = 5;
        }
        //-- 添加被出库人 转出量/系统转出量
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = $_time;
        D('Data', 'Service')->addDataLogs($dataLog);
        $save_user['zone_id'] = $_systemInfo['zone_id'];
        $where['user_id'] = array('IN',$data['user_id']);
        $result = D('User')->where($where)->save($save_user);
        if($result!==false){
            //添加分配记录
            $data_callback['status'] = 0;
            $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $data_callback['user_id'] = $data['user_id'];
            $data_callback['nexttime'] = $_time;
            $data_callback['system_user_id'] = $data['system_user_id'];
            $this->_addCallback($data_callback);
            //添加转出人 出库量
            $dataLog2['operattype'] = 3;
            $dataLog2['operator_user_id'] = $data['system_user_id'];
            $dataLog2['user_id'] = $data['user_id'];
            $dataLog2['logtime'] = $_time+1;
            D('Data', 'Service')->addDataLogs($dataLog2);
            //出库隐藏历史回访记录
            $this->_heiddenOldInfo($data['user_id']);
            //系统消息提醒
            $add_msg['system_user_id'] = $data['tosystem_user_id'];
            $_system_user_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$data['system_user_id']));
            $add_msg['title'] = $_system_user_info['data']['realname'].' 将'.count(explode(',',$data['user_id'])).'名客户出库到您库中';
            $add_msg['content'] = $_system_user_info['data']['realname'].' 将 ';
            //客户
            foreach($userList as $k=>$v){
                $add_msg['content'] .= "<span class='user_realname' data-id='".$v['user_id']."'> ".$v['realname']." </span>";
            }
            $add_msg['content'] .=' 出库到您库中，请及时跟进。';
            $add_msg['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
            D()->commit();
            return array('code'=>0,'msg'=>'数据出库成功');
        }else{
            D()->rollback();
            return array('code'=>1,'msg'=>'数据出库失败');
        }
    }



    /*
    |--------------------------------------------------------------------------
    | 确认到访
    |--------------------------------------------------------------------------
    | user_id:客户
    | @author zgt
    */
    public function affirmVisit($data)
    {
        //必要参数
        $_time = time();
        $data['system_user_id'] = $this->system_user_id;
        if(empty($data['user_id']) || empty($data['system_user_id'])) return array('code'=>300,'msg'=>'参数异常');
        //获取数据
        $info = D('User')->getFind(array('user_id'=>$data['user_id']),'visittime,user_id,status,system_user_id');
        //是否归属人操作？
        if($data['system_user_id']!=$info['system_user_id']) return array('code'=>201,'msg'=>'只有归属人才能分配该客户信息');
        //是否第一次到访？
        if(empty($info['visittime']) || $info['visittime']==0){
            $save_data['visittime'] = $_time;
            D('User')->editData($save_data, $data['user_id']);
        }else{
            return array('code'=>202,'msg'=>'该客户已到访过');
        }
        //添加分配记录
        $data_callback['status'] = 1;
        $data_callback['callbacktype'] = 20;
        $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
        $data_callback['user_id'] = $data['user_id'];
        $data_callback['nexttime'] = $_time;
        $data_callback['system_user_id'] = $data['system_user_id'];
        $data_callback['remark'] = '客户上门到访';
        $reflag = $this->_addCallback($data_callback);
        //添加数据记录
        $dataLog['operattype'] = 12;
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = $_time;
        D('Data', 'Service')->addDataLogs($dataLog);
        if($reflag['code']==0){
            return array('code'=>0,'msg'=>'确认到访成功');
        }
        return array('code'=>100,'msg'=>'确认到访失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 该客户是否在申请转入审核中
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function isApply($user_id)
    {
        return D('UserApply')->field('user_id')->where(array('user_id'=>$user_id,'status'=>10))->select();
    }


    /**
     * 转换客户状态
     * @author nxx
     */
    protected function userStatus($result)
    {
        if(empty($result[0])){
            $arrStr[0] = $result;
        }else{
            $arrStr = $result;
        }
        //课程列表-转化状态集合
        $courseList = D('Course','Service')->getCourseList();
        $course_status = array();
        foreach($courseList['data']['data'] as $k=>$v){
            $course_status[$v['course_id']] = $v['coursename'];
        }
        //客户状态
        $user_status = C('FIELD_STATUS.USER_STATUS');
        //跟进结果转换
        $user_attitude = C('FIELD_STATUS.USER_ATTITUDE');
        //学习平台
        $user_learningtype = C('FIELD_STATUS.USER_LEARNINGTYPE');
        //信息质量
        $user_infoquality = C('FIELD_STATUS.USER_INFOQUALITY');
        foreach($arrStr as $k=>$v){
            $arrStr[$k]['system_realname'] = '';
            $arrStr[$k]['updateuser_realname'] = '';
            $arrStr[$k]['createuser_realname'] = '';
            $arrStr[$k]['channelnames'] = '';
            if(!empty($v['system_user_id'])){
                $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
                $arrStr[$k]['system_realname'] = $systemUser['data']['realname'];
            }
            if(!empty($v['updateuser_id'])){
                $updateUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['updateuser_id']));
                $arrStr[$k]['updateuser_realname'] = $updateUser['data']['realname'];
            }
            if(!empty($v['createuser_id'])){
                $createUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['createuser_id']));
                $arrStr[$k]['createuser_realname'] = $createUser['data']['realname'];
            }
            if(!empty($v['channel_id'])){
                $channel = D('Channel','Service')->getChannelInfo(array('channel_id'=>$v['channel_id']));
                if($channel['data']['pid']!=0){
                    $channel_parent = D('Channel','Service')->getChannelInfo(array('channel_id'=>$channel['data']['pid']));
                    $arrStr[$k]['channel_names'] = $channel_parent['data']['channelname'].'-'.$channel['data']['channelname'];
                }else{
                    $arrStr[$k]['channel_names'] = $channel['data']['channelname'];
                }
            }
            if(!empty($v['course_id'])){
                $course = D('Course','Service')->getCourseInfo(array('course_id'=>$v['course_id']));
                $arrStr[$k]['course_name'] = $course['data']['coursename'];
            }else{
                $arrStr[$k]['course_name'] = '无';
            }
            if(!empty($v['visittime']) && $v['visittime']!=0)$arrStr[$k]['visit_time'] = date('Y-m-d H:i:s', $v['visittime']);
            if(!empty($v['nextvisit']) && $v['nextvisit']!=0)$arrStr[$k]['nextvisit_time'] = date('Y-m-d H:i:s', $v['nextvisit']);
            if(!empty($v['lastvisit']) && $v['lastvisit']!=0)$arrStr[$k]['lastvisit_time'] = date('Y-m-d H:i:s', $v['lastvisit']);
            if(!empty($v['allocationtime']) && $v['allocationtime']!=0)$arrStr[$k]['allocation_time'] = date('Y-m-d H:i:s', $v['allocationtime']);
            if(!empty($v['updatetime']) && $v['updatetime']!=0)$arrStr[$k]['update_time'] = date('Y-m-d H:i:s', $v['updatetime']);
            if(!empty($v['createtime']) && $v['createtime']!=0)$arrStr[$k]['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
            if(!empty($v['status']))$arrStr[$k]['status_name'] = $user_status[$v['status']];
            if(!empty($v['attitude_id']))$arrStr[$k]['attitude_name'] = $user_attitude[$v['attitude_id']];
            if(!empty($v['learningtype']))$arrStr[$k]['learningtype_name'] = $user_learningtype[$v['learningtype']];
            if(!empty($v['infoquality']))$arrStr[$k]['infoquality_name'] = $user_infoquality[$v['infoquality']];
            if(!empty($v['username']))$arrStr[$k]['mobile'] = decryptPhone($v['username'],C('PHONE_CODE_KEY'));
        }
        if(empty($result[0])){
            return $arrStr[0];
        }else{
            return $arrStr;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 专线电话 呼叫客户
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  $type=2:只拨固话
    | @author zgt
    */
    public function callUser($param,$type=1)
    {
        //必要参数
        if(empty($param['user_id'])) return array('code'=>201,'msg'=>'参数异常');
        $re_data = D('User')->getFind(array('user_id'=>$param['user_id']), 'username,system_user_id,tel');
        if(empty($re_data)) return array('code'=>101,'msg'=>'找不到该客户信息');
        if($re_data['system_user_id']!=$param['system_user_id']) return array('code'=>102,'msg'=>'您不是该客户所属人，无该操作权限！');
        //获取操作人启用号码 无则获取手机号码
        $call_number = D('CallNumber')->getFind(array('system_user_id'=>$param['system_user_id'],'number_start'=>1,'number_status'=>1),'number,number_type');
        if(!empty($call_number)){
            if($call_number['number_type']==1){
                $mobile_caller = str_replace('-','',$call_number['number']);
            }else{
                $mobile_caller = $call_number['number'];
            }
        }else{
            $re_data_sys = D('SystemUser')->getFind(array('system_user_id'=>$param['system_user_id']), 'username');
            $mobile_caller = decryptPhone($re_data_sys['username'],C('PHONE_CODE_KEY'));
        }
        //呼叫客户号码  是否直接呼叫客户固话?
        if(!empty($re_data['username']) && $type==1){
            $mobile_callee = decryptPhone($re_data['username'],C('PHONE_CODE_KEY'));
        }elseif(!empty($re_data['tel'])){
            $mobile_callee = str_replace('-','',$re_data['tel']);
        }else{
            return array('code'=>204,'msg'=>'该客户无可拨通电话');
        }
        $re_flag = D('Netease', 'Service')->startcall($mobile_caller,$mobile_callee);
        if($re_flag['code']==0){
            $_add_data = array(
                'call_key' => $re_flag['data'],
                'user_id' => $param['user_id'],
                'system_user_id' => $param['system_user_id'],
                'call_caller' => $mobile_caller,
                'call_callee' => $mobile_callee,
                'call_time' => time()
            );
            session('call_phone',$re_flag['data']);
            D('CallLogs')->addData($_add_data);
        }
        return array('code'=>$re_flag['code'],'msg'=>$re_flag['msg'],'data'=>$re_flag['data']);
    }

    /*
    |--------------------------------------------------------------------------
    | 专线电话 查看呼叫详情
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function getCall()
    {
        $call_key = session('call_phone');
        $re_flag = D('Netease','Service')->queryBySession($call_key);
        if($re_flag['data']->status=='SUCCESS'){
            $redata = $this->getCallList(array('call_key'=>$call_key,'rank'=>2));
            if(empty($redata['data'][0])) return array('code'=>1,'msg'=>'通话中');
            return array('code'=>0,'msg'=>'通话结束','data'=>$redata['data'][0]);
        }
        return array('code'=>1,'msg'=>'通话中');
    }

    /*
    |--------------------------------------------------------------------------
    | 专线电话 查看呼叫详情
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function getCallList($param)
    {
        $param['rank'] =  !empty($param['rank'])?$param['rank']:1;
        if($param['rank']==1) $_where['system_user_id'] = $param['system_user_id'];
        $_where['user_id'] = !empty($param['user_id'])?$param['user_id']:null;
        $_where['call_key'] = !empty($param['call_key'])?$param['call_key']:null;
        $_where['call_status'] = 1;
        $_where = array_filter($_where);
        $result = D('CallLogs')->getList($_where,null,'call_time DESC');
        foreach($result as $k=>$v){
            $info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
            $result[$k]['system_realname'] = str_replace($info['data']['sign'].'-',' ',$info['data']['realname']);
            $result[$k]['system_face'] = $info['data']['face'];
            $result[$k]['system_sex'] = $info['data']['sex'];
            $result[$k]['call_time_ymd'] = date('Y-m-d H:i', $v['call_time']);
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUser($data)
    {
        if (empty($data['username']) && empty($data['tel']) && empty($data['qq'])) return array('code' => 311, 'msg' => '手机号码 / 固定电话 / QQ 至少填写一项');
        if (empty($data['infoquality'])) return array('code' => 312, 'msg' => '信息质量不能为空');
        if (empty($data['channel_id'])) return array('code' => 313, 'msg' => '所属渠道不能为空');

        if (empty($data['course_id']) && $data['course_id'] != 0) {
            return array('code' => 314, 'msg' => '请选择意向课程');
        }
        // if (empty($data['remark'])) return array('code' => 315, 'msg' => '备注不能为空');
        $data['realname'] = trim($data['realname']);
        $data['system_user_id'] = $this->system_user_id;
        $data['zone_id'] = $this->system_user['zone_id'];
        $data['allocationtime'] = time();
        $data['updatetime'] = time();
        $data['lastvisit'] = time();
        $data['updatetime'] = time();
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        $data['updateuser_id'] = $data['system_user_id'];
        $data['createuser_id'] = $data['system_user_id'];
        //验证唯一字段 数据处理
        $checkData = $this->_checkField($data);
        if($checkData['code']!=0) {
            $createUpdate['createupdatetime'] = time();
            if ($checkData['code'] == 201) {
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                D('User')->where(array('username'=>$data['username']))->save($createUpdate);
            }
            if ($checkData['code'] == 203) {
                D('User')->where(array('tel'=>$data['tel']))->save($createUpdate);
            }
            if ($checkData['code'] == 205) {
                D('User')->where(array('qq'=>$data['qq']))->save($createUpdate);
            }
            return array('code'=>$checkData['code'], 'msg'=>$checkData['msg'], 'sign'=>!empty($checkData['sign'])?$checkData['sign']:null);
        }
        $data = $checkData['data'];
        //是否获取新渠道
        $newChannelData = $this->_isNewChannel($data);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg'], 'sign'=>!empty($newChannelData['sign'])?$newChannelData['sign']:null);
        $data = $newChannelData['data'];
        //启动事务
        $data = array_filter($data);
        D()->startTrans();
        $data['createupdatetime'] = time();
        $UserModel = new UserModel();
        $reUserId = $UserModel->addData($data);
        if($reUserId['code']==0){
            $data_info = $data;
            unset($data['createupdatetime']);
            $data_info['user_id'] = $reUserId['data'];
            $reUserInfo = D('UserInfo')->addData($data_info);
            //添加数据记录
            $dataLog['operattype'] = '1';
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['user_id'] = $reUserId['data'];
            $dataLog['logtime'] = time();
            D('Data','Service')->addDataLogs($dataLog);
        }
        //添加分配记录
        if($reUserId['code']==0 && $reUserInfo['code']==0){
            D()->commit();
            return array('code'=>0,'msg'=>'客户添加成功','data'=>$reUserId['data']);
        }else{
            D()->rollback();
            return array('code'=>$reUserId['code'],'msg'=>$reUserId['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editUser($data)
    {
        $data = array_filter($data);
        if(empty($data['user_id'])) return array('code'=>300,'msg'=>'参数异常');
        $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        if($user['system_user_id']!=$this->system_user_id) return array('code'=>201,'msg'=>'只有归属人才能修改该客户信息');
        //验证唯一字段 数据处理
        $checkData = $this->_checkField($data);
        if($checkData['code']!=0) return array('code'=>$checkData['code'], 'msg'=>$checkData['msg'], 'sign'=>!empty($checkData['sign'])?$checkData['sign']:null);
        $data = $checkData['data'];
        //是否获取新渠道
        $newChannelData = $this->_isNewChannel($data);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg'], 'sign'=>!empty($newChannelData['sign'])?$newChannelData['sign']:null);
        $data = $newChannelData['data'];
        //数据处理
        $result = D('User')->where(array('user_id'=>$data['user_id']))->save($data);
        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>100,'msg'=>'数据修改失败');
    }

    /*
   |--------------------------------------------------------------------------
   | 更新、修改客户详情
   |--------------------------------------------------------------------------
   | user_id system_user_id
   | @author zgt
   */
    public function editUserInfo($data)
    {
        $data = array_filter($data);
        if(empty($data['user_id'])) return array('code'=>300,'msg'=>'参数异常');
        $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        if($user['system_user_id']!=$this->system_user_id) return array('code'=>201,'msg'=>'只有归属人才能修改该客户信息');
        //年龄转换时间戳
        if (!empty($request['birthday'])) $request['birthday'] = strtotime("-{$this->getStrInt($request['birthday'])} year");
        $userInfo = D('UserInfo')->where(array('user_id'=>$data['user_id']))->find();

        if(empty($userInfo)){
            $result = D('UserInfo')->data($data)->add();
        }else{
            $result = D('UserInfo')->where(array('user_id'=>$data['user_id']))->save($data);
        }
        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>100,'msg'=>'数据修改失败');
    }


    /*
     |--------------------------------------------------------------------------
     | 添加用户回访记录-员工
     |--------------------------------------------------------------------------
     | user_id system_user_id
     | @author zgt
     */
    public function addUserCallback($data)
    {
        $data = array_filter($data);
        //添加回访记录
        if (empty($data['nextvisit'])) return array('code' => 300, 'msg' => '回访时间不能为空');
        if (empty($data['waytype'])) return array('code' => 301, 'msg' => '请选择回访方式');
        if (empty($data['attitude_id'])) return array('code' => 302, 'msg' => '请选择跟进结果');
        if (empty($data['remark'])) return array('code' => 303, 'msg' => '备注不能为空');
        if (empty($data['nexttime'])) return array('code' => 304, 'msg' => '下次回访时间不能为空');
        if ($data['nexttime'] > strtotime('+15 day')) return array('code' => 201, 'msg' => '回访时间设置不能大于十五天');
        if ($data['nexttime'] < time()) return array('code' => 202, 'msg' => '回访时间设置不能小于当前时间');
        //数据验证
        $user = D('User')->field('user_id,status')->where(array('user_id'=>array('IN', $data['user_id'])))->select();
        if(empty($user)) return array('code'=>2,'msg'=>'查找不到客户信息');
        $add_callback = array();
        //启动事务
        D()->startTrans();
        foreach($user as $k=>$v){
            $data_user['attitude_id'] = $data['attitude_id'];
            $data_user['nextvisit'] = $data['nexttime'];
            $data_user['callbacktype'] = $data['callbacktype'];
            //更新客户状态
            if($v['status']==20){
                $data_user['status'] = 30;
            }
            $data['callbacktime'] = time();
            $data_user['callbacknum'] = array('exp','callbacknum+1');
            $data_user['lastvisit'] = $data['callbacktime'];
            //添加数据记录
            $dataLog['operattype'] = '11';
            $dataLog['operator_user_id'] = $this->system_user_id;
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = time();
            D('Data','Service')->addDataLogs($dataLog);
            $reflag_save = D('User')->where(array('user_id'=>$v['user_id']))->save($data_user);
            if($reflag_save===false) return false;
            //获取新增数据集合
            $add_callback[$k] = $data;
            $add_callback[$k]['system_user_id'] = $this->system_user_id;
            $add_callback[$k]['user_id'] = $v['user_id'];
        }
        //批量新增回访
        $reflag = D('UserCallback')->addAll($add_callback);
        if($reflag!==false && $reflag_save!==false){
            D()->commit();
            return array('code'=>0,'msg'=>'添加成功');
        }else{
            D()->rollback();
            return array('code'=>12,'msg'=>'数据添加失败');
        }
    }



    /*
   |--------------------------------------------------------------------------
   | 获取客户短信记录
   |--------------------------------------------------------------------------
   | $rank:(1普通 2主管)
   | @author zgt
   */
    public function getUserSmsLog($data, $rank=1)
    {
        $data = array_filter($data);
        if (empty($data['user_id'])) return array('code' => 11, 'msg' => '客户ID不能为空');
        $where['touser_id'] = $data['user_id'];
        if($rank==1){
            $where['display'] = 1;
        }
        //获取数据
        $result = D('SmsLogs')->field('realname,face,touser_id,content,sendstatus,senderror,sendtime')
            ->where($where)
            ->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__SMS_LOGS__.system_user_id')
            ->order('sendtime desc')
            ->select();
        //转换时间格式
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['send_time'] = date('Y-m-d H:i:s', $v['sendtime']);
            }
        }
        return array('code'=>0, 'data'=>$result);
    }



    /*
    |--------------------------------------------------------------------------
    | 获取回访记录
    |--------------------------------------------------------------------------
    | user_id system_user_id $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function getUserCallback($param)
    {
        $param['rank'] = empty($param['rank'])?'1':$param['rank'];
        $where[$this->DB_PREFIX.'user_callback.user_id'] = $param['user_id'];
        if($param['rank']==1) $where[$this->DB_PREFIX.'user_callback.status'] = 1;
        $field = array(
            "{$this->DB_PREFIX}user_callback.user_id",
            "{$this->DB_PREFIX}user_callback.system_user_id",
            "{$this->DB_PREFIX}user_callback.waytype",
            "{$this->DB_PREFIX}user_callback.attitude_id",
            "{$this->DB_PREFIX}user_callback.remark",
            "{$this->DB_PREFIX}user_callback.nexttime",
            "{$this->DB_PREFIX}user_callback.callbacktime",
            "{$this->DB_PREFIX}system_user.realname",
            "{$this->DB_PREFIX}system_user.face"
        );
        $join = '__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER_CALLBACK__.system_user_id';
        $result =  D('UserCallback')->getList($where,$field,$this->DB_PREFIX.'user_callback.callbacktime DESC',null,$join);
        return array('code'=>0, 'data'=>$result);
    }


    /*
    |--------------------------------------------------------------------------
    | 赎回客户
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function redeemUser($data)
    {
        $data = array_filter($data);
        //当前操作人
        $data['system_user_id'] = $this->system_user_id;
        //赎回客户
        if (empty($data['nexttime'])) return array('code' => 300, 'msg' => '回访时间不能为空');
        $data['nexttime'] = strtotime($data['nexttime']);
        if (empty($data['remark'])) return array('code' => 301, 'msg' => '备注不能为空');
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id'])  || empty($data['nexttime']) || empty($data['remark'])) return array('code'=>2,'msg'=>'参数异常');
        //该客户是否在申请转入审核中
        $userApply = $this->_isApply($data['user_id']);
        if(!empty($userApply)) return array('code'=>201,'msg'=>'客户 正在审核转入中，无法赎回');
        //获取客户信息与被转出人信息
        $userInfo = D('User')->getFind(array('user_id'=>array('IN',$data['user_id'])),'user_id,status,channel_id,system_user_id,realname,infoquality');
        if($data['system_user_id']!=$userInfo['system_user_id']) return array('code'=>202,'msg'=>'只有归属人才能分配该客户信息');
        if($userInfo['status']!=160)  return array('code'=>203,'msg'=>'客户不属于回库状态,无法赎回');
        D()->startTrans();
        $save_data['status'] = 30;
        D('User')->editData($save_data, $data['user_id']);
        //添加分配记录
        $data_callback['status'] = 1;
        $data_callback['callbacktype'] = 3;
        $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
        $data_callback['user_id'] = $data['user_id'];
        $data_callback['nexttime'] = $data['nexttime'];
        $data_callback['system_user_id'] = $data['system_user_id'];
        $data_callback['remark'] = $data['remark'];
        $reflag = $this->_addCallback($data_callback);
        //添加数据记录
        $dataLog['operattype'] = 9;
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = time();
        D('Data','Service')->addDataLogs($dataLog);
        if($reflag['code']==0){
            D()->commit();
            return array('code'=>0,'msg'=>'赎回客户成功');
        }
        D()->rollback();
        return array('code'=>100,'msg'=>'赎回客户失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 客户放弃/回库
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function abandonUser($data, $rank=1)
    {
        //必要参数
        if(empty($data['user_id'])) return array('code'=>301,'msg'=>'参数异常');
        if (empty($data['remark'])) return array('code'=>300,'msg'=>'原因不能为空');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(empty($userList)) return array('code'=>100,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v) {
            //是否交易中
            if ($v['status'] == '70') return array('code' => 201, 'msg' => '客户' . $v['realname'] . '状态不予许放弃');
            //普通员工判断归属人
            if ($rank == 1) {
                if ($this->system_user_id != $v['system_user_id']) return array('code' => 200, 'msg' => '只有归属人才能分配该客户信息');
            }
        }
        $_time = time();
        //数据更新
        D()->startTrans();
        $save_user['status'] = 160;
        $where['user_id'] = array('IN',$data['user_id']);
        $result = D('User')->where($where)->save($save_user);
        if($result!==false){
            //添加回访记录
            $data_callback['status'] = 0;
            $data_callback['user_id'] = $data['user_id'];
            $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $data_callback['system_user_id'] = $this->system_user_id;
            $data_callback['nexttime'] = $_time;
            if($rank==2){
                //批量
                $user_ids = explode(',', $data['user_id']);
                if(count($user_ids)>1){
                    $data_callback['remark'] = '批量客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 15;
                }else{
                    $data_callback['remark'] = '客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 14;
                }
                $dataLog['operattype'] = '8';
            }else{
                $data_callback['remark'] = '客户放弃：'.$data['remark'];
                $data_callback['callbacktype'] = 2;
                $dataLog['operattype'] = '6';
            }
            $this->_addCallback($data_callback);
            //操作后-添加数据记录
            $dataLog['operator_user_id'] = $this->system_user_id;
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $_time;
            D('Data','Service')->addDataLogs($dataLog);
            D()->commit();
            return array('code'=>0,'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>100,'msg'=>'操作失败');
    }

    /*
   |--------------------------------------------------------------------------
   | 更换客户标记  普通/重点
   |--------------------------------------------------------------------------
   | user_id:客户
   | @author zgt
   */
    public function MarkUser($data)
    {
        //必要参数
        if(empty($data['user_id'])) return array('code'=>300,'msg'=>'参数异常');
        $user = D('User')->getFind(array('user_id'=>$data['user_id']),'system_user_id,status,mark');
        if(empty($user)) return array('code'=>2,'msg'=>'查找不到客户信息');
        if ($this->system_user_id != $user['system_user_id']) return array('code' => 200, 'msg' => '只有归属人才能操作该客户信息');
        if($user['mark']==1){
            $_save['mark'] = 2;
        }else{
            $_save['mark'] = 1;
        }
        $reflag = D('User')->editData($_save, $data['user_id']);
        if($reflag['code']==0){
            return array('code'=>0,'msg'=>'操作成功');
        }
        return array('code'=>100,'msg'=>'操作失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 申请转入客户
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id:申请人 channel_id：请选择渠道 applyreason：申请理由不能为空 infoquality：信息质量
    | @author zgt
    */
    public function applyUser($data)
    {
        $data = array_filter($data);
        $data['system_user_id'] = $this->system_user_id;
        $_time = time();
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id']) ) return array('code'=>300,'msg'=>'参数异常');
        if(empty($data['channel_id'])) return array('code'=>301,'msg'=>'渠道不能为空');
        if(empty($data['infoquality'])) return array('code'=>302,'msg'=>'信息质量不能为空');
        //是否有转介绍人手机号码
        if(!empty($data['introducermobile'])){
            if($this->checkMobile($data['introducermobile'])){
                $data['introducermobile'] = trim($data['introducermobile']);
                $data['introducermobile'] = encryptPhone($data['introducermobile'], C('PHONE_CODE_KEY'));
            }else{
                return array('code'=>200, 'msg'=>'介绍人手机号不正确');
            }
        }
        //获取用户信息
        $userInfo = D('User')->getFind(array('user_id'=>$data['user_id']));
        //保存旧用户信息
        $data['affiliation_system_user_id'] = $userInfo['system_user_id'];
        $data['affiliation_channel_id'] = $userInfo['channel_id'];
        $data['applytime'] = $_time;
        //该客户是否在申请转入审核中
        $userApply = $this->_isApply($data['user_id']);
        if(!empty($userApply)) return array('code'=>201,'msg'=>'客户 正在被其他人申请转入中，无法再次申请');
        //客户是否回库状态
        if($userInfo['status'] != 160) return array('code'=>202,'msg'=>'该客户不在回库状态,不能申请');
        //添加记录
        $reflag = D('UserApply')->addData($data);
        if($reflag['code']==0){
            return array('code'=>0,'msg'=>'提交转入申请成功，请等待审核');
        }else{
            return array('code'=>$reflag['code'],'msg'=>$reflag['msg']);
        }
    }

    /*
   |--------------------------------------------------------------------------
   | 申请转入客户
   |--------------------------------------------------------------------------
   | user_id:客户 system_user_id:申请人 channel_id：请选择渠道 applyreason：申请理由不能为空 infoquality：信息质量
   | @author zgt
   */
    public function getApplyUserList($data)
    {
        $data = array_filter($data);
        $data = $this->_applyUserWhere($data);
        $page = !empty($data['page'])?$data['page']:'0,30';
        $join = 'left join __USER__ A ON A.user_id=__USER_APPLY__.user_id';
        $field = "A.realname,A.username,A.qq,A.tel,A.user_id,
                {$this->DB_PREFIX}user_apply.user_apply_id,
                {$this->DB_PREFIX}user_apply.infoquality,
                {$this->DB_PREFIX}user_apply.system_user_id,
                {$this->DB_PREFIX}user_apply.channel_id,
                {$this->DB_PREFIX}user_apply.searchword,
                {$this->DB_PREFIX}user_apply.interviewurl,
                {$this->DB_PREFIX}user_apply.introducermobile,
                {$this->DB_PREFIX}user_apply.applyreason,
                {$this->DB_PREFIX}user_apply.applytime,
                {$this->DB_PREFIX}user_apply.auditor_system_user_id,
                {$this->DB_PREFIX}user_apply.auditorreason,
                {$this->DB_PREFIX}user_apply.auditortime,
                {$this->DB_PREFIX}user_apply.remark,
                {$this->DB_PREFIX}user_apply.status,
                {$this->DB_PREFIX}user_apply.affiliation_system_user_id,
                {$this->DB_PREFIX}user_apply.affiliation_channel_id,
                {$this->DB_PREFIX}user_apply.to_system_user_id";
        $result['data'] = D('UserApply')->getList($data, $field, 'applytime desc', $page, $join);
        $result['count'] = D('UserApply')->getCount($data, $join);
        if(!empty($result['data'])){
            $result['data'] = $this->_applyUserStatus($result['data']);
        }
        $date = time()-60*60*24;   //超过申请时间24小时 变红色
        foreach ($result['data'] as $key => $value) {
            if ($value['applytime']<$date && empty($value['auditortime'])) {
                $value['color'] = 1;
            }else{
                $value['color'] = 0;
            }
            $result['data'][$key] = $value;
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
  |--------------------------------------------------------------------------
  | 申请转入详情
  |--------------------------------------------------------------------------
  | user_id:客户 system_user_id:申请人 channel_id：请选择渠道 applyreason：申请理由不能为空 infoquality：信息质量
  | @author zgt
  */
    public function getApplyUserInfo($data)
    {
        $data = array_filter($data);
        $data = $this->_applyUserWhere($data);
        $join = 'left join __USER__ A ON A.user_id=__USER_APPLY__.user_id';
        $field = "A.realname,A.username,A.qq,A.tel,A.user_id,
                {$this->DB_PREFIX}user_apply.user_apply_id,
                {$this->DB_PREFIX}user_apply.infoquality,
                {$this->DB_PREFIX}user_apply.system_user_id,
                {$this->DB_PREFIX}user_apply.channel_id,
                {$this->DB_PREFIX}user_apply.searchword,
                {$this->DB_PREFIX}user_apply.interviewurl,
                {$this->DB_PREFIX}user_apply.introducermobile,
                {$this->DB_PREFIX}user_apply.applyreason,
                {$this->DB_PREFIX}user_apply.applytime,
                {$this->DB_PREFIX}user_apply.auditor_system_user_id,
                {$this->DB_PREFIX}user_apply.auditorreason,
                {$this->DB_PREFIX}user_apply.auditortime,
                {$this->DB_PREFIX}user_apply.remark,
                {$this->DB_PREFIX}user_apply.status,
                {$this->DB_PREFIX}user_apply.affiliation_system_user_id,
                {$this->DB_PREFIX}user_apply.affiliation_channel_id,
                {$this->DB_PREFIX}user_apply.to_system_user_id";
        $result = D('UserApply')->getFind($data, $field, $join);
        if(!empty($result)){
            $result = $this->_applyUserStatus($result);
        }
        return array('code'=>0, 'data'=>$result);
    }

    /**
     * 申请转入列表 条件处理
     * @param $where
     */
    protected function _applyUserWhere($where)
    {
        if(!empty($where['system_user_id'])){
            unset($where['admin_system_user_id']);
        }
        if (!empty($where["key_name"]) && !empty($where["key_value"])) {
            if ($where["key_name"] == 'username'){
                $where["username"] = encryptPhone(trim($where["key_value"]), C('PHONE_CODE_KEY'));
            }else{
                $where[$where["key_name"]] = array('like', '%' . $where["key_value"] . '%');
            }
        }
        unset($where["key_name"]);unset($where["key_value"]);
        foreach($where as $k=>$v){
            if($k=='admin_system_user_id'){
                //获取下及职位相关员工
                $ids = D('SystemUser','Service')->getRoleSystemUser($where['admin_system_user_id']);
                if($ids['code']==0){
                    $where["{$this->DB_PREFIX}user_apply.system_user_id"] = array('IN', $ids['data']);
                }
            }elseif($k=='zone_id'){
                //限制区域级别
                $zoneIds = D('Zone', 'Service')->getZoneIds($this->system_user['zone_id']);
                foreach ($zoneIds['data'] as $key => $value) {
                    $zidString[] = $value['zone_id'];
                }
                $where['A.zone_id'] = array("IN", $zidString);
            }elseif($k=='username' || $k=='qq' || $k=='tel' || $k=='realname'){
                $where["A.".$k] = $v;
            }elseif($k!=='page'){
                $where["{$this->DB_PREFIX}user_apply.".$k] = $v;
            }
            unset($where[$k]);
        };
        return $where;
    }

    /**
    * 申请转入列表 状态处理
    */
    protected function _applyUserStatus($array=null)
    {
        if(empty($array[0])){
            $arrStr[0] = $array;
        }else{
            $arrStr = $array;
        }
        //客户审核状态
        $user_status = C('FIELD_STATUS.USER_APPLY_STATUS');
        //信息质量
        $user_infoquality = C('FIELD_STATUS.USER_INFOQUALITY');
        foreach($arrStr as $k=>$v){
            $arrStr[$k]['system_realname'] = '';
            $arrStr[$k]['auditor_realname'] = '';
            $arrStr[$k]['affiliation_realname'] = '';
            $arrStr[$k]['last_realname'] = '';
            $arrStr[$k]['channel_names'] = '';
            $arrStr[$k]['affiliation_channel_names'] = '';
            if(!empty($v['system_user_id'])){
                $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
                $arrStr[$k]['system_realname'] = $systemUser['data']['realname'];
                $arrStr[$k]['system_sex'] = $systemUser['data']['sex'];
                $arrStr[$k]['system_face'] = $systemUser['data']['face'];
            }
            if(!empty($v['auditor_system_user_id'])){
                $auditorUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['auditor_system_user_id']));
                $arrStr[$k]['auditor_realname'] = $auditorUser['data']['realname'];
                $arrStr[$k]['auditor_sex'] = $auditorUser['data']['sex'];
                $arrStr[$k]['auditor_face'] = $auditorUser['data']['face'];
            }
            if(!empty($v['affiliation_system_user_id'])){
                $affiliationUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['affiliation_system_user_id']));
                $arrStr[$k]['affiliation_realname'] = $affiliationUser['data']['realname'];
                $arrStr[$k]['affiliation_sex'] = $affiliationUser['data']['sex'];
                $arrStr[$k]['affiliation_face'] = $affiliationUser['data']['face'];
            }
            if(!empty($v['to_system_user_id'])){
                $lastUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['to_system_user_id']));
                $arrStr[$k]['last_realname'] = $lastUser['data']['realname'];
                $arrStr[$k]['last_sex'] = $lastUser['data']['sex'];
                $arrStr[$k]['last_face'] = $lastUser['data']['face'];
            }
            if(!empty($v['channel_id'])){
                $channel = D('Channel','Service')->getChannelInfo(array('channel_id'=>$v['channel_id']));
//                if($channel['data']['pid']!=0){
//                    $channel_parent = D('Channel','Service')->getChannelInfo(array('channel_id'=>$channel['data']['pid']));
//                    $arrStr[$k]['channel_names'] = $channel_parent['data']['channelname'].'-'.$channel['data']['channelname'];
//                }else{
                    $arrStr[$k]['channel_names'] = $channel['data']['channelname'];
//                }
            }
            if(!empty($v['affiliation_channel_id'])){
                $channel = D('Channel','Service')->getChannelInfo(array('channel_id'=>$v['affiliation_channel_id']));
//                if($channel['data']['pid']!=0){
//                    $channel_parent = D('Channel','Service')->getChannelInfo(array('channel_id'=>$channel['data']['pid']));
//                    $arrStr[$k]['affiliation_channel_names'] = $channel_parent['data']['channelname'].'-'.$channel['data']['channelname'];
//                }else{
                    $arrStr[$k]['affiliation_channel_names'] = $channel['data']['channelname'];
//                }
            }
            if(!empty($v['applytime']) && $v['applytime']!=0)$arrStr[$k]['apply_time'] = date('Y-m-d H:i', $v['applytime']);
            if(!empty($v['auditortime']) && $v['auditortime']!=0)$arrStr[$k]['auditor_time'] = date('Y-m-d H:i', $v['auditortime']);
            if(!empty($v['status']) && $v['status']!=0)$arrStr[$k]['status_name'] = $user_status[$v['status']];
            if(!empty($v['infoquality']) && $v['infoquality']!=0){
                $arrStr[$k]['infoquality_name'] = $user_infoquality[$v['infoquality']];
            }elseif($v['infoquality']==0){
                $arrStr[$k]['infoquality_name'] = 'D';
            }
            if(!empty($v['username']))$arrStr[$k]['username'] = decryptPhone($v['username'], C('PHONE_CODE_KEY'));
            if($v['searchword']=='0') $arrStr[$k]['searchword'] = '无';
            if($v['interviewurl']=='0') $arrStr[$k]['interviewurl'] = '无';
        }
        if(empty($array[0])){
            return $arrStr[0];
        }else{
            return $arrStr;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 获得指定条件的最新一条申请转入记录
    |--------------------------------------------------------------------------
    | @author cq
    */
    public function getApplyRecord($where, $field='*'){
        $DB_PREFIX = C('DB_PREFIX');
        return D('UserApply')->field($field)->where($where)->order("{$DB_PREFIX}user_apply.applytime DESC")->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取申请客户转入详情
    |--------------------------------------------------------------------------
    | @author cq
    */
    public function getApplyUserDetails($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.email",
            "{$DB_PREFIX}user_apply.searchword",
            "{$DB_PREFIX}user_apply.interviewurl",
            "{$DB_PREFIX}user.status as userstatus" ,
            "{$DB_PREFIX}user.infoquality",
            "{$DB_PREFIX}channel.channelname",
            "{$DB_PREFIX}course.coursename",
            "{$DB_PREFIX}user_apply.applytime",
            "{$DB_PREFIX}user_apply.applyreason",
            "{$DB_PREFIX}user_apply.status applystatus",
            "{$DB_PREFIX}user_apply.auditortime",
            "{$DB_PREFIX}user_apply.auditorreason",
            "{$DB_PREFIX}user_apply.introducermobile", //转介绍人手机号
            "{$DB_PREFIX}system_user.system_user_id as apply_system_user_id" ,
            "{$DB_PREFIX}system_user.realname as apply_realname" ,
            "{$DB_PREFIX}system_user.face as apply_face" ,
            "{$DB_PREFIX}system_user.sex as apply_sex" ,
            "A.system_user_id as auditor_system_user_id" ,
            "A.realname as auditor_realname" ,
            "A.face as auditor_face" ,
            "A.sex as auditor_sex",
        );
        $applyDetails = $this->field($field)
            ->join('LEFT JOIN  __USER_APPLY__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__ ON  __CHANNEL__.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ A ON  A.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __COURSE__ ON  __COURSE__.course_id = __USER__.course_id')
            ->where($where)
            ->select();

        return $applyDetails;
    }

    /**
     * 获取审核界面的客户信息
     * @param $where
     * @return mixed
     */
    public function getAuditUserDetails($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.email",
            "{$DB_PREFIX}user.status as userstatus" ,
            "{$DB_PREFIX}user.infoquality",
            "{$DB_PREFIX}channel.channelname",
            "{$DB_PREFIX}course.coursename",
            "{$DB_PREFIX}system_user.realname as apply_realname" , //申请者
            "{$DB_PREFIX}user_apply.user_apply_id", //记录id
            "{$DB_PREFIX}user_apply.system_user_id", //申请人ID
            "{$DB_PREFIX}user_apply.searchword",
            "{$DB_PREFIX}user_apply.interviewurl",
            "{$DB_PREFIX}user_apply.applytime", //申请时间
            "{$DB_PREFIX}user_apply.applyreason",//审核时间
            "{$DB_PREFIX}user_apply.introducermobile", //转介绍人手机号
            "{$DB_PREFIX}user_apply.to_system_user_id", //审核通过后所属员工ID
            "{$DB_PREFIX}user_apply.remark" //备注
        );
        $audioDetails = $this->field($field)
            ->join(' __USER_APPLY__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__ ON  __CHANNEL__.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __COURSE__ ON  __COURSE__.course_id = __USER__.course_id')
            ->where($where)
            ->select();

        return $audioDetails;
    }


    /*
    |--------------------------------------------------------------------------
    | 审核转入操作
    |--------------------------------------------------------------------------
    | user_apply_id:客户 system_user_id:审核人 status：审核状态
    | @author zgt
    */
    public function auditTransfer($data)
    {
        $data = array_filter($data);
        $_time = time();
        //审核人
        $applyData['auditor_system_user_id'] = $this->system_user_id;
        //必要参数
        if(empty($data['user_apply_id']) || empty($data['status'])) return array('code'=>300,'msg'=>'参数异常');
        $applyData['auditortime'] = $_time;
        if($data['status']!=20 && $data['status']!=30) return array('code'=>201,'msg'=>'审核状态异常');
        //获取状态与原因
        $applyData['status'] = $data['status'];
        $applyData['auditorreason'] = $data['auditorreason'];
        //申请信息
        $applyInfo = D('UserApply')->where(array('user_apply_id'=>$data['user_apply_id']))->find();
        //获取客户信息
        $_userInfo = D('User')->getFind(array('user_id'=>$applyInfo['user_id']));
        //操作人
        $_system_user_info = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$applyData['auditor_system_user_id']));
        //审核失败？
        if($data['status']==20){
            $result = D('UserApply')->editData($applyData, $data['user_apply_id']);
            //系统消息提醒
            $add_msg['system_user_id'] = $applyInfo['system_user_id'];
            $add_msg['title'] = '您有一个客户 申请转入 审核失败';
            $add_msg['content'] = "您申请转入的客户 <span class='user_realname' data-id='".$applyInfo['user_id']."'> ".$_userInfo['realname']." </span> 审核失败，审核人：".$_system_user_info['data']['realname'].'。';
            $add_msg['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
            if($result['code'] == 0){
                return array('code'=>0,'msg'=>'审核操作成功');
            }
            return array('code'=>100,'msg'=>'审核操作失败');
        }
        //是否存在预转出人？
        if (!empty($applyInfo['to_system_user_id'])) {
            //获取预转出人信息 区域
            $toSysUser = D('SystemUser')->field('status,zone_id,system_user_id,realname')->where(array('system_user_id'=>$applyInfo['to_system_user_id']))->find();
            $userData['zone_id'] = $toSysUser['zone_id'];
            $userData['system_user_id'] = $applyInfo['to_system_user_id'];
        }else{
            //获取申请人信息 区域
            $systemUser = D('SystemUser')->field('status,zone_id,system_user_id,realname')->where(array('system_user_id'=>$applyInfo['system_user_id']))->find();
            $userData['zone_id'] = $systemUser['zone_id'];
            $userData['system_user_id'] = $systemUser['system_user_id'];
        }
        //客户出库
        $userData['mark'] = 1;
        $userData['status'] = 20;
        $userData['updatetime'] = $applyData['auditortime'];
        $userData['allocationtime'] = $applyData['auditortime'];
        $userData['lastvisit'] = $applyInfo['auditortime'];
        $userData['createuser_id'] = $applyInfo['system_user_id'];
        $userData['updateuser_id'] = $applyInfo['system_user_id'];
        $userData['createtime'] = $applyInfo['applytime'];
        $userData['infoquality'] = $applyInfo['infoquality'];
        $userData['channel_id'] = $applyInfo['channel_id'];
        $userData['nextvisit'] = 0;
        $userData['visittime'] = 0;
        $userData['attitude_id'] = 0;
        $userData['callbacknum'] = 0;
        $userData['waytype'] = 0;
        $userData['reservetype'] = 0;
        $userData['searchword'] = $applyInfo['searchword'];
        $userData['interviewurl'] = !empty($applyInfo['interviewurl'])?$applyInfo['interviewurl']:null;
        $userData['introducermobile'] = !empty($applyInfo['introducermobile'])?decryptPhone($applyInfo['introducermobile'],C('PHONE_CODE_KEY')):null;
        //是否获取新渠道
        $newChannelData = $this->_isNewChannel($userData);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg']);
        $userData = $newChannelData['data'];
        //开启事务
        D()->startTrans();
        $user_flag = D('User')->editData($userData, $applyInfo['user_id']);
        //是否重置 remark
        if (!empty($applyInfo['remark'])) {
            $userInfo['remark'] = $applyInfo['remark'];
            D('UserInfo')->editData($userInfo, $applyInfo['user_id']);
        }
        //数据更新
        $apply_flag = D('UserApply')->editData($applyData, $data['user_apply_id']);
        //隐藏客户旧回访记录 与短信记录
        $this->_heiddenOldInfo($applyInfo['user_id']);
        //添加回访记录
        $data_callback['status'] = 0;
        $data_callback['callbacktype'] = 5;
        $data_callback['user_id'] = $applyInfo['user_id'];
        $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
        $data_callback['system_user_id'] = $applyData['auditor_system_user_id'];
        $data_callback['nexttime'] = $_time;
        $data_callback['remark'] = '审核申请转入操作：申请人-'.$systemUser['realname'].(!empty($toSysUser['realname'])?'，预所属人-'.$toSysUser['realname'].'。':'。');
        $this->_addCallback($data_callback);
        //添加数据记录
        $dataLog['operattype'] = '4';
        $dataLog['operator_user_id'] = $applyData['auditor_system_user_id'];
        $dataLog['user_id'] = $applyInfo['user_id'];
        $dataLog['logtime'] = $_time;
        D('Data', 'Service')->addDataLogs($dataLog);
        //系统消息提醒
        //是否存在预转出人？
        if (!empty($applyInfo['to_system_user_id'])) {
            $add_msg['system_user_id'] = $applyInfo['system_user_id'];
            $add_msg['title'] = '您有一个客户 申请转入 审核成功';
            $add_msg['content'] = "您申请转入的客户 <span class='user_realname' data-id='".$applyInfo['user_id']."'> ".$_userInfo['realname']." </span> 审核成功并转给 ".$toSysUser['realname']."，审核人：".$_system_user_info['data']['realname'].'。';
            $add_msg['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
            $add_msg2['system_user_id'] = $applyInfo['to_system_user_id'];
            $add_msg2['title'] = $systemUser['realname'].'申请转入客户审核成功并将客户转入您库中成';
            $add_msg2['content'] = $systemUser['realname']."申请转入的客户 <span class='user_realname' data-id='".$applyInfo['user_id']."'> ".$_userInfo['realname']." </span> 审核成功并转到您的库中，审核人：".$_system_user_info['data']['realname'].'，请及时跟进。';
            $add_msg2['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg2);
        }else{
            $add_msg['system_user_id'] = $applyInfo['system_user_id'];
            $add_msg['title'] = '您有一个客户 申请转入 审核成功';
            $add_msg['content'] = "您申请转入的客户 <span class='user_realname' data-id='".$applyInfo['user_id']."'> ".$_userInfo['realname']." </span> 审核成功，审核人：".$_system_user_info['data']['realname'].'，请及时跟进。';
            $add_msg['msgtype'] = 1;
            D('Message', 'Service')->sendMsgs($add_msg);
        }
        if( $apply_flag['code']==0 && $user_flag['code']==0 ){
            D()->commit();
            return array('code'=>0,'msg'=>'审核通过');
        }else{
            D()->rollback();
            if($apply_flag['code']!=0){
                return array('code'=>100,'msg'=>$apply_flag['msg']);
            }
            return array('code'=>100,'msg'=>$user_flag['msg']);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function allocationList($param=null){
        $page = !empty($param['page'])?$param['page']:'0,10';
        //限制区域级别
        $zoneIds = D('Zone', 'Service')->getZoneIds($this->system_user['zone_id']);
        foreach ($zoneIds['data'] as $key => $value) {
            $zidString[] = $value['zone_id'];
        }
        $where[C('DB_PREFIX').'user_allocation.zone_id'] = array("IN", $zidString);
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user_allocation.status'] = 1;
        $result['data'] = D('UserAllocation')
            ->field( "user_allocation_id,
                allocationname,
                allocationnum,
                allocation_roles,
                startnum,
                intervalnum,
                week_text,
                {$DB_PREFIX}user_allocation.sort,
                {$DB_PREFIX}user_allocation.channel_id,
                {$DB_PREFIX}user_allocation.zone_id,
                {$DB_PREFIX}user_allocation.createtime,
                channelname,
                weighttype,
                start,
                'specify_days',
                'holiday',
                name")
            ->where($where)
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER_ALLOCATION__.channel_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER_ALLOCATION__.zone_id')
            ->order($DB_PREFIX.'user_allocation.user_allocation_id ASC')
            ->limit($page)
            ->select();
        $result['count'] = D('UserAllocation')->getCount($where);
        //转化状态
        if(!empty($result['data'])){
            foreach($result['data'] as $k=>$v){
                if(!empty($v['channel_id'])){
                    $channel = D('Channel','Service')->getChannelInfo(array('channel_id'=>$v['channel_id']));
                    if($channel['data']['pid']!=0){
                        $channel_parent = D('Channel','Service')->getChannelInfo(array('channel_id'=>$channel['data']['pid']));
                        $result['data'][$k]['channel_names'] = $channel_parent['data']['channelname'].'-'.$channel['data']['channelname'];
                    }else{
                        $result['data'][$k]['channel_names'] = $channel['data']['channelname'];
                    }
                }
                if(!empty($v['allocation_roles'])){
                    $_roles = explode(',',$v['allocation_roles']);
                    $_rolesName = '';
                    foreach($_roles as $v2){
                        $getRole = D('Role','Service')->getRoleInfo(array('role_id'=>$v2));
                        if(empty($_rolesName)){
                            $_rolesName = $getRole['data']['name'];
                        }else{
                            $_rolesName .= ','.$getRole['data']['name'];
                        }
                    }
                }
                $result['data'][$k]['rolenames'] = $_rolesName;
                $result['data'][$k]['create_time'] = date('Y-m-d H:i', $v['createtime']);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 添加分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addAllocation($data){
        $data = array_filter($data);
        if (empty($data['zone_id'])) return array('code'=>301, 'msg'=>'区域不能为空');
        if (empty($data['allocationname'])) return array('code'=>302, 'msg'=>'名称不能为空', 'data'=>'allocationname');
        if ($data['isave'] == 2) {
            $allocationnum = explode(',', $data['allocationnum']);
            foreach ($allocationnum as $key => $value) {
                if ($value[0] == 0) {
                    $allocationnum[$key] = substr($value, 1);
                }
            }
            $data['allocationnum'] = implode(',', $allocationnum);
        }else{
            if ($data['allocationnum'][0] == 0) {
                $data['allocationnum'] = substr($data['allocationnum'], 1);
            }
        }
        if (empty($data['channel_id'])) return array('code'=>304, 'msg'=>'请选择渠道');
        if (empty($data['allocation_roles'])) return array('code'=>305, 'msg'=>'请添加分配职位', 'data'=>'role_name');
        $data['holiday'] = !empty($data['holiday'])?$data['holiday']:null;
        $data['specify_days'] = !empty($data['specify_days'])?$data['specify_days']:null;
        $data['system_user_id'] = $this->system_user_id;
        $data['createtime'] = time();
        if(!empty($data['system_user_ids'])){
            $ids = explode(',',$data['system_user_ids']);
        }else{
            if(!empty($data['allocation_roles'])){
                $roles = explode(',',$data['allocation_roles']);
                $_syswhere["role_id"] = array('IN',$roles);
            }
            $_syswhere["usertype"] = array('NEQ',10);
            $_syswhere["status"] = 1;
            $_syswhere["zone_id"] = $data['zone_id'];
            $systemUserList = D('SystemUser', 'Service')->getSystemUsersList($_syswhere);
            if(!empty($systemUserList['data']['data'])){
                foreach ($systemUserList['data']['data'] as $k => $v) {
                    $ids[] = $v['system_user_id'];
                }
            }
        }
        $role_ids = explode(',', $data['allocation_roles']);
        $_where['role_id'] = array('IN', $role_ids);
        if(!empty($ids)){
            //开启事务
            D()->startTrans();
            $result = D('UserAllocation')->addData($data);
            foreach ($ids as $k=>$v) {
                $_where['user_id'] = $v;
                $sysUserRoles = D('RoleUser')->where($_where)->field('role_id, user_id')->find();
                $addData[] = array('user_allocation_id' => $result['data'],'system_user_id'=>$sysUserRoles['user_id'],'role_id'=>$sysUserRoles['role_id']);
            }
            $rflag_systemUser = D('AllocationSystemuser')->addAll($addData);
        }
        if($result['code']==0 && $rflag_systemUser!==false){
            D()->commit();
        }else{
            D()->rollback();
        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 修改分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editAllocation($data){
        $data = array_filter($data);
        if (empty($data['zone_id'])) return array('code'=>301, 'msg'=>'区域不能为空');
        if (empty($data['allocationname'])) return array('code'=>302, 'msg'=>'名称不能为空', 'data'=>'allocationname');
        if ($data['isave'] == 2) {
            $allocationnum = explode(',', $data['allocationnum']);
            foreach ($allocationnum as $key => $value) {
                if ($value[0] == 0) {
                    $allocationnum[$key] = substr($value, 1);
                }
            }
            $data['allocationnum'] = implode(',', $allocationnum);
        }else{
            if ($data['allocationnum'][0] == 0) {
                $data['allocationnum'] = substr($data['allocationnum'], 1);
            }
        }
        if (empty($data['channel_id'])) return array('code'=>304, 'msg'=>'请选择渠道');
        if (empty($data['allocation_roles'])) return array('code'=>305, 'msg'=>'请添加分配职位', 'data'=>'role_name');
        $data['holiday'] = !empty($data['holiday'])?$data['holiday']:null;
        $data['specify_days'] = !empty($data['specify_days'])?$data['specify_days']:null;
        $data['system_user_id'] = $this->system_user_id;
        //开启事务
        D()->startTrans();
        $result = D('UserAllocation')->editData($data, $data['user_allocation_id']);
        if(!empty($data['system_user_ids'])){
            $ids = explode(',',$data['system_user_ids']);
        }else{
            if(!empty($data['allocation_roles'])){
                $roles = explode(',',$data['allocation_roles']);
                $_syswhere["role_id"] = array('IN',$roles);
            }
            $_syswhere["usertype"] = array('NEQ',10);
            $_syswhere["status"] = 1;
            $_syswhere["zone_id"] = $data['zone_id'];
            $systemUserList = D('SystemUser', 'Service')->getSystemUsersList($_syswhere);
            if(!empty($systemUserList['data']['data'])){
                foreach ($systemUserList['data']['data'] as $k => $v) {
                    $ids[] = $v['system_user_id'];
                }
            }
        }
        if(!empty($ids)){
            D('AllocationSystemuser')->delData($data['user_allocation_id']);
            foreach ($ids as $v) {
                $addData[] = array('user_allocation_id' => $data['user_allocation_id'],'system_user_id'=>$v);
            }
            $rflag_systemUser = D('AllocationSystemuser')->addAll($addData);
        }
        if($result['code']==0 && $rflag_systemUser!==false){
            D()->commit();
        }else{
            D()->rollback();
        }
        return $result;
    }

    /*
     |--------------------------------------------------------------------------
     | 删除分配规则
     |--------------------------------------------------------------------------
     | @author zgt
     */
    public function allocationDel($data){
        $data = array_filter($data);
        if (empty($data['user_allocation_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $result = D('UserAllocation')->delData($data['user_allocation_id']);
        if($result!==false){
            return array('code'=>0,'msg'=>'删除成功');
        }
        return array('code'=>100,'msg'=>'删除失败');
    }


    /*
     |--------------------------------------------------------------------------
     | 修改启动分配规则
     |--------------------------------------------------------------------------
     | @author zgt
     */
    public function allocationStart($data){
        $data = array_filter($data);
        if (empty($data['user_allocation_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $data['start'] = empty($data['start'])?0:$data['start'];
        $result = D('UserAllocation')->editData($data, $data['user_allocation_id']);
        if($result['code']==0){
            return array('code'=>0,'msg'=>'操作成功');
        }
        return $result;
    }

    /*
   |--------------------------------------------------------------------------
   | 查看分配规则
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function allocationDetail($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $result = D('UserAllocation')->getFind($where);
        //分配量状态转换
        $arr_weighttype = C('FIELD_STATUS.ALLOCATION_WEIGHTTYPE');
        $result['weightname'] = $arr_weighttype[$result['weighttype']];
        //是否有分配职位
        if(!empty($result['allocation_roles'])) {
            $allocation_roles = explode(',', $result['allocation_roles']);
            $_allocation_roles = array();
            foreach($allocation_roles as $k=>$v){
                $_allocation_roles[] = $v;
            }
            $role_where['id']= array('IN',$_allocation_roles);
            $role_list = D('Role', 'Service')->getRoleList($role_where);
            $result['roles'] = $role_list['data']['data'];
            if(!empty($result['roles'])){
                foreach ($result['roles'] as $k => $v) {
                    if ($k == 0) $result['roles_name'] = $v['department_name'].''.$v['name'];
                    else $result['roles_name'] .= ',' . $v['department_name'].'.'.$v['name'];
                }
            }
            $result['systemuser'] = D('AllocationSystemuser')->getList(array('user_allocation_id'=>$result['user_allocation_id']));
            $systemUserIds = $realnames = null;
            foreach ($result['systemuser'] as $key => $value) {
                $systemuserInfo = D('SystemUser', 'Service')->getSystemUsersInfo(array('system_user_id'=>$value['system_user_id']));
                if (!empty($realnames)) {
                    $realnames = $realnames.",".$systemuserInfo['data']['realname'];
                    $systemUserIds = $systemUserIds.",".$systemuserInfo['data']['system_user_id'];
                }else{
                    $realnames = $systemuserInfo['data']['realname'];
                    $systemUserIds = $systemuserInfo['data']['system_user_id'];
                }
            }
            $result['systemuser_names'] = $realnames;
            $result['systemuser_ids'] = $systemUserIds;

        }
        return array('code'=>0, 'data'=>$result);
    }


    /*
   |--------------------------------------------------------------------------
   | 回收规则
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function abandonList($param=null){
        $page = !empty($param['page'])?$param['page']:'0,10';
        //限制区域级别
        $zoneIds = D('Zone', 'Service')->getZoneIds($this->system_user['zone_id']);
        foreach ($zoneIds['data'] as $key => $value) {
            $zidString[] = $value['zone_id'];
        }
        $where[C('DB_PREFIX').'user_abandon.zone_id'] = array("IN", $zidString);
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user_abandon.status'] = 1;
        $result['data'] = M('user_abandon')
            ->field(
                "user_abandon_id,
                abandonname,
                {$DB_PREFIX}user_abandon.createtime,
                abandon_roles,
                {$DB_PREFIX}user_abandon.channel_id,
                channelname,
                callbacknum,
                attaindays,
                week_text,
                start,
                'specify_days',
                'holiday',
                {$DB_PREFIX}user_abandon.zone_id,
                name as zonename"
            )
            ->where($where)
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER_ABANDON__.channel_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER_ABANDON__.zone_id')
            ->order('user_abandon_id ASC')
            ->limit($page)
            ->select();
        $result['count'] = D('UserAbandon')->getCount($where);
        //转化状态
        if(!empty($result['data'])){
            foreach($result['data'] as $k=>$v){
                if(!empty($v['channel_id'])){
                    $channel = D('Channel','Service')->getChannelInfo(array('channel_id'=>$v['channel_id']));
                    if($channel['data']['pid']!=0){
                        $channel_parent = D('Channel','Service')->getChannelInfo(array('channel_id'=>$channel['data']['pid']));
                        $result['data'][$k]['channel_names'] = $channel_parent['data']['channelname'].'-'.$channel['data']['channelname'];
                    }else{
                        $result['data'][$k]['channel_names'] = $channel['data']['channelname'];
                    }
                }
                if(!empty($v['abandon_roles'])){
                    $_roles = explode(',',$v['abandon_roles']);
                    $_rolesName = '';
                    foreach($_roles as $v2){
                        $getRole = D('Role','Service')->getRoleInfo(array('role_id'=>$v2));
                        if(empty($_rolesName)){
                            $_rolesName = $getRole['data']['name'];
                        }else{
                            $_rolesName .= ','.$getRole['data']['name'];
                        }
                    }
                }
                $result['data'][$k]['rolenames'] = $_rolesName;
                $result['data'][$k]['create_time'] = date('Y-m-d H:i', $v['createtime']);
            }
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addAbandon($param){
        $param = array_filter($param);
        if (empty($param['abandonname'])) return array('code'=>300, 'msg'=>'名称不能为空', 'data'=>'abandonname');
        if ($param['callbacknum'] == "") return array('code'=>301, 'msg'=>'要求回访次数不能为空', 'data'=>'callbacknum');
        if(!is_numeric($param['callbacknum'])) return array('code'=>201, 'msg'=>'必须为数字', 'data'=>'callbacknum');
        if (empty($param['unsatisfieddays'])) return array('code'=>302, 'msg'=>'未达到要求保护天数不能为空', 'data'=>'unsatisfieddays');
        if (empty($param['attaindays'])) return array('code'=>303, 'msg'=>'达到要求保护天数不能为空', 'data'=>'attaindays');
        if (empty($param['zone_id'])) return array('code'=>304, 'msg'=>'区域不能为空');
        if (empty($param['channel_id'])) return array('code'=>305, 'msg'=>'请选择渠道');
        if (empty($param['abandon_roles'])) return array('code'=>306, 'msg'=>'请添加回收职位', 'data'=>'role_name');
        $param['holiday'] = !empty($param['holiday'])?$param['holiday']:null;
        $param['specify_days'] = !empty($param['specify_days'])?$param['specify_days']:null;
        $param['system_user_id'] = $this->system_user_id;
        $param['createtime'] = time();
        return D('UserAbandon')->addData($param);
    }

    /*
    |--------------------------------------------------------------------------
    | 修改回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editAbandon($param){
        $param = array_filter($param);
        if(empty($param['user_abandon_id'])) return array('code'=>300, 'msg'=>'参数异常');
        if (empty($param['abandonname'])) return array('code'=>301, 'msg'=>'名称不能为空', 'data'=>'abandonname');
        if ($param['callbacknum'] == "") return array('code'=>302, 'msg'=>'要求回访次数不能为空', 'data'=>'callbacknum');
        if (empty($param['unsatisfieddays'])) return array('code'=>303, 'msg'=>'未达到要求保护天数不能为空', 'data'=>'unsatisfieddays');
        if (empty($param['attaindays'])) return array('code'=>304, 'msg'=>'达到要求保护天数不能为空', 'data'=>'attaindays');
        if (empty($param['zone_id'])) return array('code'=>305, 'msg'=>'区域不能为空');
        if (empty($param['channel_id'])) return array('code'=>306, 'msg'=>'请选择渠道');
        if (empty($param['abandon_roles'])) return array('code'=>307, 'msg'=>'请添加回收职位', 'data'=>'role_name');
        $param['holiday'] = !empty($param['holiday'])?$param['holiday']:null;
        $param['specify_days'] = !empty($param['specify_days'])?$param['specify_days']:null;
        $param['system_user_id'] = $this->system_user_id;
        return D('UserAbandon')->editData($param, $param['user_abandon_id']);
    }

    /*
    |--------------------------------------------------------------------------
    | 查看回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function abandonDetail($where){
        if(empty($where['user_abandon_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $result = D('UserAbandon')->getFind($where);
        $_rolesName = null;
        if(!empty($result['abandon_roles'])) {
            $abandon_roles = explode(',', $result['abandon_roles']);
            foreach($abandon_roles as $v){
                $re_data = D('Role','Service')->getRoleInfo(array('role_id'=>$v['role_id']));
                if(!empty($re_data['data'])) {
                    if(empty($_rolesName)){
                        $_rolesName = $re_data['data']['department_name'].'/'.$re_data['data']['name'];
                    }else{
                        $_rolesName .= ','.$re_data['data']['department_name'].'/'.$re_data['data']['name'];
                    }
                }
            }
        }
        $result['roles_name'] = $_rolesName;;
        return $result;
    }

    /*
     |--------------------------------------------------------------------------
     | 删除回收规则
     |--------------------------------------------------------------------------
     | @author zgt
     */
    public function abandonDel($data){
        $data = array_filter($data);
        if (empty($data['user_abandon_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $result = D('UserAbandon')->delData($data['user_abandon_id']);
        if($result!==false){
            return array('code'=>0,'msg'=>'删除成功');
        }
        return array('code'=>100,'msg'=>'删除失败');
    }


    /*
     |--------------------------------------------------------------------------
     | 修改启动回收规则
     |--------------------------------------------------------------------------
     | @author zgt
     */
    public function abandonStart($data){
        $data = array_filter($data);
        if (empty($data['user_abandon_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $data['start'] = empty($data['start'])?0:$data['start'];
        $result = D('UserAbandon')->editData($data, $data['user_abandon_id']);
        if($result['code']==0){
            return array('code'=>0,'msg'=>'操作成功');
        }
        return $result;
    }

    /**
     * 参数过滤
     * @author zgt
     */
    protected function _addCallback($data)
    {
        //数据添加
        $user = D('User')->field('user_id,status')->where(array('user_id'=>array('IN', $data['user_id'])))->select();
        if(empty($user)) return array('code'=>200,'msg'=>'查找不到客户信息');
        //启动事务
        D()->startTrans();
        foreach($user as $k=>$v){
            $data_user['attitude_id'] = $data['attitude_id'];
            $data_user['nextvisit'] = $data['nexttime'];
            $data_user['callbacktype'] = $data['callbacktype'];
            $data['callbacktime'] = !empty($data['nexttime'])?$data['nexttime']:time();
            $data_user['lastvisit'] = $data['callbacktime'];
            $reflag_save = D('User')->where(array('user_id'=>$v['user_id']))->save($data_user);
            if($reflag_save===false) return false;
            //获取新增数据集合
            $add_callback[$k] = $data;
            $add_callback[$k]['user_id'] = $v['user_id'];
        }
        //批量新增回访
        $reflag = D('UserCallback')->addAll($add_callback);
        if($reflag!==false && $reflag_save!==false){
            D()->commit();
            return array('code'=>0,'msg'=>'添加成功');
        }else{
            D()->rollback();
            return array('code'=>1,'msg'=>'数据添加失败');
        }
    }

    /**
     * 参数过滤
     * @author zgt
     */
    protected function _dispostWhere($where)
    {
        $where = array_filter($where);
        $systemType = !empty($where['system_type'])?$where['system_type']:'system_user_id';
        unset($where['system_type']);
        foreach($where as $k=>$v){
            if($k=='role_id'){
                $re_role = D('Role','Service')->getRoleUser(array('role_id'=>$v));
                $sys_ids = $re_role['data'];
            }elseif($v!='0'){
                $where["{$this->DB_PREFIX}user.".$k] = $v;
            }
            unset($where[$k]);
        }
        if(!empty($where["{$this->DB_PREFIX}user.system_user_id"])){
            $system_user_id = $where["{$this->DB_PREFIX}user.system_user_id"];
            unset($where["{$this->DB_PREFIX}user.system_user_id"]);
            $where["{$this->DB_PREFIX}user.".$systemType] = $system_user_id;
        }elseif(!empty($sys_ids)){
            $where["{$this->DB_PREFIX}user.".$systemType] = array('IN', $sys_ids);
        }
        if(!empty($where["{$this->DB_PREFIX}user.zone_id"])){
            $zoneIdArr = $this->getZoneIds($where["{$this->DB_PREFIX}user.zone_id"]);
            if(!empty($zoneIdArr)){
                $where[$this->DB_PREFIX.'user.zone_id'] = array('IN',$zoneIdArr);
            }
            unset($where['zone_id']);
        }
        if (!empty($where["{$this->DB_PREFIX}user.key_name"]) && !empty($where["{$this->DB_PREFIX}user.key_value"])) {
            if ($where["{$this->DB_PREFIX}user.key_name"] == 'username'){
                $where["{$this->DB_PREFIX}user.username"] = encryptPhone(trim($where["{$this->DB_PREFIX}user.key_value"]), C('PHONE_CODE_KEY'));
            }else{
                $where["{$this->DB_PREFIX}user.".$where["{$this->DB_PREFIX}user.key_name"]] = array('like', '%' . $where["{$this->DB_PREFIX}user.key_value"] . '%');
            }
        }
        unset($where["{$this->DB_PREFIX}user.key_name"]);
        unset($where["{$this->DB_PREFIX}user.key_value"]);
        if(!empty($where["{$this->DB_PREFIX}user.channel_id"])){
            $channelIdArr = $this->getChannelIds($where["{$this->DB_PREFIX}user.channel_id"]);
            if(!empty($channelIdArr)){
                $where[$this->DB_PREFIX.'user.channel_id'] = array('IN',$channelIdArr);
            }
            unset($where['channel_id']);
        }
        return $where;
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
     * 渠道ID 获取子集包括自己的集合
     * @author zgt
     */
    protected function getChannelIds($channel_id)
    {
        $channelIds = D('Channel','Service')->getChannelChildren(array('channel_id'=>$channel_id));
        $channelIdArr = array();
        foreach($channelIds['data'] as $k=>$v){
            $channelIdArr[] = $v['channel_id'];
        }
        return $channelIdArr;
    }
    /*
    * 参数处理 QQ username tel introducermobile interviewurl
    * @author zgt
    * @return false
    */
    protected function _checkField($data)
    {
        if(!empty($data['user_id'])){
            $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        }
        //验证手机号码是否有修改
        if(!empty($data['username'])){
            if( !empty($user) && $user['username']==encryptPhone($data['username'], C('PHONE_CODE_KEY')) ){
                unset($data['username']);
            }else{
                $data['username'] = trim($data['username']);
                $username = $data['username'];
                if(!$this->checkMobile($data['username'])) return array('code'=>200,'msg'=>'手机号码格式有误','sign'=>'username');
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                $isusername = D('User')->where(array('username'=>array(array('eq',$data['username']),array('eq',$username0),'OR')))->find();
                if(!empty($isusername)) return array('code'=>201,'msg'=>'手机号码已存在');
                //获取手机号码归属地
                $reApi = phoneVest($username);
                if(!empty($reApi)) {
                    $data['phonevest'] = $reApi['city'];
                }else{
                    $data['phonevest'] = '';
                }
            }
        }
        //验证固定电话是否有修改
        if(!empty($data['tel'])){
            if( !empty($user) && $user['tel']==$data['tel'] ) {
                unset($data['tel']);
            }else{
                $data['tel'] = trim($data['tel']);
                $arr_tel = explode('-',$data['tel']);
                if (!$this->checkTel($data['tel'])) return array('code' => 202, 'msg' => '固定号码格式有误', 'sign' => 'tel');
                if(count($arr_tel)>1){
                    $is_mobile_prefix = D('MobilePrefix')->getFind(array('number'=>$arr_tel[0]));
                    if(empty($is_mobile_prefix)) return array('code' => 206, 'msg' => '该固定电话 区号 不存在', 'sign' => 'tel');
                }
                $istel = D('User')->where(array('tel' => $data['tel']))->find();
                if (!empty($istel)) return array('code' => 203, 'msg' => '固定电话已存在');
            }
        }
        //验证QQ号码是否有修改
        if(!empty($data['qq'])){
            if( !empty($user) && $user['qq']==$data['qq'] ) {
                unset($data['qq']);
            }else{
                $data['qq'] = trim($data['qq']);
                if (!$this->checkInt($data['qq'])) return array('code' => 204, 'msg' => 'qq格式有误', 'sign' => 'qq');
                if(5<strlen($data['qq']) || strlen($data['qq'])>20)  return array('code' => 208, 'msg' => 'qq长度只能在5位到20之间', 'sign' => 'qq');
                $isqq = D('User')->where(array('qq' => $data['qq']))->find();
                if (!empty($isqq)) return array('code' => 205, 'msg' => 'qq号码已存在');
                if (empty($data['email']) && !empty($user['email'])) $data['email'] = $data['qq'] . '@qq.com';
            }
        }

        return array('code'=>0,'data'=>$data);
    }


    /**
     * 是否获取新渠道
     * @author zgt
     */
    protected function _isNewChannel($data)
    {
        //转介绍人获取渠道
        if(!empty($data['introducermobile'])) {
            if( !empty($user) && $user['introducermobile']==$data['introducermobile'] ) {
                unset($data['introducermobile']);
            }else{
                if($this->checkMobile($data['introducermobile'])!==false) $data['introducermobile'] = encryptPhone($data['introducermobile'], C('PHONE_CODE_KEY'));
                else  return array('code'=>212,'msg'=>'转介绍人手机号码格式有误','sign'=>'introducermobile');
                $introducer = D('User')->where(array('username'=>$data['introducermobile']))->find();
                if(!empty($introducer['channel_id'])) $data['channel_id'] = $introducer['channel_id'];
            }
        }
        //通过咨询地址获取 渠道与推广ID
        if(!empty($data['interviewurl'])){
            if( !empty($user) && $user['interviewurl']==$data['interviewurl'] ) {
                unset($data['interviewurl']);
            }else{
                $valueUrl = $data['interviewurl'];
                preg_match("/promote[=|\/]([0-9]*)/", $valueUrl, $promote);
                if(!empty($promote[1])){
                    $promoteInfo = D('Promote')
                        ->field('channel_id')
                        ->where(array('promote_id'=>$promote[1]))
                        ->join("__PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
                        ->find();
                    if(!empty($promoteInfo['channel_id'])){
                        $data['channel_id'] = $promoteInfo['channel_id'];
                        $data['promote_id'] = $promote[1];
                    }
                }
            }
        }
        return array('code'=>0,'data'=>$data);
    }

    /**
     * 该客户是否在申请转入审核中
     * @author zgt
     */
    protected function _isApply($user_id)
    {
        return D('UserApply')->getFind(array('user_id'=>$user_id,'status'=>10),'user_id');
    }

    /*
    * 隐藏客户旧数据
    * @author zgt
    */
    protected function _heiddenOldInfo($user_id){
        //隐藏历史回访记录
        $data['status'] = 0;
        $where['user_id'] = array('IN',$user_id);
        D('UserCallback')->where($where)->save($data);
        //隐藏短信发送记录
        $sms_data['display'] = 0;
        $sms_where['touser_id'] = array('IN',$user_id);
        D('SmsLogs')->where($sms_where)->save($sms_data);
    }

    /**
     * 添加设置模板
     * @author   Nxx
     */
    public function createSetPages($setPages)
    {
        if (!$setPages['pagesname']) {
            return array('code'=>301, 'msg'=>'请填写模板名称');
        }
        $setPages['system_user_id'] = $this->system_user_id;
        $setPages['type'] = $type;
        if ($setPages['type'] == 2) {
            if (!$setPages['channel_id']) {
                return array('code'=>302, 'msg'=>'请选择渠道');
            }
        }
        if (!$setPages['sign']) {
            return array('code'=>303, 'msg'=>'请至少选择1个表头');
        }
        $setPages['sign'] = explode(',', $setPages['sign']);

        foreach ($setPages['sign'] as $key => $sign) {
            $setPages['sign'][$key] = explode('-', $sign);
            $array[] = $setPages['sign'][$key][1];
        }
        if (!in_array('username', $array) && !in_array('qq', $array) && !in_array('tel', $array)) {
            return array('code'=>304, 'msg'=>'手机-QQ-固话至少有一个');
        }
        $set['system_user_id'] = $setPages['system_user_id'];
        $set['pagesname'] = $setPages['pagesname'];
        $set['type'] = $setPages['type'];
        $set['status'] = 1;
        if ($setPages['type'] == 2) {
            $set['channel_id'] = $setPages['channel_id'];
        }
        $result = D('Setpages')->getFind($set);
        if ($result) {
            return array('code'=>201, 'msg'=>'模板名已存在');
        }
        $set['createtime'] = time();
        foreach ($setPages['sign'] as $key => $pages) {
            $arr[] = $pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
            return array('code'=>202, 'msg'=>'请不要重复选择表头');
        }
        $setpages_id = M('setpages')->data($set)->add();
        if (!$setpages_id) {
            return array('code'=>203, 'msg'=>'模板添加失败');
        }
        foreach ($setPages['sign'] as $key => $pages) {
            $page['pagehead'] = strtoupper($pages[0]);
            $page['headname'] = $pages[1];
            $page['setpages_id'] = $setpages_id;
            $result = M("setpageinfo")->data($page)->add();
            if (!$result) {
                $updat = D('Setpages')->where("setpages_id = $setpages_id")->delete();
                return array('code'=>204, 'msg'=>'模板表头设置失败');
            }
        }
        return array('code'=>0, 'data'=>$setpages_id);
    }

    /*
    导出客户
    */
    public function outputUser($request)
    {
        session('outputUser_path',null);
        if (!$request['setpages_id']) {
            $this->error("请选择模板");
        }
        $res = D('Proid', 'Service')->getSetPagesInfos($request['setpages_id']);
        $letters = $res['data'];
        unset($request['setpages_id']);
        $request['status'] = isset($request['status']) ? $request['status'] : 0;
        if ($request['status'] == 0) {
            $where[C('DB_PREFIX') . 'user.status'] = array('IN', array(20, 30, 70, 160));
            unset($request['status']);
        }     
        
        foreach ($request as $k => $v) {
            if (!empty($request[$k])) {
                if ($k == 'allocationtime' || $k == 'updatetime' || $k == 'createtime' || $k == 'lastvisit' || $k == 'nextvisit' || $k == 'visittime') {
                    $_time = explode('@', str_replace('/', '-', $request[$k]));
                    $where[C('DB_PREFIX') . 'user.' . $k] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');    
                } elseif (!empty($request['key_name']) && !empty($request['key_value'])) {
                    if ($request['key_name'] == 'username') $where[C('DB_PREFIX') . 'user.' . $request['key_name']] = encryptPhone(trim($request['key_value']), C('PHONE_CODE_KEY'));
                    else $where[C('DB_PREFIX') . 'user.' . $request['key_name']] = array('like', '%' . $request['key_value'] . '%');
                } elseif ($k == 'channel_id') {
                    $res = $channelMain->getChannelIds($request['channel_id']);
                    $channelIds = $res['data'];
                    foreach($channelIds as $k=>$v){
                        $newIds[] = $v['channel_id'];
                    }
                    $where[C('DB_PREFIX') . 'user.channel_id'] = array('IN',$newIds);
                }elseif ($k != 'type') {
                    if ($k == 'studytype') $where[C('DB_PREFIX') . 'fee.studytype'] = $v;
                    else $where[C('DB_PREFIX') . 'user.' . $k] = $v;
                }
            }
        }
        foreach ($letters as $k1 => $value) {
            $letter[$k1] = $value['pagehead'];
            $heads[$k1] = $value['headname'];
        }
        foreach ($heads as $k2 => $head) {
            $heads_val[] = L($head);
        }               
        $zone_id = $this->system_user['zone_id'];
        $zoneIds = D('Zone', 'Service')->getZoneIds($zone_id);
        foreach ($zoneIds['data'] as $key => $value) {
            if ($zoneIdString) {
                $zoneIdString = $zoneIdString.",".$value['zone_id'];
            }else{
                $zoneIdString = $value['zone_id'];
            }
        }
        $where[C('DB_PREFIX') . 'user.zone_id'] = array("IN", $zoneIdString);  
        if(in_array('course',$heads))
        {
             $all_course_tmp=M("Course")->field('course_id,coursename')->select();
             $all_course=array();
             foreach($all_course_tmp as $k=>$v)
             {
                 $all_course[$v['course_id']]=$v['coursename'];                  
             }
        }
        $userList = D('User')->where($where)->field($heads)->limit('0,30000')->select();
        if (!$userList) {
            session('outputUser_path',array('code'=>4, 'msg'=>'此条件下没有数据可导出！'));
            exit;
        }
        foreach ($userList as $key => $user) {
            if(in_array('mark',$heads))
            {
                if ($user['mark'] == 1) {
                    $user['mark'] = '普通';
                } else {
                    $user['mark'] = '重点';
                }
            }
            if(in_array('reservetype',$heads))
            {
                if ($user['reservetype'] == 10) {
                    $user['reservetype'] = '审核中';
                } elseif ($user['reservetype'] == 20) {
                    $user['reservetype'] = '审核失败';
                }elseif ($user['reservetype'] == 30){
                    $user['reservetype'] = '审核通过';
                }
            }
            if(in_array('username',$heads))
            {
                if ($user['username']) {
                    $user['username'] = decryptPhone($user['username'], C('PHONE_CODE_KEY'));
                }
            }
            if(in_array('status',$heads))
            {
                if ($user['status'] == 20) {
                    $user['status'] = '待联系';
                } elseif ($user['status'] == 30) {
                    $user['status'] = '待跟进';
                } elseif ($user['status'] == 70) {
                    $user['status'] = '交易';
                }elseif ($user['status'] == 160) {
                    $user['status'] = '回库';
                }else{
                    $user['status'] = '其他';
                }
            }
            if(in_array('infoquality',$heads))
            {
                if ($user['infoquality'] == 1) {
                    $user['infoquality'] = 'A';
                } elseif ($user['infoquality'] == 2) {
                    $user['infoquality'] = 'B';
                } elseif ($user['infoquality'] == 3) {
                    $user['infoquality'] = 'C';
                } elseif ($user['infoquality'] == 4) {
                    $user['infoquality'] = 'D';
                }
            }
            if(in_array('visittime',$heads))
            {
                if ($user['visittime']) {
                    $user['visittime'] = date('Y-m-d H:i:s', $user['visittime']);
                }
            }
            if(in_array('updatetime',$heads))
            {
                if ($user['updatetime']) {
                    $user['updatetime'] = date('Y-m-d H:i:s', $user['updatetime']);
                }
            }
            if(in_array('allocationtime',$heads))
            {
                if ($user['allocationtime']) {
                    $user['allocationtime'] = date('Y-m-d H:i:s', $user['allocationtime']);
                }
            }
            if(in_array('createtime',$heads))
            {
                if ($user['createtime']) {
                    $user['createtime'] = date('Y-m-d H:i:s', $user['createtime']);
                }
            }
            if(in_array('createtime',$heads))
            {
                if ($user['lastvisit']) {
                    $user['lastvisit'] = date('Y-m-d H:i:s', $user['lastvisit']);
                }
            }
            if(in_array('nextvisit',$heads))
            {
                if ($user['nextvisit']) {
                    $user['nextvisit'] = date('Y-m-d H:i:s', $user['nextvisit']);
                }
            }
            if(in_array('course',$heads))
            {
                if ($user['course_id']) {
                    $user['course'] = $all_course[$user['course_id']];
                }
            }
            if(in_array('createname',$heads))
            {
                if (!$user['createname']) {
                    $user['createname'] = "系统创建";
                }
            }
            if(in_array('learningtype',$heads))
            {
                if ($user['learningtype'] == 1) {
                    $user['learningtype'] = '泽林';
                } elseif ($user['learningtype'] == 2) {
                    $user['learningtype'] = '8点1课';
                } else {
                    $user['learningtype'] = '其他';
                }
            }
            if(in_array('attitude_id',$heads))
            {
                $result = C("USER_ATTITUDE");
                foreach ($result as $k12 => $value) {
                    if ($user['attitude_id'] == $k12) {
                        $user['attitude_id'] = $value['text'];
                    }
                }
            }
            foreach ($heads as $k3 => $head) {
                $newArr[$key][$k3] = $user[$head];
            }
        }
        $newArr = array_chunk($newArr, 5000);
        $k = count($newArr); 
        for ($i=0; $i < $k; $i++) {
            $arr = $newArr[$i];
            $cache_type = 3;
            $res[] = outExecls('user', $heads_val, $arr, $letter, $cache_type);
        }
        $name = "$setpages[system_user_id]".date("Ymdhis");
        foreach ($res as $key => $value) {
            $dirFiles[] = "./Uploads/excel/{$value}";
        } 
        create_zip($dirFiles,"./Uploads/excel/{$name}.zip");
        $path = "/Uploads/excel/{$name}.zip"; 
        if (!empty($path)) {
            session('outputUser_path',array('code'=>0, 'msg'=>'导出成功！','data'=>$path));
            exit();
        }
    }
    
    /**
    导入客户
    */
    public function inputUser($request, $datas)
    {
        session('faile_import', null);
        session('success_import', null);
        $resSetPages = D('Proid', 'Service')->getSetPages($request);
        $setpagesInfo = $resSetPages['data'];
        $resPagesInfo = D('Proid', 'Service')->getSetPagesInfo($request['setpages_id']);
        $letters = $resPagesInfo['data'];
        foreach ($letters as $k1 => $letter) {
            $k1 = $k1 + 1;
            $users[$k1][] = $letter['pagehead'];
            $users[$k1][] = $letter['headname'];
        }

        /*对生成的数组进行字段对接*/
        foreach ($users as $key => $user) {
            foreach ($datas as $k => $v) {
                if ($k > 1) {
                    $keys = array_keys($v);
                    foreach ($keys as $k2 => $v1) {
                        if ($user[0] == $v1) {
                            $userList[$k - 2]["$user[1]"] = $v[$v1];
                        }
                    }
                }
            }
        }
        //对接完成后转换相应的数据：年龄、邮箱等，去除非法数据
        foreach ($userList as $key => $user) {
            if ($user['username']) {   //手机格式处理
                $user['username'] = str_replace(' ','',$user['username']);
                $num = strlen($user['username']);
                if ($num > 11) {
                    $user['username'] = substr($user['username'], ($num - 11), $num);
                }
            }
            if ($user['qq'] && !$user['email']) {  //邮箱
                $user['email'] = $user['qq'] . '@qq' . '.com';
            }
            if ($user['sex'] == '男') {  //性别
                $user['sex'] = 1;
            } elseif ($user['sex'] == '女') {
                $user['sex'] = 2;
            } else {
                $user['sex'] == 0;
            }
            if ($user['birthday']) {   //出生日期
                $num = strlen($user['birthday']);
                if ($num <= 2) {
                    $b = $user['birthday'];
                    $a = date('Y', strtotime("-{$b} years"));
                    $a = $a . '-01' . '-01';
                    $user['birthday'] = strtotime($a);
                } else {
                    $bir = preg_replace('/[^\d]/', '-', $user['birthday']) . '-' . '01';
                    $user['birthday'] = strtotime($bir);
                }
                //当出现年月字段时未做处理
            }
            if ($user['educationname']) {  //学历处理
                $education_array = C('EDUCATION_ARRAY');
                foreach($education_array as $k=>$v){
                    if($v==$user['educationname']){
                        $user['education_id'] = $k;
                    }
                }
                unset($user['educationname']);
            }
            if (strlen($user['postcode']) != 6) {  //邮编验证
                unset($user['postcode']);
            }
            
            if ($user['wantsalary']) {  //目前薪资
                $user['wantsalary'] = str_replace('万', '0000', $user['wantsalary']);
                $user['wantsalary'] = str_replace('k', '000', $user['wantsalary']);
                $user['wantsalary'] = str_replace('-', '_', $user['wantsalary']);
                $user['wantsalary'] = preg_replace('[\W]', '', $user['wantsalary']);

                $user['wantsalary'] = explode('_', $user['wantsalary']);
                $user['wantsalary'] = max($user['wantsalary']);
            } else {
                $user['wantsalary'] = 0;//  0-表示面议    年薪未处理
            }

            if ($user['workyear']) {  //目前薪资
                $user['workyear'] = explode('.', $user['workyear']);
                if (!empty($user['workyear'][1]) && $user['workyear'][1] >= 5) {
                    $user['workyear'] = $user['workyear'][0] + 1;
                } elseif (!empty($user['workyear'][1]) && $user['workyear'][1] < 5) {
                    $user['workyear'] = $user['workyear'][0];
                } else {
                    $user['workyear'] = str_replace('-', '_', $user['workyear']);
                    $user['workyear'] = preg_replace('[\W]', '', $user['workyear']);
                    $user['workyear'] = explode('_', $user['workyear'][0]);
                    $max = max($user['workyear']);
                    $min = min($user['workyear']);
                    $user['workyear'] = (int)ceil(($max + $min) / 2);
                }
            } else {
                $user['workyear'] = 0;//  0-表示面议
            }
            $user['infoquality'] = 4;   //信息质量不明确
            $user['channel_id'] = $setpagesInfo[0]['channel_id'];
            $user['course_id'] = 0;
            $result = $this->addUser($user);
            if ($result['code'] != 0) {
                $userList[$key]['msg'] = $result['msg'];
                $errorData[$key] = $userList[$key];
                unset($userList[$key]);
                continue;
            }

        }
        session('faile_import', $errorData);
        session('success_import', $userList);
    }

}
