<?php

namespace Common\Controller;

use Common\Controller\BaseController;
use Common\Controller\RoleController as RoleMain;

class SystemUserController extends BaseController
{
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
    public function systemLogin($data)
    {
        //获取 数据判断
        $username = trim($data['username']);
        $password = $data['password'];

        $userInfo = D('SystemUser')->getSystemUser(array('username'=>encryptPhone($username,C('PHONE_CODE_KEY'))));
        if(empty($userInfo)) return array('code'=>'3', 'data'=>'', 'msg'=>'该用户名未注册', 'sign'=>'username');
        if(empty($userInfo['password'])) return array('code'=>'3', 'data'=>'', 'msg'=>'您的账户尚未激活,请点击下方激活按钮', 'sign'=>'password');
        if($userInfo['status']!=1) return array('code'=>'3', 'data'=>'', 'msg'=>'该账号已无效，请联系管理员');
        if($userInfo['usertype']==10) return array('code'=>'3', 'data'=>'', 'msg'=>'该员工已离职，已无法登录OA系统');
        if($userInfo['password'] !== passwd($password)) return array('code'=>'3', 'data'=>'', 'msg'=>'密码错误', 'sign'=>'password');
        //获取权限信息 获取职位（有多个）
        $user_role = D('RoleUser')->getSystemUserRole($userInfo['system_user_id']);
        if(empty($user_role)) return array('code'=>'1', 'data'=>'', 'msg'=>'无法获取您的权限信息');
        //判断是否开启禁止登录功能 登录白名单组判断
        $open_login = C('open_login');
        $w_list = C('w_list');
        $userInfo['logintime'] = time();
        //添加登录日志
        $this->addSystemUserLogs($userInfo['system_user_id']);
        $newArr = array('userInfo'=>$userInfo,'userRole'=>$user_role);
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'登录成功');
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
    public function removeToken($system_user_id=null)
    {
        if($system_user_id!==null){
            $where['system_user_id'] = $system_user_id;
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


    /*
    |--------------------------------------------------------------------------
    | 员工激活
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function systemActivation($data)
    {
        //数据验证
        $username = trim($data['username']);
        $userInfo = D('SystemUser')->getSystemUser(array('username'=>encryptPhone($username,C('PHONE_CODE_KEY'))));
        if (empty($userInfo)) return array('code'=>'3', 'data'=>'', 'msg'=>'该用户名未注册', 'sign'=>'username');
        if ($userInfo['status']!=1 || $userInfo['usertype']==10) return array('code'=>'3', 'data'=>'', 'msg'=>'该用户已无效,请联系管理员', 'sign'=>'username');
        if ($data['phoneverify'] != session('smsVerifyCode_activate')) return array('code'=>'3', 'data'=>'', 'msg'=>'短信验证码不正确，请重新输入', 'sign'=>'phoneverify');
        if ($data['password'] != $data['confirmPassword']) return array('code'=>'3', 'data'=>'', 'msg'=>'您输入的两次密码不一致,请重新输入', 'sign'=>'password');
        if (!preg_match("/^[A-Za-z0-9_]{6,20}$/", $data['password'])) return array('code'=>'3', 'data'=>'', 'msg'=>'密码必须是6-20位的字母,数字或者下划线', 'sign'=>'password');
        if (!empty($userInfo['password'])) return array('code'=>'3', 'data'=>'', 'msg'=>'密码已经存在，无需重新设置', 'sign'=>'password');
        //加密用户密码 更新用户信息
        $save_data['password'] = passwd($data['password'],C('PHONE_CODE_KEY'));
        $flag_edit = D('SystemUser')->editSystemUser($save_data, $userInfo['system_user_id']);
        if(empty($flag_edit)) return array('code'=>'1', 'data'=>'', 'msg'=>'操作失败');
        //添加登录日志
        $userInfo['logintime'] = time();
        $this->addSystemUserLogs($userInfo['system_user_id']);
        //获取职位（有多个）
        $user_role = D('RoleUser')->getSystemUserRole($userInfo['system_user_id']);
        $newArr = array('userInfo'=>$userInfo,'userRole'=>$user_role);
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'激活成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工消息列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMsgList($where, $limit=null)
    {
        $result['data'] = D('SystemUserMsg')->where($where)->limit($limit)->order('createtime desc')->select();
        $result['count'] = D('SystemUserMsg')->where($where)->count();
        if(!empty($result['data'])){
            foreach($result['data'] as $k=>$v){
                $result['data'][$k]['create_time'] = date('m-d H:i',$v['createtime']);
            }
        }
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加消息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addMsg($where)
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 消息-已读
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function readMsg($where)
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表--缓存
    |--------------------------------------------------------------------------
    | $type 是否添加分配渠道统计
    | @author zgt
    */
    public function getListCache($where, $order=null, $limit=null, $type=null)
    {
        $where = array_filter($where);
        if(F('Cache/Personnel/system_list') && empty($where['realname'])){
            $systemUserAll = F('Cache/Personnel/system_list');
        }else{
            if(!empty($where['realname'])){
                $systemUserList = $this->getList($where);
            }else{
                $systemUserList = $this->getList(array('usertype'=>array('neq',10)));
            }
            $systemUserCount = $this->getCount(array('usertype'=>array('neq',10)));
            $systemUserAll['data'] = $systemUserList['data'];
            $systemUserAll['count'] = $systemUserCount['data'];
            if(empty($where['realname'])){
                F('Cache/Personnel/system_list',$systemUserAll);
            }
        }
        if(!empty($where['zone_id'])){
            $zoneIdArr = array();
            $zoneIdArr = $this->getZoneIds($where['zone_id']);
        }
        foreach($systemUserAll['data'] as $k=>$v){
            if(!empty($where['zone_id']) && !in_array($v['zone_id'],$zoneIdArr)){
                unset($systemUserAll['data'][$k]);
            }
            if(!empty($where['role_id'])){
                $in_flag = false;
                foreach($v['roles'] as $k2=>$v2){
                    if( in_array($v2['role_id'],explode(',', $where['role_id']))){
                        $in_flag = true;
                    }
                }
                if($in_flag===false) unset($systemUserAll['data'][$k]);
            }
        }
        if($limit!==null){
            $limit = explode(',',$limit);
            $systemUserAll['data'] = array_slice($systemUserAll['data'], $limit[0], $limit[1]);
        }
        // 是否添加分配渠道统计
        if(!empty($type)) $systemUserAll['data'] = $this->getInfoqualityCount($systemUserAll['data']);
        return $systemUserAll;
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getList($where, $order=null, $limit=null, $type=null)
    {
        //参数处理
        $where = $this->dispostWhere($where);
        //获取Model数据
        $field = array(
            "{$this->DB_PREFIX}system_user.system_user_id",
            "{$this->DB_PREFIX}system_user.username",
            "{$this->DB_PREFIX}system_user.realname",
            "{$this->DB_PREFIX}system_user.sign",
            "{$this->DB_PREFIX}system_user.face",
            "{$this->DB_PREFIX}system_user.email",
            "{$this->DB_PREFIX}system_user.sex",
            "{$this->DB_PREFIX}system_user.check_id",
            "{$this->DB_PREFIX}system_user.isuserinfo",
            "{$this->DB_PREFIX}system_user.usertype",
            "{$this->DB_PREFIX}system_user.logintime",
            "{$this->DB_PREFIX}system_user.loginip",
            "{$this->DB_PREFIX}zone.zone_id",
            "{$this->DB_PREFIX}zone.level as zonelevel",
            "{$this->DB_PREFIX}zone.name as zonename",
            "{$this->DB_PREFIX}system_user.createtime",
            "{$this->DB_PREFIX}system_user.createip",
            "{$this->DB_PREFIX}system_user_engaged.status as engaged_status"
        );
        $join = 'LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id
                 LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id';
        if(!empty($order)){
            $order = "{$this->DB_PREFIX}system_user.".$order;
        }else{
            $order = "{$this->DB_PREFIX}system_user.sign";
        }
        $result = D('SystemUser')->getList($where, $order, $limit, $field, $join);
        //添加多职位
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['realname'] = $v['sign'].'-'.$v['realname'];
                $roles = $this->getSystemUserRole($v['system_user_id']);
                $roleNames = '';
                foreach($roles as $k2=>$v2){
                    if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
                    else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                }
                $result[$k]['role_names'] = $roleNames;
                $result[$k]['roles'] = $roles;
            }
        }

        // 是否添加分配渠道统计
        if(!empty($type)) $result = $this->getInfoqualityCount($result);
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where)
    {
        $where = $this->dispostWhere($where);
        $join = 'LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id';
        //获取Model数据
        $result = D('SystemUser')->getCount($where, $join);
        //返回数据与状态
        return array('code'=>'0', 'data'=>empty($result)?0:$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工信息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($system_user_id)
    {
        $where[$this->DB_PREFIX.'system_user.system_user_id'] = $system_user_id;
        $where[$this->DB_PREFIX.'system_user.status'] = 1;
        $field = array(
            "{$this->DB_PREFIX}system_user.system_user_id",
            "{$this->DB_PREFIX}system_user.username",
            "{$this->DB_PREFIX}system_user.realname",
            "{$this->DB_PREFIX}system_user.nickname",
            "{$this->DB_PREFIX}system_user.face",
            "{$this->DB_PREFIX}system_user.email",
            "{$this->DB_PREFIX}system_user.sex",
            "{$this->DB_PREFIX}system_user.check_id",
            "{$this->DB_PREFIX}system_user.isuserinfo",
            "{$this->DB_PREFIX}system_user.usertype",
            "{$this->DB_PREFIX}system_user_info.plivatemail",
            "{$this->DB_PREFIX}system_user_info.birthday",
            "{$this->DB_PREFIX}system_user_info.nativeplace",
            "{$this->DB_PREFIX}system_user_info.school",
            "{$this->DB_PREFIX}system_user_info.marital",
            "{$this->DB_PREFIX}system_user_info.province_id",
            "{$this->DB_PREFIX}system_user_info.city_id",
            "{$this->DB_PREFIX}system_user_info.area_id",
            "{$this->DB_PREFIX}system_user_info.address",
            "{$this->DB_PREFIX}system_user_info.education_id",
            "{$this->DB_PREFIX}system_user_info.school",
            "{$this->DB_PREFIX}system_user_info.majorname",
            "{$this->DB_PREFIX}system_user_info.entrytime",
            "{$this->DB_PREFIX}system_user_info.straightime",
            "{$this->DB_PREFIX}system_user_info.socialsecurity",
            "{$this->DB_PREFIX}system_user_info.providentfund",
            "{$this->DB_PREFIX}system_user_info.remark",
            "{$this->DB_PREFIX}system_user_info.worktime",
            "{$this->DB_PREFIX}system_user_info.workintroduction",
            "{$this->DB_PREFIX}system_user_info.status as info_status",
            "{$this->DB_PREFIX}zone.zone_id",
            "{$this->DB_PREFIX}zone.level as zonelevel",
            "{$this->DB_PREFIX}zone.name as zonename",
            "{$this->DB_PREFIX}system_user.createtime",
            "{$this->DB_PREFIX}system_user.createip",
            "{$this->DB_PREFIX}system_user_engaged.status as engaged_status"
        );
        $join = 'LEFT JOIN __SYSTEM_USER_INFO__ ON __SYSTEM_USER_INFO__.system_user_id=__SYSTEM_USER__.system_user_id
        LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id
        LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id';
        //获取model数据
        $getInfo = D('SystemUser')->getInfo($where, $field, $join);
        //添加多职位
        $roles = $this->getSystemUserRole($system_user_id);
        if(!empty($roles)){
            $getInfo['user_roles'] = $roles;
            $roleNames = '';
            foreach($roles as $k2=>$v2){
                if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
                else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
            }
            $getInfo['role_names'] = $roleNames;
        }
        return array('code'=>'0', 'data'=>$getInfo, 'msg'=>'操作成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 添加员工
    |--------------------------------------------------------------------------
    | @parameter realname username email zone_id role_id usertype check_id entrytime straightime
    | @author zgt
    */
    public function create($data)
    {
        $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
        $userInfo = D('SystemUser')->where(array('username'=>$data['username']))->find();
        if(!empty($userInfo)) return array('code'=>'3', 'msg'=>'该手机号码已被注册', 'sign'=>'username');
        $userInfoCheck = D('SystemUser')->where(array('check_id'=>$data['check_id']))->find();
        if(!empty($userInfoCheck)) return array('code'=>'3', 'msg'=>'该指纹编号已存在', 'sign'=>'check_id');
        $result = C('SYSTEM_USER_ORDER');
        $sign = mb_substr($data['realname'], 0, 1, "UTF-8");
        $res = array_keys($result);
        if (in_array($sign, $res)) {
            $data['sign'] = $result[$sign];
        }
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        //启动事务
        D()->startTrans();
        $result = D('SystemUser')->field('realname,username,email,sex,check_id,zone_id,usertype,createtime,createip,sign')->data($data)->add();
        if(!empty($data['role_id'])){
            $where_role = array();
            $add_role = explode(',',$data['role_id']);
            foreach($add_role as $k=>$v){
                $where_role[] = array('role_id'=>$v,'user_id'=>$result);
            }
            $flag_addrole = D('RoleUser')->addAll($where_role);
        }else{
            $flag_addrole = true;
        }
        $data['system_user_id'] = $result;
        $flag_addinfo = D('SystemUserInfo')->field('system_user_id,entrytime,straightime')->data($data)->add();
        $flag_addengaged = D('SystemUserEngaged')->data(array('system_user_id'=>$result,'status'=>2))->add();
        if($result && $flag_addrole && $flag_addinfo && $flag_addengaged){
            D()->commit();
            F('Cache/Personnel/system_list',null);
            F('Cache/Personnel/role',null);
            F('Cache/Personnel/roleUser',null);
            return array('code'=>'0', 'msg'=>'操作成功');
        }else{
            D()->rollback();
            return array('code'=>'1', 'msg'=>'数据操作失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工信息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function edit($data)
    {
        if(empty($data['system_user_id'])) return array('code'=>'11', 'msg'=>'参数异常');
        $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
        //是否修改手机号码 是：清空密码
        $userInfo = D('SystemUser')->where(array('system_user_id'=>$data['system_user_id']))->find();
        if($userInfo['username']!=$data['username']){
            $data['password'] == '';
            $isUsername = D('SystemUser')->where(array('username'=>$data['username']))->find();
            if(!empty($isUsername)) return array('code'=>'3', 'msg'=>'该手机号码已被注册', 'sign'=>'username');
        }
        $userInfoCheck = D('SystemUser')->where(array('check_id'=>$data['check_id']))->find();
        if(!empty($userInfoCheck)) {
            if($userInfoCheck['system_user_id'] != $data['system_user_id']){
                return array('code'=>'11', 'msg'=>'该指纹编号已存在', 'sign'=>'check_id');
            }
        }
        //启动事务
        D()->startTrans();
        $result = D('SystemUser')->field('realname,username,face,password,check_id,email,emailpassword,sex,zone_id,usertype,createtime,status')->where("system_user_id = {$data['system_user_id']}")->save($data);
        if(!empty($data['role_id'])){
            $where_role['user_id'] = $data['system_user_id'];
            D('RoleUser')->where($where_role)->delete();
            $where_role = array();
            $add_role = explode(',',$data['role_id']);
            foreach($add_role as $k=>$v){
                $where_role[] = array('role_id'=>$v,'user_id'=>$data['system_user_id']);
            }
            $flag_addrole = D('RoleUser')->addAll($where_role);
        }else{
            $flag_addrole = true;
        }
        $flag_addinfo = D('SystemUserInfo')->field('entrytime,straightime')->where(array('system_user_id'=>$data['system_user_id']))->save($data);

        if($result!==false && $flag_addrole!==false && $flag_addinfo!==false){
            D()->commit();
            F('Cache/Personnel/system_list',null);
            F('Cache/Personnel/role',null);
            F('Cache/Personnel/roleUser',null);
            return array('code'=>'0', 'msg'=>'操作成功');
        }else{
            D()->rollback();
            return array('code'=>'1', 'msg'=>'数据操作失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 员工信息 删除/离职
    |--------------------------------------------------------------------------
    | @parameter system_user_id flag
    | @author zgt
    */
    public function del($data)
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
            F('Cache/Personnel/system_list',null);
            F('Cache/Personnel/role',null);
            F('Cache/Personnel/roleUser',null);
            return array('code'=>'0', 'msg'=>'数据操作失败');
        }
        D()->rollback();
        return array('code'=>'1', 'msg'=>'操作成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工 是否有客户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserCount($where)
    {
        $numCount = D('User')->where(array('system_user_id'=>$where['system_user_id'],'status'=>array('IN','20,30')))->count();
        if($numCount>0){
            return array('code'=>'0', 'data'=>$numCount, 'msg'=>'该员工下面有客户，是否需要带走客户');
        }
        return array('code'=>'1', 'msg'=>'该员工下无客户');
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工 所属客户地区
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editUserZone($where)
    {
        $userInfo = D('SystemUser')->field('system_user_id,zone_id')->where(array('system_user_id'=>$where['system_user_id']))->find();
        $flag = D('User')->where(array('system_user_id'=>$where['system_user_id'],'status'=>array('IN','20,30')))->save(array('zone_id'=>$userInfo['zone_id']));
        if($flag!==false){
            return array('code'=>'0', 'msg'=>'数据操作失败');
        }
        return array('code'=>'1', 'msg'=>'操作成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 修改员工信息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editInfo($data)
    {    
        if(!empty($sex)) $system_user['sex'] = isset($data['sex'])?$data['sex']:1;
        if(!empty($check_id)) $system_user['check_id'] = isset($data['check_id'])?$data['check_id']:null;
        if(!empty($usertype)) $system_user['usertype'] = isset($data['usertype'])?$data['usertype']:null;
        unset($data['sex']);unset($data['check_id']);unset($data['usertype']);
        //启动事务
        D()->startTrans();
        if ($system_user) {
            //修改SystemUser
            $flag_save = D('SystemUser')->where("system_user_id=$data[system_user_id]")->save($system_user);
        }else{
            $flag_save = true;
        }
        //修改SystemUserInfo
        $flag_saveInfo = D('SystemUserInfo')->where('system_user_id='.$data['system_user_id'])->save($data);
        if($flag_save!==false && $flag_saveInfo!==false){
            D()->commit();
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>'1', 'msg'=>'操作失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工自定义列信息
    |--------------------------------------------------------------------------
    | system_user_id columntype
    | @author zgt
    */
    public function getColumn($data)
    {
        if (S('Cache_columnList_'.$data['system_user_id'].'_'.$data['columntype'])) {
            $result = S('Cache_columnList_'.$data['system_user_id'].'_'.$data['columntype']);
        }else{
            $result = D('SystemUserColumn')->where(array('system_user_id'=>$data['system_user_id'],'columntype'=>$data['columntype']))->order('sort ASC')->select();
            S('Cache_columnList_'.$data['system_user_id'].'_'.$data['columntype'], $result, '300');
        }
        return $result;
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
            S('Cache_columnList_'.$data['system_user_id'].'_'.$data['columntype'], null);
            D()->commit();
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>'1', 'msg'=>'操作失败');
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
        $roleController = new RoleMain();
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $roleList = $roleController->getAllRole();
        $reroles = $roleController->getRoleUserList($system_user_id);

        $rerolesUserList = $roleController->getRoleUserList();
        if($reroles['code']==0){
            $my_roles = array();
            $new_roles = array();
            foreach($reroles['data']['data'] as $k=>$v){
                //数组分级
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
    | 员工获取短信模版
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getSmsTemplate($system_user_id)
    {
        $templateList = D('SmsTemplate')->where(array('system_user_id'=>$system_user_id))->select();
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
        $data['createtime'] = time();
        $add_flag = D('SmsTemplate')->add($data);
        if($add_flag!==false){
            return array('code'=>'0', 'msg'=>'创建成功');
        }
        return array('code'=>'1', 'msg'=>'创建失败');
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
        $save_flag = D('SmsTemplate')->where(array('sms_template_id'=>$data['sms_template_id'],'system_user_id'=>$data['system_user_id']))->save($data);
        if($save_flag!==false){
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        return array('code'=>'1', 'msg'=>'操作失败');
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
        $del_flag = D('SmsTemplate')->where(array('sms_template_id'=>$data['sms_template_id'],'system_user_id'=>$data['system_user_id']))->delete();
        if($del_flag!==false){
            return array('code'=>'0', 'msg'=>'操作成功');
        }
        return array('code'=>'1', 'msg'=>'操作失败');
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
        if(empty($data['user_id'])) return array('code'=>'11', 'msg'=>'参数异常');
        $userInfo = D('User')->field('realname,username')->where(array('user_id'=>$data['user_id']))->find();
        //短信发送
        $query = array(
            'mobile'=>decryptPhone($userInfo['username'], C('PHONE_CODE_KEY')),
            'content'=>trim($data['sendTxt'])
        );
        $apiController = new ApiController();
        $send_flag = $apiController->sendSmsGY($query);
        //添加发送记录
        $send_log = array(
            "touser_id"=>$data['user_id'],
            "system_user_id"=>$data['system_user_id'],
            "sendtime"=>time(),
            'content'=>$data['sendTxt'],
            'sendstatus'=>$send_flag['code'],
            'senderror'=>$send_flag['msg']
        );
        D('SmsLogs')->add($send_log);
        if($send_flag['code']==0){
            return array('code'=>'0', 'msg'=>'短信发送成功');
        }
        return array('code'=>'1', 'msg'=>$send_flag['msg']);
    }


    /**
     * 获取员工统计信息质量
     * @author zgt
     */
    protected function getInfoqualityCount($systemList)
    {
        //渠道列表
        $channelMain = new ChannelController();
        $channeList = $channelMain->getList();
        $channelList = $channeList['data']['data'];
        $newChannelArr = array();
        foreach($channelList as $k=>$v){
            $newChannelArr[$v['channelname']][] = $v['channel_id'];
            if(!empty($v['children'])){
                foreach($v['children'] as $k2=>$v2){
                    $newChannelArr[$v['channelname']][] = $v2['channel_id'];
                }
            }
        }
        foreach ($systemList as $key => $sysUser) {
            $i=0;
            foreach($newChannelArr as $k2=>$v2){
                $systemList[$key]['count'][$i]['channelname'] = $k2;
                $where['channel_id'] = array('IN',$v2);
                $where['system_user_id'] = $sysUser['system_user_id'];
                $where['date'] = date('Ymd');
                $countA = D('UserAllocationLogs')->where($where)->sum('infoqualitya');
                $countB = D('UserAllocationLogs')->where($where)->sum('infoqualityb');
                $countC = D('UserAllocationLogs')->where($where)->sum('infoqualityc');
                $countD = D('UserAllocationLogs')->where($where)->sum('infoqualityd');
                $systemList[$key]['count'][$i]['countA'] = (!empty($countA))?$countA:0;
                $systemList[$key]['count'][$i]['countB'] = (!empty($countB))?$countB:0;
                $systemList[$key]['count'][$i]['countC'] = (!empty($countC))?$countC:0;
                $systemList[$key]['count'][$i]['countD'] = (!empty($countD))?$countD:0;
                $i++;
            }
        }
        return $systemList;
    }

    /**
     * 参数过滤
     * @author zgt
     */
    protected function dispostWhere($where)
    {
        $where = array_filter($where);
        foreach($where as $k=>$v){
            if($k=='role_id' && $v!=0){
                $ids = $this->getRoleIds($v);
                if(empty($ids)) $ids='0';
                $where["{$this->DB_PREFIX}system_user.system_user_id"] = array('IN', $ids);
            }elseif(!empty($v)){
                $where["{$this->DB_PREFIX}system_user.".$k] = $v;
            }
            unset($where[$k]);
        }
        if(!empty($where["{$this->DB_PREFIX}system_user.zone_id"])){
            $zoneIdArr = $this->getZoneIds($where["{$this->DB_PREFIX}system_user.zone_id"]);
            $where[$this->DB_PREFIX.'system_user.zone_id'] = array('IN',$zoneIdArr);
            unset($where['zone_id']);
        }

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
     * 添加登录日志
     * @author zgt
     */
    protected function addSystemUserLogs($systemUserId)
    {
        $len = 32;
        $add_log['token'] = $this->_createToken($len);
        session("token", $add_log['token']);
        $add_log['logintime'] = time();
        $add_log['loginip'] = get_client_ip();
        //获取API
        $apiController = new ApiController();
        $reApi = $apiController->getApiIplookup($add_log['loginip']);
        if($reApi['code']==0 && $reApi['data']['city']!=''){
            $add_log['city'] = $reApi['data']['city'];
            $add_log['district'] = $reApi['data']['county'];
        }
        $addflag = D('SystemUser')->where("system_user_id = $systemUserId")->save($add_log);
        if($addflag!==false){
            $add_log['system_user_id'] = $systemUserId;
            //开启登录告警
            if(!empty($add_log['city'])){
                $reAlarm = $this->SystemUserLogsAlarm($systemUserId,$reApi);
                if($reAlarm===true){
                    $add_log['status'] = 1;
                }
            }
            return D('SystemUserLogs')->data($add_log)->add();
        }

    }

    /**
     * 是否开启登录告警
     * @author zgt
     */
    public function SystemUserLogsAlarm($systemUserId,$data)
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
                $apiController = new ApiController();
                $send_flag = $apiController->sendSms('alarm', $send_data);
                if($send_flag['code']==0){
                    //开启警告验证
                    session('login_alarm',true);
                    //主管发送短信
                    $role_id = D('RoleUser')->field('role_id')->where(array('user_id'=>$systemUserId))->find();
                    $superiorid = D('Role')->field('superiorid')->where(array('id'=>$role_id))->find();
                    if(!empty($superiorid)){
                        //记录已发送数组
                        $send_username = array();
                        $superiorUser = D('RoleUser')->field('username')->where(array('role_id'=>$superiorid))->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ROLE_USER__.user_id')->select();
                        if(!empty($superiorUser)){
                            foreach($superiorUser as $v){
                                $sendsuperior_data = array(
                                    "time"=>date("Y-m-d H:i:s", $time),
                                    "city"=>$data['data']['city'],
                                    "realname"=>$info['realname'],
                                    "mobile"=>decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                );
                                $apiController->sendSms('alarmsuperior', $sendsuperior_data);
                                $send_username[] = $v['username'];
                            }
                        }
                        //额外通知高层人员
                        if(C('SMSHINT_USER')){
                            $smshint_user = C('SMSHINT_USER');
                            $smshint_list = D('SystemUser')->field('username')->where(array('system_user_id'=>array('IN',$smshint_user)))->select();
                            if(!empty($smshint_list)){
                                foreach($smshint_list as $v){
                                    if(!in_array($v['username'],$send_username)){
                                        $sendsuperior_data = array(
                                            "time"=>date("Y-m-d H:i:s", $time),
                                            "city"=>$data['data']['city'],
                                            "realname"=>$info['realname'],
                                            "mobile"=>decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                        );
                                        $apiController->sendSms('alarmsuperior', $sendsuperior_data);
                                    }
                                }
                            }
                        }
                    }
                    return true;
                }
                return true;
            }elseif(in_array(date('H'), array('23','24','1','2','3','4','5','6','7'))){
                $info = D('SystemUser')->field('username,realname')->where(array('system_user_id'=>$systemUserId))->find();
                $apiController = new ApiController();
                $time = time();
                $role_id = D('RoleUser')->field('role_id')->where(array('user_id'=>$systemUserId))->find();
                $superiorid = D('Role')->field('superiorid')->where(array('id'=>$role_id))->find();
                if(!empty($superiorid) && $superiorid!=0) {
                    //记录已发送数组
                    $send_username = array();
                    $superiorUser = D('RoleUser')->field('username')->where(array('role_id' => $superiorid))->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ROLE_USER__.user_id')->select();
                    if (!empty($superiorUser)) {
                        foreach ($superiorUser as $v) {
                            $sendsuperior_data = array(
                                "time" => date("Y-m-d H:i:s", $time),
                                "realname" => $info['realname'],
                                "mobile" => decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                            );
                            $apiController->sendSms('alarmlatesuperior', $sendsuperior_data);
                            $send_username[] = $v['username'];
                        }
                    }
                }
                //额外通知高层人员
                if(C('SMSHINT_USER')){
                    $smshint_user = C('SMSHINT_USER');
                    $smshint_list = D('SystemUser')->field('username')->where(array('system_user_id'=>array('IN',$smshint_user)))->select();
                    if(!empty($smshint_list)){
                        foreach($smshint_list as $v){
                            if(!in_array($v['username'],$send_username)){
                                $sendsuperior_data = array(
                                    "time" => date("Y-m-d H:i:s", $time),
                                    "realname" => $info['realname'],
                                    "mobile" => decryptPhone($v['username'], C('PHONE_CODE_KEY')),
                                );
                                $apiController->sendSms('alarmlatesuperior', $sendsuperior_data);
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

    /*
     * userid=>获取员工权限
     * @author zgt
     * @return array
     */
    protected function getSystemUserRole($systemUserId){
        return D('RoleUser')
            ->field('name,role_id,departmentname')
            ->where(array('user_id'=>$systemUserId))
            ->join('__ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->select();
    }
}
