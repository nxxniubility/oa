<?php
/*
* 员工服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;
use Org\Util\Rbac;
use Think\Verify;

class SystemUserService extends BaseService
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
    | 员工登录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function login($data)
    {
        if(empty($data['username'])) return array('code'=>300, 'msg'=>'用户名不能为空', 'data'=>'username');
        if(empty($data['password'])) return array('code'=>301, 'msg'=>'密码不能为空', 'data'=>'password');
        //获取 数据判断
        $data = array_filter($data);
        $username = trim($data['username']);
        $password = trim($data['password']);
        if(!$this->checkMobile($username)) return array('code'=>'201', 'data'=>'username', 'msg'=>'手机号码格式有误');
        $verify = new Verify();
        if(!$verify->check($data['verification'],'login')) return array('code'=>'202', 'data'=>'verification', 'msg'=>'验证码不正确');
        //数据加密
        $username = encryptPhone($username, C('PHONE_CODE_KEY'));
        $user_info = D('SystemUser')->getFind(array('username'=>$username));
        if(empty($user_info)) return array('code'=>'101', 'data'=>'username', 'msg'=>'该用户名未注册');
        if(empty($user_info['password'])) return array('code'=>'102', 'data'=>'username', 'msg'=>'您的账户尚未激活,请点击下方激活按钮');
        if($user_info['status']!=1) return array('code'=>'103', 'data'=>'username', 'msg'=>'该账号已无效，请联系管理员');
        if($user_info['usertype']==10) return array('code'=>'104', 'data'=>'username', 'msg'=>'该员工已离职，已无法登录OA系统');
        if($user_info['password'] !== passwd($password)) return array('code'=>'105', 'data'=>'password', 'msg'=>'密码错误');
        //获取权限信息 获取职位（有多个）
        $user_role = $this->getSystemUserRole(array('system_user_id'=>$user_info['system_user_id']));
        if(empty($user_role['data'])) return array('code'=>'100', 'data'=>'', 'msg'=>'无法获取您的权限信息');
        //判断是否开启禁止登录功能 登录白名单组判断
        $open_login = C('open_login');
        $w_list = C('w_list');
        $user_info['logintime'] = time();
        //添加登录日志
        $this->addSystemUserLogs($user_info['system_user_id']);
        $newArr = array('userInfo'=>$user_info,'userRole'=>$user_role['data']);
        //保存登录状态
        $this->_loginSign($newArr);
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'登录成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 员工激活 短信
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function activationSms($param)
    {
        //参数验证
        if(empty($param['username'])) return array('code'=>300, 'msg'=>'用户名不能为空', 'data'=>'username');
        //数据加密
        $username = encryptPhone(trim($param['username']), C('PHONE_CODE_KEY'));
        $user_info = D('SystemUser')->getFind(array('username'=>$username));
        if (empty($user_info)) return array('code'=>201, 'msg'=>'该OA账号未创建,请先找人事创建账号！', 'data'=>'username');
        if (!empty($user_info['password'])) return array('code'=>202, 'msg'=>'该OA账号已激活！');
        //发送短信验证码
        $data['mobile'] = trim($param['username']);
        $result = D('Api', 'Service')->sendSms('authentication', $data);
        if($result['code']==0){
            return array('code'=>0, 'msg'=>'已经发送验证码,请查收','data'=>'smsverify');
        }else{
            return array('code'=>100, 'msg'=>$result['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 员工激活
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function activation($param)
    {
        //参数验证
        if (empty($param['username'])) return array('code'=>301, 'msg'=>'手机号不能为空', 'data'=>'username');
        if(!$this->checkMobile($param['username'])) return array('code'=>302, 'msg'=>'手机号码格式有误', 'data'=>'username');
        if (empty($param['phoneverify'])) return array('code'=>303, 'msg'=>'验证码不能为空,请输入6位验证码', 'data'=>'phoneverify');
        if (empty($param['password'])) return array('code'=>304, 'msg'=>'密码不能为空,请输入密码', 'data'=>'password');
        if (empty($param['confirmPassword'])) return array('code'=>305, 'msg'=>'确认密码不能为空,请输入确认密码', 'data'=>'confirmPassword');
        if ($param['phoneverify'] != session('smsVerifyCode_authentication')) return array('code'=>306, 'msg'=>'短信验证码不正确，请重新输入', 'data'=>'phoneverify');
        //数据验证
        $username = encryptPhone($param['username'], C('PHONE_CODE_KEY'));
        $user_info = D('SystemUser')->getFind(array('username'=>$username));
        if (!preg_match("/^[A-Za-z0-9_]{6,20}$/", $param['password'])) return array('code'=>307, 'msg'=>'密码必须是6-20位的字母,数字或者下划线', 'data'=>'password');
        if ($user_info['status']!=1 || $user_info['usertype']==10) return array('code'=>308, 'msg'=>'该用户已无效,请联系管理员', 'data'=>'username');
        if (!empty($user_info['password'])) return array('code'=>309, 'msg'=>'密码已经存在，无需重新设置', 'data'=>'password');
        //加密用户密码 更新用户信息
        $save_data['password'] = passwd($param['password'],C('PHONE_CODE_KEY'));
        $result = D('SystemUser')->editData($save_data, $user_info['system_user_id']);
        if($result['code']!=0)return array($result['code'], $result['msg']);
        //获取权限信息 获取职位（有多个）
        $user_role = $this->getSystemUserRole(array('system_user_id'=>$user_info['system_user_id']));
        if(empty($user_role['data'])) return array('code'=>'100', 'data'=>'', 'msg'=>'无法获取您的权限信息');
        //添加登录日志
        $this->addSystemUserLogs($user_info['system_user_id']);
        $newArr = array('userInfo'=>$user_info,'userRole'=>$user_role['data']);
        //保存登录状态
        $this->_loginSign($newArr);
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'激活成功');
    }

    /**
     * system 保存登录状态
     * @author zgt
     */
    protected function _loginSign($newArr){
        //保存session
        $session_data = array(
            'zone_id'=>$newArr['userInfo']['zone_id'],
            'system_user_id'=>$newArr['userInfo']['system_user_id'],
            'realname'=>$newArr['userInfo']['realname'],
            'username'=>$newArr['userInfo']['username'],
            'face'=>$newArr['userInfo']['face'],
            'email'=>$newArr['userInfo']['email'],
            'sex'=>$newArr['userInfo']['sex'],
            'usertype'=>$newArr['userInfo']['usertype'],
            'isuserinfo'=>$newArr['userInfo']['isuserinfo'],
            'logintime'=>$newArr['userInfo']['logintime']
        );
        session('system_user_id',$newArr['userInfo']['system_user_id']);
        session('system_user',$session_data);
        session('system_user_role',$newArr['userRole']);
        //登录成功
        Rbac::saveAccessList();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工 职位
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getSystemUserRole($param)
    {
        $_where['user_id'] = $param['system_user_id'];
        $_roles = D('RoleUser')->getList($_where,'role_id');
        $re_roles = array();
        if(!empty($_roles)){
            foreach($_roles as $v){
                $re_data = D('Role','Service')->getRoleInfo(array('role_id'=>$v['role_id']));
                if(!empty($re_data['data'])) $re_roles[] = $re_data['data'];
            }
        }
        return array('code'=>'0', 'data'=>$re_roles, 'msg'=>'获取成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工忙线状态
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getSystemEngagedStatus(){
        $_where['system_user_id'] = $this->system_user_id;
        $result = D('SystemUserEngaged')->getFind($_where);
        return array('code'=>'0', 'data'=>$result, 'msg'=>'获取成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工忙线状态
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editSystemEngagedStatus($param){
        $_where['system_user_id'] = $this->system_user_id;
        $result = D('SystemUserEngaged')->editData($param,$param['system_user_id']);
        return array('code'=>'0', 'data'=>$result, 'msg'=>'操作成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户自定义的节点
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserDefaultNodes(){
        $_where['system_user_id'] = $this->system_user_id;
        $result = D('DefineNodes')->getList($_where,'node_id','sort asc');
        if(!empty($result)){
            foreach($result as $k=>$v){
                $re_data = D('Node','Service')->getNodeInfo(array('node_id'=>$v['node_id']));
                if(!empty($re_data['data'])){
                    $result[$k]['name'] = $re_data['data']['name'];
                    $result[$k]['title'] = $re_data['data']['title'];
                    $result[$k]['level'] = $re_data['data']['level'];
                    $result[$k]['remark'] = $re_data['data']['remark'];
                }
            }
        }
        return array('code'=>'0', 'data'=>$result, 'msg'=>'获取成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 验证唯一登录
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function proveToken($system_user_id)
    {
        $token = session("token");
        $tokenData = D("SystemUserLogs")->where("system_user_id = $system_user_id")->order("zl_system_user_logs.logintime desc")->limit(1)->field("token")->find();
        if($token == $tokenData['token']){
            return array("code"=>0,'msg'=>'');
        }else{
            return array("code"=>1,'msg'=>'无效的登录');
        }

    }

    /*
    |--------------------------------------------------------------------------
    | 抹去唯一Token
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function removeToken($param)
    {
        if(!empty($param['system_user_id'])){
            $where['system_user_id'] = $param['system_user_id'];
        }
        $where['logintime'] = array(array('GT',strtotime(date('Y-m-d'))), array('ELT',time()));
        $save['token'] = time();
        $flag = D("SystemUserLogs")->where($where)->save($save);
        if($flag!==false){
            return array("code"=>0,'msg'=>'重置Token成功');
        }
        return array("code"=>1,'msg'=>'重置Token失败');
    }

    /**
     * 生成token
     * @author nxx
     */
    protected function _createToken($len)
    {
        $chrBase = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $max = strlen($chrBase) - 1;
        $randomstr = null;
        for($i=0; $i<$len; $i++){
            $randomstr .= $chrBase[rand(0,$max)];
        }
        return $randomstr;
    }



    /**
     * 参数过滤
     * @author zgt
     */
    protected function _dispostWhere($where)
    {
        $where = array_filter($where);
        foreach($where as $k=>$v){
            if($k=='role_id' && $v!=0){
                $ids = $this->_getRoleIds($v);
                if(empty($ids)) $ids='0';
                $where["{$this->DB_PREFIX}system_user.system_user_id"] = array('IN', $ids);
            }elseif(!empty($v)){
                $where["{$this->DB_PREFIX}system_user.".$k] = $v;
            }
            unset($where[$k]);
        }
        if(!empty($where["{$this->DB_PREFIX}system_user.zone_id"])){
            $zoneIdArr = $this->_getZoneIds($where["{$this->DB_PREFIX}system_user.zone_id"]);
            $where[$this->DB_PREFIX.'system_user.zone_id'] = array('IN',$zoneIdArr);
            unset($where['zone_id']);
        }

        return $where;
    }
    /**
     * 职位ID  获取对应人员ID
     * @author zgt
     */
    protected function _getRoleIds($role_id)
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



    /*
     * 添加登录日志
     * @author zgt
     * @return false
     */
    protected function addSystemUserLogs($systemUserId)
    {
        //登录唯一token
        $len = 32;
        $add_log['token'] = $this->_createToken($len);
        session("token", $add_log['token']);
        //登录时间
        $add_log['logintime'] = time();
        $add_log['loginip'] = get_client_ip();
        //获取API IP地址
        $reApi = D('Api', 'Service')->getApiIplookup($add_log['loginip']);
        if($reApi['code']==0 && $reApi['data']['city']!=''){
            $add_log['city'] = $reApi['data']['city'];
            $add_log['district'] = $reApi['data']['county'];
        }
        $addflag = D('SystemUser')->editData($add_log,$systemUserId);
        if($addflag['code']==0){
            //更新缓存
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$systemUserId));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            if(!empty($cahce_all['data'])){
                foreach($cahce_all['data'] as $k => $v){
                    if($v['system_user_id'] == $systemUserId){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
            }
            F('Cache/systemUsers', $cahce_all);
            //开启登录告警
            $add_log['system_user_id'] = $systemUserId;
            if(!empty($add_log['city'])){
                $reAlarm = $this->_logsAlarm($systemUserId,$reApi);
                if($reAlarm===true){
                    $add_log['status'] = 1;
                }
            }
            return D('SystemUserLogs')->data($add_log)->add();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 异常登录验证
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function loginAlarm($request)
    {
        if(empty($request['verifyCode'])) return array('code'=>301, 'msg'=>'请输入验证码！');
        $verifyCode = session('smsVerifyCode_alarm');
        if($request['verifyCode']==$verifyCode){
            $system_user = session('system_user');
            $system_user_id = session('system_user_id');
            D('SystemUserLogs')->where(array('system_user_id'=>$system_user_id,'logintime'=>$system_user['logintime']))->save(array('status'=>0));
            session('login_alarm', null);
            return array('code'=>0, 'msg'=>'验证成功');
        }
        return array('code'=>201, 'msg'=>'验证失败');
    }

    /**
     * 是否开启登录告警
     * @author zgt
     */
    protected function _logsAlarm($systemUserId,$data)
    {
        session('login_alarm',null);
        $result = D('SystemUserLogs')->field('city')->where(array('system_user_id'=>$systemUserId,'status'=>0))->order('system_user_logs_id desc')->limit('0,10')->select();
        $d_arr = array();
        if(!empty($result)){
            foreach($result as $k=>$v){
                $d_arr[] = $v['city'];
            }
            // 发送短信
            if(!in_array($data['data']['city'], $d_arr)){
                $info = D('SystemUser')->field('username,realname')->where(array('system_user_id'=>$systemUserId))->find();
                $time = time();
                $send_data = array(
                    "time"=>date("Y-m-d H:i:s", $time),
                    "city"=>$data['data']['city'],
                    "mobile"=>decryptPhone($info['username'], C('PHONE_CODE_KEY')),
                );
                $send_flag = D('Api', 'Service')->sendSms('alarm', $send_data);
                if($send_flag['code']==0){
                    //开启警告验证
                    session('login_alarm',true);
                    //主管发送短信
                    $role_id = D('RoleUser')->getFind(array('user_id'=>$systemUserId),'role_id');
                    $superiorid = D('Role')->getFind(array('id'=>$role_id),'superiorid');
                    if(!empty($superiorid)){
                        //记录已发送数组
                        $send_username = array();
                        $superiorUser = D('RoleUser')->getList(array('role_id'=>$superiorid),'username','__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ROLE_USER__.user_id');
                        if(!empty($superiorUser)){
                            foreach($superiorUser as $v){
                                $sendsuperior_data = array(
                                    "time"=>date("Y-m-d H:i:s", $time),
                                    "city"=>$data['data']['city'],
                                    "realname"=>$info['realname'],
                                    "mobile"=>decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                );
                                 D('Api', 'Service')->sendSms('alarmsuperior', $sendsuperior_data);
                                $send_username[] = $v['username'];
                            }
                        }
                        //额外通知高层人员
                        if(C('SMSHINT_USER')){
                            $smshint_user = C('SMSHINT_USER');
                            $smshint_list = D('SystemUser')->getList(array('system_user_id'=>array('IN',$smshint_user)),'username');
                            if(!empty($smshint_list)){
                                foreach($smshint_list as $v){
                                    if(!in_array($v['username'],$send_username)){
                                        $sendsuperior_data = array(
                                            "time"=>date("Y-m-d H:i:s", $time),
                                            "city"=>$data['data']['city'],
                                            "realname"=>$info['realname'],
                                            "mobile"=>decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                        );
                                         D('Api', 'Service')->sendSms('alarmsuperior', $sendsuperior_data);
                                    }
                                }
                            }
                        }
                    }
                    return true;
                }
                return true;
            }elseif(in_array(date('H'), array('23','24','1','2','3','4','5','6'))){
                //深夜时段告警
                $time = time();
                $info = D('SystemUser')->getFind(array('system_user_id'=>$systemUserId),'username,realname');
                $role_id = D('RoleUser')->getFind(array('user_id'=>$systemUserId),'role_id');
                $superiorid = D('Role')->getFind(array('id'=>$role_id),'superiorid');
                if(!empty($superiorid) && $superiorid!=0) {
                    //记录已发送数组
                    $send_username = array();
                    $superiorUser = D('RoleUser')->getList(array('role_id'=>$superiorid),'username','__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ROLE_USER__.user_id');
                    if (!empty($superiorUser)) {
                        foreach ($superiorUser as $v) {
                            $sendsuperior_data = array(
                                "time" => date("Y-m-d H:i:s", $time),
                                "realname" => $info['realname'],
                                "mobile" => decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                            );
                             D('Api', 'Service')->sendSms('alarmlatesuperior', $sendsuperior_data);
                            $send_username[] = $v['username'];
                        }
                    }
                }
                //额外通知高层人员
                if(C('SMSHINT_USER')){
                    $smshint_user = C('SMSHINT_USER');
                    $smshint_list = D('SystemUser')->getList(array('system_user_id'=>array('IN',$smshint_user)),'username');
                    if(!empty($smshint_list)){
                        foreach($smshint_list as $v){
                            if(!in_array($v['username'],$send_username)){
                                $sendsuperior_data = array(
                                    "time" => date("Y-m-d H:i:s", $time),
                                    "realname" => $info['realname'],
                                    "mobile" => decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                );
                                 D('Api', 'Service')->sendSms('alarmlatesuperior', $sendsuperior_data);
                            }
                        }
                    }
                }
                return true;
            }
            return fasle;
        }
        return fasle;
    }






    //----------------


    /*
   |--------------------------------------------------------------------------
   | SystemUser 获取员工列表-缓存
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUsersList($param)
    {
        //区域存在时获取子集
        if(!empty($param['zone_id'])){
            $_zone_arr = $this->_getZoneIds($param['zone_id']);
            $param['zone_id'] = array('IN', $_zone_arr);
        }
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/systemUsers') ) {
            $systemUsers = F('Cache/systemUsers');
        }else{
            $systemUsers = $this->_getSystemUserList();
            F('Cache/systemUsers', $systemUsers);
        }
        $systemUsers = $this->disposeArray($systemUsers,  $param['order'], $param['page'],  $param);
        return array('code'=>'0', 'data'=>$systemUsers);
    }

    /**
     * 获取员工统计信息质量
     * @author zgt
     */
    public function getInfoqualityCount($param)
    {
        $system_ids = explode(',', $param['systemUserId']);
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $channelList = $channeList['data']['data'];
        $newChannelArr = array();
        foreach ($channelList as $k => $v) {
            $newChannelArr[$v['channelname']][] = $v['channel_id'];
            if (!empty($v['children'])) {
                foreach ($v['children'] as $k2 => $v2) {
                    $newChannelArr[$v['channelname']][] = $v2['channel_id'];
                }
            }
        }
        $where_system['system_user_id'] = array('IN',$system_ids);
        $systemList = $this->getSystemUsersList($where_system);
        if(empty($systemList['data']['data'])) return array('code'=>'200','msg'=>'找不到员工数据');
        foreach ($systemList['data']['data'] as $key => $sysUser) {
            $i=0;
            foreach($newChannelArr as $k2=>$v2){
                $new_arr[$key]['system_user_id'] = $sysUser['system_user_id'];
                $new_arr[$key]['count'][$i]['channelname'] = $k2;
                $where['channel_id'] = array('IN',$v2);
                $countA = D('DataLogs')->where(array('infoquality'=>1,'system_user_id'=>$sysUser['system_user_id'],'channel_id'=>$where['channel_id'],'operattype'=>array('IN','1,2,3'),'logtime'=>array('EGT',strtotime(date('Y-m-d'))) ))->count();
                $countB = D('DataLogs')->where(array('infoquality'=>2,'system_user_id'=>$sysUser['system_user_id'],'channel_id'=>$where['channel_id'],'operattype'=>array('IN','1,2,3'),'logtime'=>array('EGT',strtotime(date('Y-m-d'))) ))->count();
                $countC = D('DataLogs')->where(array('infoquality'=>3,'system_user_id'=>$sysUser['system_user_id'],'channel_id'=>$where['channel_id'],'operattype'=>array('IN','1,2,3'),'logtime'=>array('EGT',strtotime(date('Y-m-d'))) ))->count();
                $countD = D('DataLogs')->where(array('infoquality'=>4,'system_user_id'=>$sysUser['system_user_id'],'channel_id'=>$where['channel_id'],'operattype'=>array('IN','1,2,3'),'logtime'=>array('EGT',strtotime(date('Y-m-d'))) ))->count();
                $new_arr[$key]['count'][$i]['countA'] = (!empty($countA))?$countA:0;
                $new_arr[$key]['count'][$i]['countB'] = (!empty($countB))?$countB:0;
                $new_arr[$key]['count'][$i]['countC'] = (!empty($countC))?$countC:0;
                $new_arr[$key]['count'][$i]['countD'] = (!empty($countD))?$countD:0;
                $i++;
            }
        }
        return $new_arr;
    }

    /*
    |--------------------------------------------------------------------------
    | SystemUser 获取客户详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getSystemUsersInfo($param)
    {
        if (!$param['system_user_id']) {
            $param['system_user_id'] = $this->system_user_id;
        }
        if( F('Cache/systemUsers') ) {
            $systemUsers = F('Cache/systemUsers');
            if(!empty($systemUsers)){
                foreach($systemUsers['data'] as $k=>$v){
                    if($v['system_user_id'] == $param['system_user_id']){
                        $info = $v;
                    }
                }
            }
        }else{
            $info = D('SystemUser')->getFind(array('system_user_id'=>$param['system_user_id']),'system_user_id,realname,zone_id');
        }
        return array('code'=>'0', 'data'=>$info);
    }

    /**
     * 获取员工实时列表
     * @return array
     */
    protected function _getSystemUserList()
    {
        $systemUsers['data'] = D('SystemUser')->getList();
        $systemUsers['count'] = D('SystemUser')->getCount();
        $systemUsers['data'] = $this->_addStatus($systemUsers['data']);
        return $systemUsers;
    }

    /**
     * 添加状态
     * @return array
     */
    protected function _addStatus($array=null){
        //添加多职位
        if(!empty($array)){
            if((count($array) == count($array, 1))) {
                $_array[] = $array;
            }else{
                $_array = $array;
            }
            $systemUserStatus = C('FIELD_STATUS.SYSTEMUSERSTATUS');
            $sexStatus = C('FIELD_STATUS.SEX');
            foreach($_array as $k=>$v){
                //标识名称
                $_array[$k]['sign_realname'] = $v['sign'].'-'.$v['realname'];
                //员工状态
                $_array[$k]['usertype_name'] = $systemUserStatus[$v['usertype']];
                //员工性别
                $_array[$k]['sex_name'] = $sexStatus[$v['sex']];
                //员工状态
                $_array[$k]['username'] = decryptPhone($v['username'],  C('PHONE_CODE_KEY'));
                //区域名称
                $_where_zone['zone_id'] = $v['zone_id'];
                //时间
                $_array[$k]['create_time'] = date('Y-m-d H:i:s',$v['createtime']);
                $_array[$k]['login_time'] =  ($v['logintime']!=0)?date('Y-m-d H:i:s',$v['logintime']):' ';
                $zone_list = D('Zone','Service')->getZoneInfo($_where_zone);
                $_array[$k]['zone_id'] = $zone_list['data']['zone_id'];
                $_array[$k]['zonelevel'] = $zone_list['data']['level'];
                $_array[$k]['zonename'] = $zone_list['data']['name'];
                //多职位
                $user_role = $this->getSystemUserRole(array('system_user_id'=>$v['system_user_id']));
                $roleNames = $roleName = $role_id = '';
                foreach($user_role['data'] as $k2=>$v2){
                    if($k2==0) {
                        $roleNames = $v2['department_name'].'/'.$v2['name'];
                        $roleName = $v2['name'];
                        $role_id = $v2['id'];
                    }else{
                        $roleNames .= '，'.$v2['department_name'].'/'.$v2['name'];
                        $roleName .= '，'.$v2['name'];
                        $role_id .= '，'.$v2['id'];
                    }
                    $_array[$k]['role_ids'][] = $v2['id'];
                }
                $_array[$k]['role_names'] = $roleNames;
                $_array[$k]['rolename'] = $roleName;
                $_array[$k]['role_id'] = $role_id;
                $_array[$k]['roles'] = $user_role['data'];
            }
        }
        //原格式返回
        if ((count($array) == count($array, 1))) {
            return $_array[0];
        } else {
            return $_array;
        }
    }

    /**
     * 区域ID 获取子集包括自己的集合
     * @author zgt
     */
    protected function _getZoneIds($zone_id)
    {
        $where['zone_id'] = $zone_id;
        $zoneIds = D('Zone', 'Service')->getZoneIds($where);
        $zoneIdArr = array();
        foreach($zoneIds['data'] as $k=>$v){
            $zoneIdArr[] = $v['zone_id'];
        }
        return $zoneIdArr;
    }

    /*
    |--------------------------------------------------------------------------
    | 添加员工
    |--------------------------------------------------------------------------
    | @parameter realname username email zone_id role_id usertype check_id entrytime straightime
    | @author zgt
    */
    public function addSystemUser($data)
    {
        $data = array_filter($data);
        if(empty($data['realname'])) return array('code'=>203, 'msg'=>'真实姓名不能为空');
        if (strlen($data['realname'])>12)  return array('code'=>201, 'msg'=>'员工姓名不得超过12个字符');
        if(empty($data['username'])) return array('code'=>204, 'msg'=>'手机号码不能为空');
        if(!$this->checkMobile($data['username'])) return array('code'=>205, 'msg'=>'手机号码格式有误');
        if(!$this->checkIsCompanyEmail($data['email'])) return array('code'=>206, 'msg'=>'邮箱地址输入有误');
        if(empty($data['zone_id'])) return array('code'=>300, 'msg'=>'请选择所属区域');
        if(empty($data['role_id'])) return array('code'=>301, 'msg'=>'请选择所属部门及职位');
        if(empty($data['usertype'])) return array('code'=>302, 'msg'=>'请选择员工状态');
        if(empty($data['check_id'])) return array('code'=>303, 'msg'=>'指纹编号不能为空');
        if(empty($data['entrytime'])) return array('code'=>304, 'msg'=>'入职时间不能为空');
        if(empty($data['straightime'])) return array('code'=>305, 'msg'=>'转正时间不能为空');
        $data['entrytime'] = strtotime($data['entrytime']);
        $data['straightime'] = strtotime($data['straightime']);
        $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
        $userInfo = D('SystemUser')->getFind(array('username'=>$data['username']),'system_user_id');
        if(!empty($userInfo['data'])) return array('code'=>201, 'msg'=>'该手机号码已被注册');
        $userInfoCheck = D('SystemUser')->getFind(array('check_id'=>$data['check_id']),'system_user_id');
        if(!empty($userInfoCheck['data'])) return array('code'=>203, 'msg'=>'该指纹编号已存在');
        $sign_arr = C('SYSTEM_USER_ORDER');
        $sign = mb_substr($data['realname'], 0, 1, "UTF-8");
        $res = array_keys($sign_arr);
        if (in_array($sign, $res)) {
            $data['sign'] = $sign_arr[$sign];
        }
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        //启动事务
        D()->startTrans();
        $result = D('SystemUser')->addData($data);
        if($result['code']==0 && !empty($data['role_id'])){
            $where_role = array();
            $add_role = explode(',',$data['role_id']);
            foreach($add_role as $k=>$v){
                $where_role[] = array('role_id'=>$v,'user_id'=>$result['data']);
            }
            D('RoleUser')->addAll($where_role);
        }
        $data['system_user_id'] = $result['data'];
        $flag_addinfo = D('SystemUserInfo')->field('system_user_id,entrytime,straightime')->data($data)->add();
        $flag_addengaged = D('SystemUserEngaged')->data(array('system_user_id'=>$result['data'],'status'=>2))->add();
        if($result['code']==0 && $flag_addinfo && $flag_addengaged){
            D()->commit();
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$result['data']));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            $cahce_all['data'][] = $new_info;
            $cahce_all['count'] =  $cahce_all['count']+1;
            F('Cache/systemUsers', $cahce_all);
            return array('code'=>'0', 'msg'=>'操作成功');
        }else{
            D()->rollback();
            return array('code'=>$result['code'], 'msg'=>$result['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工信息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editSystemUser($data)
    {
        $data = array_filter($data);
        if(empty($data['realname'])) return array('code'=>301, 'msg'=>'真实姓名不能为空');
        if(empty($data['username'])) return array('code'=>302, 'msg'=>'手机号码不能为空');
        if(!$this->checkMobile($data['username'])) return array('code'=>303, 'msg'=>'手机号码格式有误');
        if(!$this->checkIsCompanyEmail($data['email'])) return array('code'=>304, 'msg'=>'邮箱地址输入有误');
        if(empty($data['zone_id'])) return array('code'=>305, 'msg'=>'请选择所属区域');
        if(empty($data['role_id'])) return array('code'=>306, 'msg'=>'请选择所属部门及职位');
        if(empty($data['usertype'])) return array('code'=>307, 'msg'=>'请选择员工状态');
        if(empty($data['check_id'])) return array('code'=>308, 'msg'=>'指纹编号不能为空');
        if(empty($data['entrytime'])) return array('code'=>309, 'msg'=>'入职时间不能为空');
        if(empty($data['straightime'])) return array('code'=>310, 'msg'=>'转正时间不能为空');
        $data['entrytime'] = strtotime($data['entrytime']);
        $data['straightime'] = strtotime($data['straightime']);
        if($data['entrytime']>$data['straightime']) return array('code'=>201, 'msg'=>'转正时间不能小于入职时间');
        $system_user_id = !empty($data['system_user_id'])?$data['system_user_id']:$this->system_user_id;
        $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
        //是否修改手机号码 是：清空密码
        $userInfo = D('SystemUser')->where(array('system_user_id'=>$system_user_id))->find();
        if($userInfo['username']!=$data['username']){
            $data['password'] == '';
            $isUsername = D('SystemUser')->where(array('username'=>$data['username']))->find();
            if(!empty($isUsername)) return array('code'=>202, 'msg'=>'该手机号码已被注册');
        }
        $userInfoCheck = D('SystemUser')->where(array('check_id'=>$data['check_id']))->find();
        if(!empty($userInfoCheck)) {
            if($userInfoCheck['system_user_id'] != $system_user_id){
                return array('code'=>203, 'msg'=>'该指纹编号已存在', 'sign'=>'check_id');
            }
        }
         //启动事务
        D()->startTrans();
        $result = D('SystemUser')->editData($data, $system_user_id);
        $flag_userINfo['code'] = 0;
        if(!empty($data['role_id'])) {
            D('RoleUser')->delData($system_user_id);
            $edit_role = explode(',',$data['role_id']);
            if(!empty($edit_role)){
                foreach($edit_role as $k=>$v){
                    $where_role['role_id'] = $v;
                    $where_role['user_id'] = $system_user_id;
                    D('RoleUser')->addData($where_role);
                }
            }
        }
        $flag_userINfo = D('SystemUserInfo')->editData($data,$system_user_id);
        if($flag_userINfo['code']==0 && $result['code']==0){
            D()->commit();
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$system_user_id));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            if(!empty($cahce_all['data'])){
                foreach($cahce_all['data'] as $k => $v){
                    if($v['system_user_id'] == $system_user_id){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
            }
            F('Cache/systemUsers', $cahce_all);
            return array('code'=>'0', 'msg'=>'操作成功');
        }else{
            D()->rollback();
            return array('code'=>100, 'msg'=>$result['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工详情信息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editSystemUserInfo($data)
    {
        $data = array_filter($data);
        unset($data['username']);
        unset($data['realnamename']);
        unset($data['email']);
        if(empty($data['birthday'])) return array('code'=>300, 'msg'=>'生日不能为空','data'=>array('sign'=>'birthday'));
        if(empty($data['nativeplace'])) return array('code'=>301, 'msg'=>'籍贯不能为空','data'=>array('sign'=>'nativeplace'));
        if(empty($data['education_id'])) return array('code'=>302, 'msg'=>'学历不能为空');
        if(empty($data['school'])) return array('code'=>303, 'msg'=>'毕业学校不能为空','data'=>array('sign'=>'school'));
        if(empty($data['plivatemail'])) return array('code'=>304, 'msg'=>'个人邮箱不能为空','data'=>array('sign'=>'plivatemail'));
        if(empty($data['usertype'])) return array('code'=>305, 'msg'=>'用户状态不能为空');
        if(empty($data['entrytime'])) return array('code'=>305, 'msg'=>'入职时间不能为空','data'=>array('sign'=>'entrytime'));
        if(empty($data['straightime'])) return array('code'=>305, 'msg'=>'转正时间不能为空','data'=>array('sign'=>'straightime'));
        if(empty($data['check_id'])) return array('code'=>305, 'msg'=>'指纹编号不能为空','data'=>array('sign'=>'check_id'));
        $data['birthday'] = strtotime($data['birthday']);
        $data['entrytime'] = strtotime($data['entrytime']);
        $data['straightime'] = strtotime($data['straightime']);
        if($data['entrytime']>$data['straightime']) return array('code'=>203, 'msg'=>'转正时间不能小于入职时间');
        $system_user_id = !empty($data['system_user_id'])?$data['system_user_id']:$this->system_user_id;
        $userInfoCheck = D('SystemUser')->where(array('check_id'=>$data['check_id']))->find();
        if(!empty($userInfoCheck)) {
            if($userInfoCheck['system_user_id'] != $system_user_id){
                return array('code'=>202, 'msg'=>'该指纹编号已存在', 'sign'=>'check_id');
            }
        }
        //启动事务
        D()->startTrans();
        $result = D('SystemUser')->editData($data, $system_user_id);
        $flag_userINfo = D('SystemUserInfo')->editData($data,$system_user_id);
        if($flag_userINfo['code']==0 && $result['code']==0){
            D()->commit();
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$system_user_id));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            if(!empty($cahce_all['data'])){
                foreach($cahce_all['data'] as $k => $v){
                    if($v['system_user_id'] == $system_user_id){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
            }
            F('Cache/systemUsers', $cahce_all);
            return array('code'=>'0', 'msg'=>'操作成功');
        }else{
            D()->rollback();
            return array('code'=>100, 'msg'=>$result['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 员工信息 删除/离职
    |--------------------------------------------------------------------------
    | @parameter system_user_id flag
    | @author zgt
    */
    public function delSystemUser($data)
    {
        //启动事务
        D()->startTrans();
        if($data['flag']=='del'){
            $data_save['status'] = 0;
            $flag_saveUser = true;
        }else{
            $data_save['usertype'] = 10;
            $data_save['departuretime'] = time();
            //客户回库
            $where['system_user_id'] = $data['system_user_id'];
            $where['status'] = array('IN','20,30');
            $data_saveUser['status'] = 160;
            $flag_saveUser = D('User')->where($where)->save($data_saveUser);
        }
        //操作员工
        $flag = D('SystemUser')->where(array('system_user_id'=>array('IN', $data['system_user_id'])))->save($data_save);
        if($flag!==false && $flag_saveUser!==false){
            D()->commit();
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$data['system_user_id']));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            if(!empty($cahce_all['data'])){
                foreach($cahce_all['data'] as $k => $v){
                    if($v['system_user_id'] == $data['system_user_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
            }
            F('Cache/systemUsers', $cahce_all);
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>'1', 'msg'=>'操作失败');
    }

    /*
   |--------------------------------------------------------------------------
   | 修改员工 所属客户地区
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editUserZone($where)
    {
        $userInfo = D('SystemUser')->field('system_user_id,zone_id')->where(array('system_user_id' => $where['system_user_id']))->find();
        $flag = D('User')->where(array('system_user_id' => $where['system_user_id'], 'status' => array('IN', '20,30')))->save(array('zone_id' => $userInfo['zone_id']));
        if ($flag !== false) {
            return array('code' => '0', 'msg' => '数据操作成功');
        }
        return array('code' => '100', 'msg' => '数据操作失败');

    }

    /**
     * 获取员工信息详情
     * @author nxx
     */
    public function getSystemUserInfo($param)
    {
        $system_user_id = !empty($param['system_user_id'])?$param['system_user_id']:$this->system_user_id;
        $DB_PREFIX = C('DB_PREFIX');
        $where[C('DB_PREFIX').'system_user.system_user_id'] = $system_user_id;
        $systemUserInfo = D('SystemUser')
            ->field(array(
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.username",
                "{$DB_PREFIX}system_user.realname",
                "{$DB_PREFIX}system_user.nickname",
                "{$DB_PREFIX}system_user.face",
                "{$DB_PREFIX}system_user.email",
                "{$DB_PREFIX}system_user.sex",
                "{$DB_PREFIX}system_user.check_id",
                "{$DB_PREFIX}system_user.isuserinfo",
                "{$DB_PREFIX}system_user.usertype",
                "{$DB_PREFIX}system_user_info.plivatemail",
                "{$DB_PREFIX}system_user_info.birthday",
                "{$DB_PREFIX}system_user_info.nativeplace",
                "{$DB_PREFIX}system_user_info.school",
                "{$DB_PREFIX}system_user_info.marital",
                "{$DB_PREFIX}system_user_info.province_id",
                "{$DB_PREFIX}system_user_info.city_id",
                "{$DB_PREFIX}system_user_info.area_id",
                "{$DB_PREFIX}system_user_info.address",
                "{$DB_PREFIX}system_user_info.education_id",
                "{$DB_PREFIX}system_user_info.school",
                "{$DB_PREFIX}system_user_info.majorname",
                "{$DB_PREFIX}system_user_info.entrytime",
                "{$DB_PREFIX}system_user_info.straightime",
                "{$DB_PREFIX}system_user_info.socialsecurity",
                "{$DB_PREFIX}system_user_info.providentfund",
                "{$DB_PREFIX}system_user_info.remark",
                "{$DB_PREFIX}system_user_info.worktime",
                "{$DB_PREFIX}system_user_info.workintroduction",
                "{$DB_PREFIX}system_user_info.status as info_status",
                "{$DB_PREFIX}zone.zone_id",
                "{$DB_PREFIX}zone.level as zonelevel",
                "{$DB_PREFIX}zone.name as zonename",
                "{$DB_PREFIX}department.department_id",
                "{$DB_PREFIX}department.departmentname",
                "{$DB_PREFIX}role.id as role_id",
                "{$DB_PREFIX}role.name as rolename",
                "{$DB_PREFIX}system_user.createtime",
                "{$DB_PREFIX}system_user.createip",
                "{$DB_PREFIX}system_user_engaged.status as engaged_status"
            ))
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER_INFO__ ON __SYSTEM_USER_INFO__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->join('LEFT JOIN __ROLE_USER__ ON __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
            ->join('LEFT JOIN __ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->find();
        //添加多职位
        $roles = $this->getSystemUserRole(array('system_user_id'=>$systemUserInfo['system_user_id']));
        $systemUserInfo['user_roles'] = $roles['data'];
        $roleNames = '';
        foreach($roles['data'] as $k2=>$v2){
            if($k2==0) $roleNames .= $v2['department_name'].'/'.$v2['name'];
            else $roleNames .= '，'.$v2['department_name'].'/'.$v2['name'];
        }

        $systemUserInfo['role_names'] = $roleNames;
        $systemUserInfo['username'] = decryptPhone($systemUserInfo['username'],  C('PHONE_CODE_KEY'));
        //时间格式
        $systemUserInfo['entry_time'] = $systemUserInfo['entrytime']!=0?date('Y-m-d', $systemUserInfo['entrytime']):'';
        $systemUserInfo['straigh_time'] = $systemUserInfo['straightime']!=0?date('Y-m-d', $systemUserInfo['straightime']):'';
        $systemUserInfo['birthday_time'] = !empty($systemUserInfo['birthday'])?date('Y-m-d', $systemUserInfo['birthday']):'';

        $usertypeList = C('FIELD_STATUS.SYSTEMUSERSTATUS');
        $systemUserInfo['usertype_name'] = $usertypeList[$systemUserInfo['usertype']];

        $sexList = C('FIELD_STATUS.SEX');
        $systemUserInfo['sex_name'] = $sexList[$systemUserInfo['sex']];

        $maritalList = C('FIELD_STATUS.MARITAL');
        $systemUserInfo['marital_name'] = $maritalList[$systemUserInfo['marital']];

        $eduList = C('FIELD_STATUS.EDUCATION_ARRAY');
        $systemUserInfo['education_name'] = $eduList[$systemUserInfo['education_id']];
        return array('code'=>0, 'data'=>$systemUserInfo);
    }

    /**
     * 修改密码
     * @author nxx
     */
    public function editPwd($param)
    {
        $system_user_id = $this->system_user_id;
        if (empty($param['oldPassword']) || strlen($param['oldPassword'])<6){
            return array('code'=>301, 'msg'=>'旧密码不能为空,请输入密码');
        }
        if (empty($param['password']) || strlen($param['password'])<6){
            return array('code'=>302, 'msg'=>'新密码不能为空,且不能低于6位数');
        }
        if ($param['password'] != $param['confirmPassword']){
            return array('code'=>303, 'msg'=>'您输入的两次密码不一致,请重新输入');
        }
        if(session('smsVerifyCode_pwdEdit') != $param['phoneverify'] || !$param['phoneverify']){
            return array('code'=>304, 'msg'=>'短信验证码不正确，请重新输入');
        }
        //数据验证
        $userInfo = D('SystemUser')->getFind(array('system_user_id'=>$system_user_id),'password');   
        if($userInfo['password'] != passwd($param['oldPassword'],C('PHONE_CODE_KEY'))){
            return array('code'=>201, 'msg'=>'您输入的旧密码验证有误');
        }
        $data['password'] = passwd($param['password'],C('PHONE_CODE_KEY'));
        $result = D('SystemUser')->editData($data, $system_user_id);
        if ($result['code'] == 0) {
            return array('code'=>0, 'msg'=>'修改成功');
        }
        return array('code'=>202, 'msg'=>'您输入的旧密码验证有误');
    }


    /*
    |--------------------------------------------------------------------------
    | 获取员工 自定义列
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getColumnList($where)
    {
        if(empty($where['columntype'])) return array('code'=>300, 'msg'=>'类型不能为空');
        $where['system_user_id'] = $this->system_user_id;
        if( session('columnList_'.$this->system_user_id.'_'.$where['columntype']) ){
            $result = session('columnList_'.$this->system_user_id.'_'.$where['columntype']);
        }else{
            $result = D('SystemUserColumn')->getList($where);
            session('columnList_'.$this->system_user_id.'_'.$where['columntype'],$result);
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改员工自定义列信息
   |--------------------------------------------------------------------------
   | system_user_id columntype
   | @author zgt
   */
    public function editColumn($data)
    {
        if(empty($data['columntype'])) return array('code'=>300, 'msg'=>'类型不能为空');
        $data['system_user_id'] = $this->system_user_id;
        D()->startTrans();
        D('SystemUserColumn')->where(array('system_user_id'=>$data['system_user_id'],'columntype'=>$data['columntype']))->delete();
        $columnNames = explode(',', $data['columnname']);
        foreach($columnNames as $k=>$v){
            $v = explode('-', $v);
            $add_data[$k]['system_user_id'] = $data['system_user_id'];
            $add_data[$k]['columntype'] = $data['columntype'];
            $add_data[$k]['columnname'] = $v[0];
            $add_data[$k]['sort'] = $v[1];
        }
        $reflag = D('SystemUserColumn')->addAll($add_data);
        if($reflag!==false) {
            D()->commit();
            session('columnList_'.$this->system_user_id.'_'.$data['columntype'],null);
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>100, 'msg'=>'操作失败');
    }

    /*
   |--------------------------------------------------------------------------
   | 修改员工自定义列信息
   |--------------------------------------------------------------------------
   | system_user_id columntype
   | @author zgt
   */
    public function editSystemUserFace($data)
    {
        $result = D('SystemUser')->editData($data, $this->system_user_id);
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工 呼叫号码设置
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getCallNumber($where)
    {
        $where['system_user_id'] = $this->system_user_id;
        $where['number_status'] = 1;
        $result = D('CallNumber')->getList($where,'call_number_id,number,number_type,number_start');
        foreach($result as $k=>$v){
            $result[$k]['number_type_name'] = $v['number_type']==1?'固定电话':'手机号码';
            $result[$k]['number_start_name'] = $v['number_start']==1?'禁用':'启用';
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加员工 呼叫号码设置
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addCallNumber($data)
    {
        if(empty($data['number_type'])) {
            return array('code'=>205,'msg'=>'类型不能为空');
        }
        if(empty($data['number'])) {
            return array('code'=>206,'msg'=>'号码不能为空');
        }
        //实例验证类
        if($data['number_type']==1){
            if(!$this->checkTel($data['number'])){
                return array('code'=>201,'msg'=>'固话码格式有误,格式必需是：区号-号码！');
            }
        }else{
            if(!$this->checkMobile($data['number'])){
                return array('code'=>202,'msg'=>'手机号码格式有误');
            }
        }
        $data['system_user_id'] = $this->system_user_id;
        $result = D('CallNumber')->addData($data);
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工 呼叫号码设置
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editCallNumber($data)
    {
        if(empty($data['number_type'])) {
            return array('code'=>301,'msg'=>'类型不能为空');
        }
        if(empty($data['number'])) {
            return array('code'=>302,'msg'=>'号码不能为空');
        }
        if(empty($data['call_number_id'])) {
            return array('code'=>303,'msg'=>'参数异常');
        }
        //实例验证类
        if($data['number_type']==1){
            if(!$this->checkTel($data['number'])) return array('code'=>201,'msg'=>'固话码格式有误,格式必需是：区号-号码！');
        }else{
            if(!$this->checkMobile($data['number'])) return array('code'=>202,'msg'=>'手机号码格式有误');
        }
        $result = D('CallNumber')->editData($data,$data['call_number_id']);
        //返回数据与状态
       return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工 呼叫号码设置
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function delCallNumber($data)
    {
        if(empty($data['call_number_id'])){
            return array('code'=>206,'msg'=>'参数异常');
        }
        $data_edit['number_status'] = 0;
        $result = D('CallNumber')->editData($data_edit,$data['call_number_id']);
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 启用员工 呼叫号码设置
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function startCallNumber($data)
    {
        if(empty($data['call_number_id']))  return array('code'=>206,'msg'=>'参数异常');
        D()->startTrans();
        $re_all = D('CallNumber')->where(array('system_user_id'=>$data['system_user_id']))->save(array('number_start'=>0));
        $re_edit = D('CallNumber')->editData($data,$data['call_number_id']);
        if($re_all!==false && $re_edit['code']==0){
            D()->commit();
            //返回数据与状态
            return array('code'=>'0', 'data'=>$re_edit['data']);
        }else{
            D()->rollback();
            //返回数据与状态
            return array('code'=>'100', 'msg'=>'数据操作失败');
        }
    }

    /*
   |--------------------------------------------------------------------------
   | 员工获取短信模版
   |--------------------------------------------------------------------------
   | $type
   | @author zgt
   */
    public function getSmsTemplate()
    {
        $templateList = D('SmsTemplate')->where(array('system_user_id'=>$this->system_user_id))->select();
        if(!empty($templateList)){
            foreach($templateList as $k=>$v){
                $templateList[$k]['create_time'] = date('Y-m-d', $v['createtime']);
            }
        }
        return array('code'=>'0', 'msg'=>'获取成功', 'data'=>$templateList);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工短信模版 创建
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function createSmsTemplate($data)
    {
        $data = array_filter($data);
        if(empty($data['templatename'])) array('code'=>300, 'msg'=>'名称不能为空');
        if(empty($data['template'])) array('code'=>301, 'msg'=>'模版不能为空');
        $data['template'] = trim($data['template']);
        $data['createtime'] = time();
        $add_flag = D('SmsTemplate')->addData($data);
        if($add_flag['code']==0){
            return array('code'=>0, 'msg'=>'创建成功');
        }
        return array('code'=>$add_flag['code'], 'msg'=>$add_flag['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工短信模版  修改
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function editSmsTemplate($data)
    {
        $data = array_filter($data);
        $data['system_user_id'] = $this->system_user_id;
        if(empty($data['sms_template_id'])) array('code'=>300, 'msg'=>'参数异常');
        if(empty($data['templatename'])) array('code'=>301, 'msg'=>'名称不能为空');
        if(empty($data['template'])) array('code'=>302, 'msg'=>'模版不能为空');
        $own = D('SmsTemplate')->getFind(array('sms_template_id'=>$data['sms_template_id'],'system_user_id'=>$data['system_user_id']),'sms_template_id');
        if(empty($own)) array('code'=>201, 'msg'=>'该模版不属于自己');
        $data['template'] = trim($data['template']);
        $save_flag = D('SmsTemplate')->editData($data,$data['sms_template_id']);
        if($save_flag['code']==0){
            return array('code'=>0, 'msg'=>'操作成功');
        }
        return array('code'=>$save_flag['code'], 'msg'=>$save_flag['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工短信模版  删除
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function delSmsTemplate($data)
    {
        $data = array_filter($data);
        $data['system_user_id'] = $this->system_user_id;
        if(empty($data['sms_template_id'])) array('code'=>300, 'msg'=>'参数异常');
        $own = D('SmsTemplate')->getFind(array('sms_template_id'=>$data['sms_template_id'],'system_user_id'=>$data['system_user_id']),'sms_template_id');
        if(empty($own)) array('code'=>201, 'msg'=>'该模版不属于自己');
        $del_flag = D('SmsTemplate')->delData(array('sms_template_id'=>$data['sms_template_id']));
        if($del_flag!==false){
            return array('code'=>0, 'msg'=>'操作成功');
        }
        return array('code'=>100, 'msg'=>'操作失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 员工短信发送
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function sendSmsUser($data)
    {
        if(empty($data['user_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $system_user = $this->system_user;
        $request['myname'] = $system_user['realname'];
        $userInfo = D('User')->getFind(array('user_id'=>$data['user_id']),'realname,username');
        //短信发送
        $query = array(
            'mobile'=>decryptPhone($userInfo['username'], C('PHONE_CODE_KEY')),
            'content'=>trim($data['sendTxt'])
        );
        $send_flag = D('Api','Service')->sendSmsGY($query);
        //添加发送记录
        $send_log = array(
            "touser_id"=>$data['user_id'],
            "system_user_id"=>$data['system_user_id'],
            "sendtime"=>time(),
            'content'=>$data['sendTxt'],
            'sendstatus'=>$send_flag['code'],
            'senderror'=>$send_flag['msg']
        );
        D('SmsLogs')->addData($send_log);
        if($send_flag['code']==0){
            return array('code'=>'0', 'msg'=>'短信发送成功');
        }
        return array('code'=>100, 'msg'=>$send_flag['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工获取下属职位对应 员工ID
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getRoleSystemUser($system_user_id)
    {
        //获取所有职位集合
        $roleList = D('Role','Service')->getRoleList();
        $roleList = $roleList['data'];
        $reroles = $this->getRoleUserList($system_user_id);
        $rerolesUserList = $this->getRoleUserList();
        if($reroles['code']==0){
            $my_roles = array();
            $new_roles = array();
            foreach($reroles['data']['data'] as $k=>$v){
                //数组分级
                $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
                $newZoneList = $Arrayhelps->subFinds($roleList['data'],$v['role_id'],'id','superiorid');
                $my_roles = array_merge($my_roles,$newZoneList);
                $my_roles[] = array('id'=>$v['role_id']);
            }
            foreach($my_roles as $k=>$v){
                $new_roles[] = $v['id'];
            }
            //是否管理员权限
            if(!in_array(C('ADMIN_SUPER_ROLE'),$new_roles)){
                foreach($rerolesUserList['data']['data'] as $k=>$v){
                    if(in_array($v['role_id'], $new_roles) ){
                        $sysList[] = $v['user_id'];
                    }
                }
            }else{
                return array('code'=>'1', 'msg'=>'管理员');
            }
        }
        return array('code'=>'0', 'msg'=>'操作成功', 'data'=>$sysList);
    }


    /*
    |--------------------------------------------------------------------------
    | 获取员工对应职位关系列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    protected function getRoleUserList($system_user_id=null)
    {
        $role['data'] = D('RoleUser')->select();
        if($system_user_id!==null){
            $user_role = array();
            foreach($role['data'] as $k=>$v){
                if($v['user_id']==$system_user_id){
                    $user_role['data'][] = $v;
                }
            }
            $user_role['count'] = count($user_role['data']);
            return array('code'=>'0', 'msg'=>'操作成功', 'data'=>$user_role);
        }
        return array('code'=>'0', 'msg'=>'操作成功', 'data'=>$role);
    }


    /*
    |--------------------------------------------------------------------------
    | 获取对应的员工到访信息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getSystemUserVisit($where=null,$order=null,$limit='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $order = !empty($order)?$order:$DB_PREFIX.'user_visit_logs.visitnum';
        $where[$DB_PREFIX.'system_user.zone_id'] = $this->system_user['zone_id'];
        $where[$DB_PREFIX.'user_visit_logs.date'] = date('Ymd');
        $redata['data'] =
            $this
                ->field(array(
                    "{$DB_PREFIX}system_user.system_user_id",
                    "{$DB_PREFIX}system_user.username",
                    "{$DB_PREFIX}system_user.realname",
                    "{$DB_PREFIX}system_user.face",
                    "{$DB_PREFIX}system_user.email",
                    "{$DB_PREFIX}system_user.sex",
                    "{$DB_PREFIX}system_user.check_id",
                    "{$DB_PREFIX}system_user.isuserinfo",
                    "{$DB_PREFIX}system_user.usertype",
                    "{$DB_PREFIX}system_user.logintime",
                    "{$DB_PREFIX}system_user.loginip",
                    "{$DB_PREFIX}zone.zone_id",
                    "{$DB_PREFIX}zone.level as zonelevel",
                    "{$DB_PREFIX}zone.name as zonename",
                    "{$DB_PREFIX}department.department_id",
                    "{$DB_PREFIX}department.departmentname",
                    "{$DB_PREFIX}role.id as role_id",
                    "{$DB_PREFIX}role.name as rolename",
                    "{$DB_PREFIX}system_user.createtime",
                    "{$DB_PREFIX}system_user.createip",
                    "{$DB_PREFIX}system_user_engaged.status as engaged_status",
                    "{$DB_PREFIX}user_visit_logs.visitnum"
                ))
                ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                ->join('LEFT JOIN __USER_VISIT_LOGS__ on __USER_VISIT_LOGS__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->group("{$DB_PREFIX}system_user.system_user_id")->Distinct(true)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->select();
        //统计总数
        unset($where[$DB_PREFIX.'user_visit_logs.date']);
        if(!empty($redata['data'])){
            $redata['count'] = $this
                ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->where($where)
                ->count("{$DB_PREFIX}system_user.system_user_id");
            //添加多职位
            foreach($redata['data'] as $k=>$v){
                $roles = $this->getSystemUserRole(array('system_user_id'=>$v['system_user_id']));
                $roleNames = '';
                foreach($roles as $k2=>$v2){
                    if($k2==0) $roleNames .= $v2['department_name'].'/'.$v2['name'];
                    else $roleNames .= '，'.$v2['department_name'].'/'.$v2['name'];
                }
                $redata['data'][$k]['role_names'] = $roleNames;
            }
        }else{
            $redata['count'] = 0;
        }      
        return array('code'=>0, 'data'=>$redata);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取本中心销售/教务名单
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemVisitList($where){
        //获取销售与教务 配置项ID
        $market_arr = C('ADMIN_MARKET_ROLE');
        $educational_arr = C('ADMIN_EDUCATIONAL_ROLE');
        //合并教务与销售ID集合
        $market_arr = explode(',', $market_arr);
        $educational_arr = explode(',', $educational_arr);
        $roles_arr = array_merge($market_arr, $educational_arr);
        $where['role_ids'] = array('in',$roles_arr);
        $where['usertype'] = array('neq',10);
        $where['status'] = 1;
        $list = $this->getSystemUsersList($where);
        if(!empty($list['data']['data'])){
            foreach($list['data']['data'] as $k=>$v){
                $visit_logs = D('UserVisitLogs')->getFind(array('system_user_id'=>$v['system_user_id'],'date'=>date('Ymd')),'visitnum');
                $list['data']['data'][$k]['visitnum'] = !empty($visit_logs)?$visit_logs['visitnum']:0;
            }
            uasort($list['data']['data'], function($a, $b) {
                $al = ($a['visitnum']);
                $bl = ($b['visitnum']);
                if($al==$bl)return 0;
                return ($al<$bl)?-1:1;
            });
            array_values($list['data']['data']);
        }
        return array('code'=>0, 'data'=>$list['data']);
    }
}
