<?php
/*
* 客户服务接口
* @author zgt
*
*/
namespace Common\Service;

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
        $dataLog['user_id'] = $reUserId;
        $dataLog['system_user_id'] = $data['system_user_id'];
        $dataLog['updateuser_id'] = $data['system_user_id'];
        $dataLog['createuser_id'] = $data['system_user_id'];
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['zone_id'] = $data['zone_id'];
        $dataLog['channel_id'] = $data['channel_id'];
        $dataLog['infoquality'] = $data['infoquality'];
        $dataLog['logtime'] = time();
        $DataService = new DataService();
        $DataService->addDataLogs($dataLog);
        if(!empty($reUserId) && !empty($reUserInfo)){
            D()->commit();
            return array('code'=>0,'msg'=>'客户添加成功','data'=>$reUserId);
        }else{
            D()->rollback();
            return array('code'=>12,'msg'=>'数据添加失败');
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
        if(empty($data['system_user_id']) || empty($data['user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        if($user['system_user_id']!=$data['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能修改该客户信息');
        $userInfo = D('UserInfo')->where(array('user_id'=>$data['user_id']))->find();
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
    | 添加回访记录
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function addCallback($data,$rank=1)
    {
        //数据添加
        $user = D('User')->field('user_id,status')->where(array('user_id'=>array('IN', $data['user_id'])))->select();
        if(empty($user)) return false;
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
            return array('code'=>0);
        }else{
            D()->rollback();
            return array('code'=>1);
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
    | 客户放弃/回库
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
            $data_callback['system_user_id'] = $data['system_user_id'];
            $data_callback['nexttime'] = $_time;
            if($rank==2){
                //批量
                if(count($where['user_id'])>1){
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
            $this->addCallback($data_callback,2);
            //操作后-添加数据记录
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $_time;
            $DataService = new DataService();
            $DataService->addDataLogs($dataLog);
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
        $_time = time();
        $save_user['mark'] = 1;
        $save_user['nextvisit'] = $_time;
        $save_user['attitude_id'] = 0;
        $save_user['callbacknum'] = 0;
        $save_user['lastvisit'] = $_time;
        $save_user['allocationtime'] = $_time;
        $save_user['system_user_id'] = $data['tosystem_user_id'];
        //数据更新
        D()->startTrans();
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
            if($rank==2){
                //批量
                if(count(explode(',',$data['user_id']))>1){
                    $data_callback['remark'] = '批量客户转出(管理操作)';
                    $data_callback['callbacktype'] = 11;
                }else{
                    $data_callback['remark'] = '客户转出(管理操作)';
                    $data_callback['callbacktype'] = 10;
                }
            }else{
                $data_callback['remark'] = '客户转出';
                $data_callback['callbacktype'] = 1;
            }
            $this->addCallback($data_callback,2);
            //操作添加数据记录
            $dataLog['operattype'] = 3;
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $_time;
            $DataService = new DataService();
            $DataService->addDataLogs($dataLog);
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
        //必要参数
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
                if(count(explode(',',$data['user_id']))>1){
                    $data_callback['remark'] = '批量客户出库(管理操作)';
                    $data_callback['callbacktype'] = 13;
                }else{
                    $data_callback['remark'] = '客户出库(管理操作)';
                    $data_callback['callbacktype'] = 12;
                }
                $log_operattype = 15;
            }else{
                $data_callback['remark'] = '客户出库';
                $data_callback['callbacktype'] = 4;
                $log_operattype = 5;
            }
            $this->addCallback($data_callback,2);
            //添加数据记录
            $dataLog['operattype'] = 3;
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['user_id'] = $data['user_id'];
            $dataLog['logtime'] = $_time;
            $DataService = new DataService();
            $DataService->addDataLogs($dataLog,$userList,$log_operattype);
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
        $dataLog['operattype'] = 9;
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = time();
        $DataService = new DataService();
        $DataService->addDataLogs($dataLog);
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
        $dataLog['operattype'] = 12;
        $dataLog['operator_user_id'] = $data['system_user_id'];
        $dataLog['user_id'] = $data['user_id'];
        $dataLog['logtime'] = time();
        $DataService = new DataService();
        $DataService->addDataLogs($dataLog);
        if($reflag['code']==0){
            return array('code'=>0,'msg'=>'确认到访成功');
        }
        return array('code'=>1,'msg'=>'确认到访失败');
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
                if(!$checkform->checkMobile($data['username'])) return array('code'=>11,'msg'=>'手机号码格式有误','sign'=>'username');
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                $isusername = D('User')->where(array('username'=>array(array('eq',$data['username']),array('eq',$username0),'OR')))->find();
                if(!empty($isusername)) return array('code'=>11,'msg'=>'手机号码已存在');
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
                if (!$checkform->checkTel($data['tel'])) return array('code' => 11, 'msg' => '固定号码格式有误', 'sign' => 'tel');
                $istel = D('User')->where(array('tel' => $data['tel']))->find();
                if (!empty($istel)) return array('code' => 11, 'msg' => '固定电话已存在');
            }
        }
        //验证QQ号码是否有修改
        if(!empty($data['qq'])){
            if( !empty($user) && $user['qq']==$data['qq'] ) {
                unset($data['qq']);
            }else{
                $data['qq'] = trim($data['qq']);
                if (!$checkform->checkInt($data['qq'])) return array('code' => 11, 'msg' => 'qq格式有误', 'sign' => 'qq');
                $isqq = D('User')->where(array('qq' => $data['qq']))->find();
                if (!empty($isqq)) return array('code' => 11, 'msg' => 'qq号码已存在');
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

        $courseMain = new CourseService();
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