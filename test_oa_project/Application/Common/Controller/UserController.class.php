<?php

namespace Common\Controller;

use Common\Controller\BaseController;
use Common\Service\DataService;
use Common\Service\RedisUserService;

class UserController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 查找用户基础信息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUser($where,$field='*')
    {
        return D('User')->field($field)->where($where)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserInfo($user_id)
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
        $where[$this->DB_PREFIX.'user.user_id'] = $user_id;
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
    | 查找用户列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where, $order=null, $limit=null)
    {
        //参数处理
        $where = $this->dispostWhere($where);
//        $RedisUserService = new RedisUserService();
//        $result = $RedisUserService->getList($where);
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
            "{$this->DB_PREFIX}user.updateuser_id",
            "{$this->DB_PREFIX}user.reservetype",
            "{$this->DB_PREFIX}system_user.system_user_id",
            "{$this->DB_PREFIX}system_user.realname as system_realname",
            "{$this->DB_PREFIX}user.channel_id",
            "A.system_user_id as updateuser_id",
            "A.realname as updateuser_realname"
        );
        $result = D('User')
            ->field($field)
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->order($order)
            ->limit($limit)
            ->select();
        if(!empty($result)){
            //转换客户状态
            $result = $this->userStatus($result);
        }

        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 查询客户列表总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where)
    {
        $where = $this->dispostWhere($where);
        $result = D('User')->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->count();
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function createUser($data)
    {
        $data['allocationtime'] = time();
        $data['updatetime'] = time();
        $data['lastvisit'] = time();
        $data['updatetime'] = time();
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        $data['updateuser_id'] = $data['system_user_id'];
        $data['createuser_id'] = $data['system_user_id'];
        //验证唯一字段 数据处理
        $checkData = $this->checkField($data);
        if($checkData['code']!=0) return array('code'=>$checkData['code'], 'msg'=>$checkData['msg'], 'sign'=>!empty($checkData['sign'])?$checkData['sign']:null);
        $data = $checkData['data'];
        //是否获取新渠道
        $newChannelData = $this->isNewChannel($data);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg'], 'sign'=>!empty($newChannelData['sign'])?$newChannelData['sign']:null);
        $data = $newChannelData['data'];
        //启动事务
        D()->startTrans();
        $reUserId = D('User')->data($data)->add();
        if(!empty($reUserId)){
            $data_info = $data;
            $data_info['user_id'] = $reUserId;
            $reUserInfo = D('UserInfo')->data($data_info)->add();
        }
        //添加数据记录
        $dataLog['operattype'] = '1';
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['system_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $reUserId;
        $dataLog['logtime'] = time();
        $dataController = new DataController();
        $dataController->addDataLogs($dataLog);
        //添加分配记录
        $logs = $this->allocationLogs($data, $data['system_user_id']);
        if(!empty($reUserId) && !empty($reUserInfo) && !empty($logs)){
            D()->commit();
            return array('code'=>0,'msg'=>'客户添加成功','data'=>$reUserId);
        }else{
            D()->rollback();
            return array('code'=>12,'msg'=>'数据添加失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 更新、修改客户
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function editUser($data)
    {
        if(empty($data['system_user_id']) || empty($data['user_id'])) return array('code'=>2,'msg'=>'参数异常');
        //验证唯一字段 数据处理
        $checkData = $this->checkField($data);
        if($checkData['code']!=0) return array('code'=>$checkData['code'], 'msg'=>$checkData['msg'], 'sign'=>!empty($checkData['sign'])?$checkData['sign']:null);
        $data = $checkData['data'];
        //是否获取新渠道
        $newChannelData = $this->isNewChannel($data);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg'], 'sign'=>!empty($newChannelData['sign'])?$newChannelData['sign']:null);
        $data = $newChannelData['data'];
        //数据处理
        $result = D('User')->where(array('user_id'=>$data['user_id']))->save($data);
        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>1,'msg'=>'数据修改失败');
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
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(empty($data['system_user_id']) || empty($data['user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        if($user['system_user_id']!=$data['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能修改该客户信息');
        $userInfo = D('UserInfo')->where(array('user_id'=>$data['user_id']))->find();

        if(!empty($data['identification']) && !$checkform->checkIdcard($data['identification'])) return array('code'=>1,'msg'=>'身份证格式格式错误','sign'=>'identification');
        if(!empty($data['email']) && !$checkform->isEmail($data['email'])) return array('code'=>1,'msg'=>'邮箱格式格式错误','sign'=>'email');
        if(!empty($data['urgentmobile']) && !$checkform->checkMobile($data['urgentmobile'])) return array('code'=>1,'msg'=>'联系人号码格式错误','sign'=>'urgentmobile');

        if(empty($userInfo)){
            $result = D('UserInfo')->data($data)->add();
        }else{
            $result = D('UserInfo')->where(array('user_id'=>$data['user_id']))->save($data);
        }
        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>1,'msg'=>'数据修改失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取回访记录
    |--------------------------------------------------------------------------
    | user_id system_user_id $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function getUserCallback($user_id,$rank=1)
    {
        $where[$this->DB_PREFIX.'user_callback.user_id'] = $user_id;
        if($rank==1) $where[$this->DB_PREFIX.'user_callback.status'] = 1;
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
        $result =  D('UserCallback')
            ->field($field)
            ->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER_CALLBACK__.system_user_id')
            ->where($where)
            ->order($this->DB_PREFIX.'user_callback.callbacktime DESC')
            ->select();
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加回访记录
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function addCallback($data,$rank=1)
    {
        //数据添加
        $user = D('User')->field('user_id,status')->where(array('user_id'=>array('IN', $data['user_id'])))->select();
        if(empty($user)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //启动事务
        D()->startTrans();
        foreach($user as $k=>$v){
            $data_user['attitude_id'] = $data['attitude_id'];
            $data_user['nextvisit'] = $data['nexttime'];
            $data_user['callbacktype'] = $data['callbacktype'];
            if($rank==1){
                //更新客户状态
                if($v['status']==20){
                    $data_user['status'] = 30;
                }
                $data['callbacktime'] = time();
                $data_user['callbacknum'] = array('exp','callbacknum+1');
                $data_user['lastvisit'] = $data['callbacktime'];
                //添加数据记录
                $dataLog['operattype'] = '11';
                $dataLog['operator_user_id'] = $data['system_user_id'];
                $dataLog['system_user_id'] = $data['system_user_id'];
                $dataLog['user_id'] = $data['user_id'];
                $dataLog['logtime'] = time();
                $dataController = new DataController();
                $dataController->addDataLogs($dataLog);
            }else{
                $data['callbacktime'] = !empty($data['nexttime'])?$data['nexttime']:time();
                $data_user['lastvisit'] = $data['callbacktime'];
            }
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

    /*
    |--------------------------------------------------------------------------
    | 获取客户短信记录
    |--------------------------------------------------------------------------
    | $rank:(1普通 2主管)
    | @author zgt
    */
    public function getUserSmsLog($data, $rank=1)
    {
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
    | 该客户是否在申请转入审核中
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function isApply($user_id)
    {
        return D('UserApply')->field('user_id')->where(array('user_id'=>$user_id,'status'=>10))->select();
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
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id']) || empty($data['channel_id']) || empty($data['infoquality'])) return array('code'=>2,'msg'=>'参数异常');
        $data = array_filter($data);
        $checkform = new \Org\Form\Checkform();
        //是否有转介绍人手机号码
        if(!empty($data['introducermobile'])){
            $PHONE_CODE_KEY = C('PHONE_CODE_KEY');
            if($checkform->checkMobile($data['introducermobile'])){
                $data['introducermobile'] = trim($data['introducermobile']);
                $data['introducermobile'] = encryptPhone($data['introducermobile'],$PHONE_CODE_KEY);
            }else{
                $this->ajaxReturn(1,'介绍人手机号不正确');
            }
        }
        //获取用户信息
        $userInfo = D('User')->where(array('user_id'=>$data['user_id']))->find();
        //保存旧用户信息
        $data['affiliation_system_user_id'] = $userInfo['system_user_id'];
        $data['affiliation_channel_id'] = $userInfo['channel_id'];
        $data['applytime'] = time();
        //该客户是否在申请转入审核中
        $userApply = $this->isApply($data['user_id']);
        if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 正在被其他人申请转入中，无法再次申请');
        //客户是否回库状态
        if($userInfo['status'] != 160) return array('code'=>1,'msg'=>'该客户不在回库状态,不能申请');
        //添加记录
        $reflag = D('UserApply')->add($data);
        if($reflag !== false){
            return array('code'=>0,'msg'=>'提交转入申请成功，请等待审核');
        }else{
            return array('code'=>1,'msg'=>'提交转入申请失败');
        }
    }
    /*
    |--------------------------------------------------------------------------
    | 审核申请转入列表
    |--------------------------------------------------------------------------
    | user_apply_id:客户 system_user_id:审核人 status：审核状态
    | @author zgt
    */
    public function getApplyList($where, $order=null, $limit=null)
    {
        //参数处理
        $where = $this->dispostApplyWhere($where);
        $field  = array(
            "{$this->DB_PREFIX}user_apply.user_apply_id",
            "{$this->DB_PREFIX}user_apply.applytime",
            "{$this->DB_PREFIX}user_apply.status as applystatus",
            "{$this->DB_PREFIX}user.user_id",
            "{$this->DB_PREFIX}user.realname",
            "{$this->DB_PREFIX}user.username",
            "{$this->DB_PREFIX}user.qq",
            "{$this->DB_PREFIX}user.tel",
            "{$this->DB_PREFIX}user.status as userstatus" ,
            "{$this->DB_PREFIX}user.infoquality",
            "{$this->DB_PREFIX}user.channel_id",
            "{$this->DB_PREFIX}channel.channelname",
            "{$this->DB_PREFIX}system_user.realname as apply_realname" ,
            "{$this->DB_PREFIX}system_user.system_user_id as apply_system_user_id" ,
            "C.realname as auditor_realname" ,
            "C.system_user_id as auditor_system_user_id",
            "D.channelname as apply_channelname",
            "D.channel_id as apply_channel_id",
            "E.realname as affiliation_realname" ,
            "E.system_user_id as affiliation_system_user_id",
            "F.channelname as affiliation_channelname",
            "F.channel_id as affiliation_channel_id"
        );
        $applyList['data'] = D('UserApply')->field($field)
            ->join('__USER__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__  ON  __CHANNEL__.channel_id = __USER__.channel_id')
//            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__  ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ C ON  C.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ D ON  D.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ E ON  E.system_user_id = __USER_APPLY__.affiliation_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ F ON  F.channel_id = __USER_APPLY__.affiliation_channel_id')
            ->where($where)
            ->limit($limit)
            ->order("{$this->DB_PREFIX}user_apply.applytime DESC")
            ->select();

        $applyList['count'] = D('UserApply')->field($field)
            ->join('__USER__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__  ON  __CHANNEL__.channel_id = __USER__.channel_id')
//            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__  ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ C ON  C.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ D ON  D.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ E ON  E.system_user_id = __USER_APPLY__.affiliation_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ F ON  F.channel_id = __USER_APPLY__.affiliation_channel_id')
            ->where($where)->count();

        return $applyList;
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
        //必要参数
        if(empty($data['user_apply_id']) || empty($data['system_user_id']) || empty($data['status'])) return array('code'=>2,'msg'=>'参数异常');
        //审核人
        $applyData['auditor_system_user_id'] = $data['system_user_id'];
        $_time = time();
        $applyData['auditortime'] = $_time;
        //不同状态处理
        if($data['status'] == 20){
            $applyData['status'] = 20;
            $applyData['auditorreason'] = $data['auditorreason'];
            //数据更新
            $result = D('UserApply')->where(array('user_apply_id'=>$data['user_apply_id']))->save($applyData);
            if($result !== false){
                return array('code'=>0,'msg'=>'审核操作成功');
            }else{
                return array('code'=>1,'msg'=>'审核操作失败');
            }
        }elseif($data['status'] == 30){
            $applyData['status'] = 30;
            //申请信息
            $applyInfo = D('UserApply')->where(array('user_apply_id'=>$data['user_apply_id']))->find();
            //获取申请人信息 区域
            $systemUser = D('SystemUser')->field('status,zone_id,system_user_id,realname')->where(array('system_user_id'=>$applyInfo['system_user_id']))->find();
            $userData['zone_id'] = $systemUser['zone_id'];
            $userData['system_user_id'] = $systemUser['system_user_id'];
            //是否存在预转出人？ 获取预申请人信息 区域
            if (!empty($applyInfo['to_system_user_id'])) {
                $toSysUser = D('SystemUser')->field('status,zone_id,system_user_id,realname')->where(array('system_user_id'=>$applyInfo['to_system_user_id']))->find();
                $userData['zone_id'] = $toSysUser['zone_id'];
                $userData['system_user_id'] = $applyInfo['to_system_user_id'];
            }
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
            $newChannelData = $this->isNewChannel($userData);
            if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg']);
            $userData = $newChannelData['data'];
            //开启事务
            D()->startTrans();
            $userResult = D('User')->where(array('user_id'=>$applyInfo['user_id']))->save($userData);
            //是否重置 remark
            if (!empty($applyInfo['remark'])) {
                $userInfo['remark'] = $applyInfo['remark'];
                D('UserInfo')->where(array('user_id'=>$applyInfo['user_id']))->save($userInfo);
            }
            $applyResult = D('UserApply')->where(array('user_apply_id'=>$applyInfo['user_apply_id']))->save($applyData);
            $callbackData['status'] = 0;
            $callbackResult = D('UserCallback')->where(array('user_id'=>$applyInfo['user_id']))->save($callbackData);
            if($userResult !==false && $applyResult !==false && $callbackResult !==false){
                //添加回访记录
                $data_callback['status'] = 0;
                $data_callback['callbacktype'] = 5;
                $data_callback['user_id'] = $applyInfo['user_id'];
                $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
                $data_callback['system_user_id'] = $data['system_user_id'];
                $data_callback['nexttime'] = $_time;
                $data_callback['remark'] = '审核申请转入操作：申请人-'.$systemUser['realname'].(!empty($toSysUser['realname'])?'，预所属人-'.$toSysUser['realname'].'。':'。');
                $this->addCallback($data_callback,2);
                //添加数据记录
                $dataLog['operattype'] = '4';
                $dataLog['operator_user_id'] = $data['system_user_id'];
                $dataLog['user_id'] = $applyInfo['user_id'];
                $dataLog['logtime'] = time();
                $DataService = new DataService();
                $DataService->addDataLogs($dataLog);

                D()->commit();
                return array('code'=>0,'msg'=>'审核通过');
            }else{
                D()->rollback();
                return array('code'=>1,'msg'=>'审核异常');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 客户放弃
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function abandonUser($data, $rank=1)
    {
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(empty($userList)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v) {
            //是否交易中
            if ($v['status'] == '70') return array('code' => 1, 'msg' => '客户' . $v['realname'] . '状态不予许放弃');
            //普通员工判断归属人
            if ($rank == 1) {
                if ($data['system_user_id'] != $v['system_user_id']) return array('code' => 1, 'msg' => '只有归属人才能分配该客户信息');
            }
        }
        //数据更新
        D()->startTrans();
        $time = time();
        $save_user['status'] = 160;
        $where['user_id'] = array('IN',$data['user_id']);
        $result = D('User')->where($where)->save($save_user);
        if($result!==false){
            //添加回访记录
            $data_callback['status'] = 0;
            $data_callback['user_id'] = $data['user_id'];
            $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $data_callback['system_user_id'] = $data['system_user_id'];
            $data_callback['nexttime'] = $time;
            if($rank==2){
                //批量
                if(count($userList)>1){
                    $data_callback['remark'] = '批量客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 15;
                }else{
                    $data_callback['remark'] = '客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 14;
                }
                //添加数据记录
                $dataLog['operattype'] = '8';
            }else{
                //添加数据记录
                $dataLog['operattype'] = '6';
            }
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['system_user_id'] = $data['system_user_id'];
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $time;
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
            $this->addCallback($data_callback,2);
            D()->commit();
            return array('code'=>0,'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>1,'msg'=>'操作失败');
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
        //必要参数
        if(empty($data['user_id']) || empty($data['tosystem_user_id']) || empty($data['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $_time = time();
        $save_user['mark'] = 1;
        $save_user['nextvisit'] = $_time;
        $save_user['attitude_id'] = 0;
        $save_user['callbacknum'] = 0;
        $save_user['lastvisit'] = $_time;
        $save_user['allocationtime'] = $_time;
        $save_user['system_user_id'] = $data['tosystem_user_id'];
        //获取客户信息与被转出人信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        $_systemInfo = D('SystemUser')->where(array('system_user_id'=>$data['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //普通员工判断归属人
            if($rank==1){
                if($data['system_user_id']!=$v['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
                if($data['tosystem_user_id']==$v['system_user_id']) return array('code'=>1,'msg'=>'无法将客户转给自己哦');
            }
            //该客户是否在申请转入审核中
            $userApply = $this->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        //数据更新
        D()->startTrans();
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
            if($rank==2){
                //批量
                if(count($userList)>1){
                    $data_callback['remark'] = '批量客户转出(管理操作)';
                    $data_callback['callbacktype'] = 11;
                }else{
                    $data_callback['remark'] = '客户转出(管理操作)';
                    $data_callback['callbacktype'] = 10;
                }
            }else{
                $data_callback['remark'] = '客户转出';
                $data_callback['callbacktype'] = 1;
                //添加数据记录
                $dataLog['operattype'] = '5';
                $dataLog['operator_user_id'] = $data['system_user_id'];
                $dataLog['system_user_id'] = $data['tosystem_user_id'];
                $dataLog['user_id'] = $data['user_id'];
                $dataLog['logtime'] = $_time;
                $dataController = new DataController();
                $dataController->addDataLogs($dataLog);
            }
            $this->addCallback($data_callback,2);
            //新增分配记录
            $this->allocationLogs($userList,$data['tosystem_user_id']);
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
        //必要参数
        if(empty($data['user_id']) || empty($data['tosystem_user_id']) || empty($data['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $_time = time();
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
        //获取客户信息与被转出人信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        $_systemInfo = D('SystemUser')->where(array('system_user_id'=>$data['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //普通员工判断归属人
            if($rank==1){
                if($data['system_user_id']!=$v['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
                if($data['tosystem_user_id']==$v['system_user_id']) return array('code'=>1,'msg'=>'无法将客户转给自己哦');
            }
            //该客户是否在申请转入审核中
            $userApply = $this->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        //数据更新
        D()->startTrans();
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
            if($rank==2){
                //批量
                if(count($userList)>1){
                    $data_callback['remark'] = '批量客户出库(管理操作)';
                    $data_callback['callbacktype'] = 13;
                }else{
                    $data_callback['remark'] = '客户出库(管理操作)';
                    $data_callback['callbacktype'] = 12;
                }
            }else{
                $data_callback['remark'] = '客户出库';
                $data_callback['callbacktype'] = 4;
            }
            $this->addCallback($data_callback,2);
            //新增分配记录
            $this->allocationLogs($userList,$data['tosystem_user_id']);
            //添加数据记录
            $dataLog['operattype'] = '3';
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['system_user_id'] = $data['tosystem_user_id'];
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $_time;
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
            //出库隐藏历史回访记录
            $this->heiddenOldInfo($data['user_id']);
            D()->commit();
            return array('code'=>0,'msg'=>'数据出库成功');
        }else{
            D()->rollback();
            return array('code'=>1,'msg'=>'数据出库失败');
        }
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
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id'])  || empty($data['nexttime']) || empty($data['remark'])) return array('code'=>2,'msg'=>'参数异常');
        //该客户是否在申请转入审核中
        $userApply = $this->isApply($data['user_id']);
        if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 正在审核转入中，无法赎回');
        //获取客户信息与被转出人信息
        $userInfo = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->find();
        if($data['system_user_id']!=$userInfo['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
        if($userInfo['status']!=160)  return array('code'=>1,'msg'=>'客户不属于回库状态,无法赎回');
        D()->startTrans();
        $save_data['status'] = 30;
        D('User')->where(array('user_id'=>$data['user_id']))->save($save_data);

        //添加分配记录
        $data_callback['status'] = 1;
        $data_callback['callbacktype'] = 3;
        $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
        $data_callback['user_id'] = $data['user_id'];
        $data_callback['nexttime'] = $data['nexttime'];
        $data_callback['system_user_id'] = $data['system_user_id'];
        $data_callback['remark'] = $data['remark'];
        $reflag = $this->addCallback($data_callback);
        //添加数据记录
        $dataLog['operattype'] = '9';
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['system_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = time();
        $dataController = new DataController();
        $dataController->addDataLogs($dataLog);
        if($reflag['code']==0){
            D()->commit();
            return array('code'=>0,'msg'=>'赎回客户成功');
        }
        D()->rollback();
        return array('code'=>1,'msg'=>'赎回客户失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 确认到访
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function affirmVisit($data)
    {
        //必要参数
        if(empty($data['user_id']) || empty($data['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $_time = time();
        $info = D('User')->field('visittime,user_id,status,system_user_id')->where(array('user_id'=>$data['user_id']))->find();
        if($data['system_user_id']!=$info['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
        if(empty($info['visittime']) || $info['visittime']==0){
            $save_data['visittime'] = $_time;
            D('User')->where(array('user_id'=>$data['user_id']))->save($save_data);
        }
        //添加分配记录
        $data_callback['status'] = 1;
        $data_callback['callbacktype'] = 20;
        $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
        $data_callback['user_id'] = $data['user_id'];
        $data_callback['nexttime'] = $_time;
        $data_callback['system_user_id'] = $data['system_user_id'];
        $data_callback['remark'] = '客户上门到访,转出客户(前台操作)';
        $reflag = $this->addCallback($data_callback);
        //添加数据记录
        $dataLog['operattype'] = '12';
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['system_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = time();
        $dataController = new DataController();
        $dataController->addDataLogs($dataLog);
        if($reflag['code']==0){
            return array('code'=>0,'msg'=>'确认到访成功');
        }
        return array('code'=>1,'msg'=>'确认到访失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 用户ID获取
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getRoleChildren($system_user_id)
    {
        D('role_user')->where()->select();
    }

    /*
     * 参数处理 QQ username tel introducermobile interviewurl
     * @author zgt
     * @return false
     */
    protected function checkField($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
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
                if(!$checkform->checkMobile($data['username'])) return array('code'=>1,'msg'=>'手机号码格式有误','sign'=>'username');
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                $isusername = D('User')->where(array('username'=>array(array('eq',$data['username']),array('eq',$username0),'OR')))->find();
                if(!empty($isusername)) return array('code'=>1,'msg'=>'手机号码已存在');
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
                if (!$checkform->checkTel($data['tel'])) return array('code' => 1, 'msg' => '固定号码格式有误', 'sign' => 'tel');
                $istel = D('User')->where(array('tel' => $data['tel']))->find();
                if (!empty($istel)) return array('code' => 1, 'msg' => '固定电话已存在');
            }
        }
        //验证QQ号码是否有修改
        if(!empty($data['qq'])){
            if( !empty($user) && $user['qq']==$data['qq'] ) {
                unset($data['qq']);
            }else{
                $data['qq'] = trim($data['qq']);
                if (!$checkform->checkInt($data['qq'])) return array('code' => 1, 'msg' => 'qq格式有误', 'sign' => 'qq');
                $isqq = D('User')->where(array('qq' => $data['qq']))->find();
                if (!empty($isqq)) return array('code' => 1, 'msg' => 'qq号码已存在');
                if (empty($data['email']) && !empty($user['email'])) $data['email'] = $data['qq'] . '@qq.com';
            }
        }

        return array('code'=>0,'data'=>$data);
    }

    /**
     * 是否获取新渠道
     * @author zgt
     */
    public function isNewChannel($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        //转介绍人获取渠道
        if(!empty($data['introducermobile'])) {
            if( !empty($user) && $user['introducermobile']==$data['introducermobile'] ) {
                unset($data['introducermobile']);
            }else{
                if($checkform->checkMobile($data['introducermobile'])!==false) $data['introducermobile'] = encryptPhone($data['introducermobile'], C('PHONE_CODE_KEY'));
                else  return array('code'=>12,'msg'=>'转介绍人手机号码格式有误','sign'=>'introducermobile');
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
     * 参数过滤
     * @author zgt
     */
    protected function dispostWhere($where)
    {
        $where = array_filter($where);
        $systemType = !empty($where['system_type'])?$where['system_type']:'system_user_id';
        unset($where['system_type']);
        foreach($where as $k=>$v){
            if($k=='role_id'){
                $sys_ids = $this->getRoleIds($v);
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
            $where[$this->DB_PREFIX.'user.zone_id'] = array('IN',$zoneIdArr);
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
            $ChannelController = new ChannelController();
            $channelArr = $ChannelController->getChannelIds($where["{$this->DB_PREFIX}user.channel_id"]);
            $channelIdArr = array();
            foreach($channelArr['data'] as $k=>$v){
                $channelIdArr[] = $v['channel_id'];
            }
            $where[$this->DB_PREFIX.'user.channel_id'] = array('IN',$channelIdArr);
            unset($where['channel_id']);
        }
        return $where;
    }

    protected function dispostApplyWhere($where)
    {
        $where = array_filter($where);
        foreach($where as $k=>$v){
            if($k=='admin_system_user_id' && empty($where['system_user_id'])){
                //获取下及职位相关员工
                $systemUser = new SystemUserController();
                $ids = $systemUser->getRoleSystemUser($where['admin_system_user_id']);
                if($ids['code']==0){
                    $where["{$this->DB_PREFIX}user_apply.system_user_id"] = array('IN', $ids['data']);
                }
            }elseif($k=='zone_id'){
                $zoneIdArr = $this->getZoneIds($where["zone_id"]);
                $where[$this->DB_PREFIX.'system_user.zone_id'] = array('IN',$zoneIdArr);
            }elseif($v!='0'){
                $where["{$this->DB_PREFIX}user_apply.".$k] = $v;
            }
            unset($where[$k]);
        }
        if (!empty($where["{$this->DB_PREFIX}user_apply.key_name"]) && !empty($where["{$this->DB_PREFIX}user_apply.key_value"])) {
            if ($where["{$this->DB_PREFIX}user_apply.key_name"] == 'username'){
                $where["{$this->DB_PREFIX}user.username"] = encryptPhone(trim($where["{$this->DB_PREFIX}user_apply.key_value"]), C('PHONE_CODE_KEY'));
            }else{
                $where["{$this->DB_PREFIX}user.".$where["{$this->DB_PREFIX}user_apply.key_name"]] = array('like', '%' . $where["{$this->DB_PREFIX}user_apply.key_value"] . '%');
            }
        }
        unset($where["{$this->DB_PREFIX}user_apply.key_name"]);
        unset($where["{$this->DB_PREFIX}user_apply.key_value"]);
        unset($where["{$this->DB_PREFIX}user_apply.admin_system_user_id"]);
        return $where;
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
            ->where(array('role_id'=>$role_id))
            ->select();
        $systemUserArr = array();
        foreach($reList as $v){
            $systemUserArr[] = $v['user_id'];
        }
        return $systemUserArr;
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
        //课程列表
        $courseMain = new CourseController();
        $courseList = $courseMain->getList();
        $data['courseAll'] = $courseList['data'];
        //课程列表status
        $course_status = array();
        foreach($courseList['data'] as $k=>$v){
            $course_status[$v['course_id']] = $v['coursename'];
        }
        //客户状态
        $user_status = C('USER_STATUS');
        //跟进结果转换
        $user_attitude = C('USER_ATTITUDE');
        //学习平台
        $user_learningtype = C('USER_LEARNINGTYPE');
        //信息质量
        $user_infoquality = C('USER_INFOQUALITY');
        foreach($arrStr as $k=>$v){
            if(!empty($v['channel_id']))$arrStr[$k]['channelnames'] = D('Channel')->getChannelNames($v['channel_id']);
            if(!empty($v['visittime']) && $v['visittime']!=0)$arrStr[$k]['visit_time'] = date('Y-m-d H:i:s', $v['visittime']);
            if(!empty($v['nextvisit']) && $v['nextvisit']!=0)$arrStr[$k]['nextvisit_time'] = date('Y-m-d H:i:s', $v['nextvisit']);
            if(!empty($v['lastvisit']) && $v['lastvisit']!=0)$arrStr[$k]['lastvisit_time'] = date('Y-m-d H:i:s', $v['lastvisit']);
            if(!empty($v['allocationtime']) && $v['allocationtime']!=0)$arrStr[$k]['allocation_time'] = date('Y-m-d H:i:s', $v['allocationtime']);
            if(!empty($v['updatetime']) && $v['updatetime']!=0)$arrStr[$k]['update_time'] = date('Y-m-d H:i:s', $v['updatetime']);
            if(!empty($v['createtime']) && $v['createtime']!=0)$arrStr[$k]['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
            if(!empty($v['course_id']))$arrStr[$k]['coursename'] = $course_status[$v['course_id']];
            if(!empty($v['status']))$arrStr[$k]['statusname'] = $user_status[$v['status']]['text'];
            if(!empty($v['attitude_id']))$arrStr[$k]['attitudename'] = $user_attitude[$v['attitude_id']]['text'];
            if(!empty($v['learningtype']))$arrStr[$k]['learningtypename'] = $user_learningtype[$v['learningtype']]['text'];
            if(!empty($v['infoquality']))$arrStr[$k]['infoqualityname'] = $user_infoquality[$v['infoquality']];
            if(!empty($v['username']))$arrStr[$k]['mobile'] = decryptPhone($v['username'],C('PHONE_CODE_KEY'));
        }
        if(empty($result[0])){
            return $arrStr[0];
        }else{
            return $arrStr;
        }
    }


    /**
     * 分配、新增数据时，记录分配质量信息
     * @author nxx
     */
    public function allocationLogs($data, $system_user_id)
    {
        //添加分配记录
        $log_data['system_user_id'] = $system_user_id;
        $log_data['date'] = date('Ymd');
        $channelList = D("Channel")->field('channel_id')->where("pid = 0")->select();
        foreach ($channelList as $key => $value) {
            $channelList[$key] = $value['channel_id'];
        }
        if(empty($data[0])){
            $newList[0] = $data;
        }else{
            $newList = $data;
        }
        foreach($newList as $k=>$v){
            if (in_array($v['channel_id'], $channelList)) {
                $log_data['channel_id'] = $v['channel_id'];
            }else{
                $channelInfo = D("Channel")->where("channel_id = ".$v['channel_id'])->find();
                $log_data['channel_id'] = $channelInfo['pid'];
            }
            $reflag = $this->getUserAllocationLogs($v['infoquality'], $log_data);
            if($reflag === false) return false;
        }
        return true;
    }

    /**
     * 分配、新增数据时，记录分配质量信息
     * @author nxx
     */
    public function getUserAllocationLogs($infoquality, $log_data)
    {
        $logs = D('UserAllocationLogs')->where($log_data)->find();
        if ($logs) {
            if ($infoquality == 1) {
                $logs['infoqualitya'] = $logs['infoqualitya'] + 1;
            }
            if ($infoquality == 2) {
                $logs['infoqualityb'] = $logs['infoqualityb'] + 1;
            }
            if ($infoquality == 3) {
                $logs['infoqualityc'] = $logs['infoqualityc'] + 1;
            }
            if ($infoquality == 4) {
                $logs['infoqualityd'] = $logs['infoqualityd'] + 1;
            }
            $result = D('UserAllocationLogs')->where($log_data)->save($logs);
            if ($result !== false) {
                return true;
            }
        }else{
            if ($infoquality == 1) {
                $log_data['infoqualitya'] = 1;
            }
            if ($infoquality == 2) {
                $log_data['infoqualityb'] = 1;
            }
            if ($infoquality == 3) {
                $log_data['infoqualityc'] = 1;
            }
            if ($infoquality == 4) {
                $log_data['infoqualityd'] = 1;
            }
            $result = D('UserAllocationLogs')->add($log_data);
            if ($result !== false) {
                return true;
            }
        }
        return false;
    }

    /*
     * 隐藏客户旧数据
     * @author zgt
     */
    protected function heiddenOldInfo($user_id){
        D()->startTrans();
        //隐藏历史回访记录
        $data['status'] = 0;
        $where['user_id'] = array('IN',$user_id);
        $reflag_callback = D('UserCallback')->where($where)->save($data);
        //隐藏短信发送记录
        $sms_data['display'] = 0;
        $sms_where['touser_id'] = array('IN',$user_id);
        $reflag_sms = D('SmsLogs')->where($sms_where)->save($sms_data);
        if($reflag_callback!==false && $reflag_sms!==false){
            D()->commit();
            return true;
        }
        D()->rollback();
        return fasle;
    }

}