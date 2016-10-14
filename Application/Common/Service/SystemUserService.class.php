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
        $verify = new Verify();
        //获取 数据判断
        $data = array_filter($data);
        $username = trim($data['username']);
        $password = trim($data['password']);
        if(!$this->checkMobile($username)) return array('code'=>'201', 'data'=>array('sign'=>'username'), 'msg'=>'手机号码格式有误');
        if(!$verify->check($data['verification'],'login')) return array('code'=>'202', 'data'=>array('sign'=>'verification'), 'msg'=>'验证码不正确');
        //数据加密
        $username = encryptPhone($username, C('PHONE_CODE_KEY'));
        $user_info = D('SystemUser')->getFind(array('username'=>$username));
        if(empty($user_info)) return array('code'=>'101', 'data'=>array('sign'=>'username'), 'msg'=>'该用户名未注册');
        if(empty($user_info['password'])) return array('code'=>'102', 'data'=>array('sign'=>'username'), 'msg'=>'您的账户尚未激活,请点击下方激活按钮');
        if($user_info['status']!=1) return array('code'=>'103', 'data'=>array('sign'=>'username'), 'msg'=>'该账号已无效，请联系管理员');
        if($user_info['usertype']==10) return array('code'=>'104', 'data'=>array('sign'=>'username'), 'msg'=>'该员工已离职，已无法登录OA系统');
        if($user_info['password'] !== passwd($password)) return array('code'=>'105', 'data'=>array('sign'=>'password'), 'msg'=>'密码错误');
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
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'登录成功');
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
    public function getUserDefaultNodes($param){
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
        $user_role = $this->getSystemUserRole(array('system_user_id'=>$userInfo['system_user_id']));        
        $newArr = array('userInfo'=>$userInfo,'userRole'=>$user_role['data']);
        return array('code'=>'0', 'data'=>$newArr, 'msg'=>'激活成功');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getSystemUserList($where, $order=null, $limit=null, $type=null)
    {
        //参数处理
        $where = $this->_dispostWhere($where);
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
                $user_role = $this->getSystemUserRole(array('system_user_id'=>$v['system_user_id']));
                foreach($user_role['data'] as $k2=>$v2){
                    if($k2==0) {
                        $roleNames = $v2['departmentname'].'/'.$v2['name'];
                        $roleName = $v2['name'];
                    }else{
                        $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                        $roleName .= '，'.$v2['name'];
                    }
                }
                $result[$k]['role_names'] = $roleNames;
                $result[$k]['rolename'] = $roleName;
                $result[$k]['roles'] = $user_role['data'];
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
        $where = $this->_dispostWhere($where);
        $join = 'LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id';
        //获取Model数据
        $result = D('SystemUser')->getCount($where, $join);
        //返回数据与状态
        return array('code'=>'0', 'data'=>empty($result)?0:$result);
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
        if(F('Cache/system') && empty($where['realname'])){
            $systemUserAll = F('Cache/system');
        }else{
            if(!empty($where['realname'])){
                $systemUserList = D('SystemUser')->getList($where);
            }else{
                $systemUserList = D('SystemUser')->getList(array('usertype'=>array('neq',10)));
            }
            $systemUserCount = D('SystemUser')->getCount(array('usertype'=>array('neq',10)));
            $systemUserAll['data'] = $systemUserList['data'];
            $systemUserAll['count'] = $systemUserCount['data'];
            if(empty($where['realname'])){
                F('Cache/system',$systemUserAll);
            }
        }
        if(!empty($where['zone_id'])){
            $zoneIdArr = array();
            $zoneIdArr = $this->_getZoneIds($where['zone_id']);
            foreach($systemUserAll['data'] as $k=>$v){
                if(!empty($where['zone_id']) && !in_array($v['zone_id'],$zoneIdArr)){
                    unset($systemUserAll['data'][$k]);
                }
                if(!empty($where['role_id'])){
                    $in_flag = false;
                    foreach($v['roles'] as $k2=>$v2){
                        if($v2['role_id']==$where['role_id'] ){
                            $in_flag = true;
                        }
                    }
                    if($in_flag===false) unset($systemUserAll['data'][$k]);
                }
            }
        }
        if($limit!==null){
            $limit = explode(',',$limit);
            $systemUserAll['data'] = array_slice($systemUserAll['data'], $limit[0], $limit[1]);
        }
        if(!empty($where['system_user_id'])){
            foreach($systemUserAll['data'] as $k=>$v){
                if($v['system_user_id']==$where['system_user_id']){
                    $systemUserAll['data'] = $v;
                }
            }
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$systemUserAll);
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
     * userid=>获取员工权限
     * @author zgt
     * @return array
     */
//    protected function getSystemUserRole($systemUserId){
//        return D('RoleUser')
//            ->field('name,role_id,departmentname')
//            ->where(array('user_id'=>$systemUserId))
//            ->join('__ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
//            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
//            ->select();
//    }

    /*
     * 添加登录日志
     * @author zgt
     * @return false
     */
    protected function addSystemUserLogs($systemUserId){
        $add_log['logintime'] = time();
        $add_log['loginip'] = get_client_ip();
        $addflag = D('SystemUser')->editData($add_log,$systemUserId);
        if($addflag['code']==0){
            $add_log['system_user_id'] = $systemUserId;
            $reflag = D('SystemUserLogs')->addData($add_log);
            if($reflag['code']==0){
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
            }
        }
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
        if(!empty($param['where']['zone_id'])){
            $_zone_arr = $this->_getZoneIds($param['where']['zone_id']);
            $param['where']['zone_id'] = array('IN', $_zone_arr);
        }
        $param['page'] = !empty($param['page'])?$param['page']:'1,30';
        if( F('Cache/systemUsers') ) {
            $systemUsers = F('Cache/systemUsers');
        }else{
            $systemUsers = $this->_getSystemUserList();
            F('Cache/systemUsers', $systemUsers);
        }
        $systemUsers = $this->disposeArray($systemUsers,  $param['order'], $param['page'],  $param['where']);
        return array('code'=>'0', 'data'=>$systemUsers);
    }

    /*
  |--------------------------------------------------------------------------
  | SystemUser 获取客户详情
  |--------------------------------------------------------------------------
  | @author zgt
  */
    public function getSystemUsersInfo($param)
    {
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
            foreach($_array as $k=>$v){
                //标识名称
                $_array[$k]['sign_realname'] = $v['sign'].'-'.$v['realname'];
                //员工状态
                $_array[$k]['usertype_name'] = $systemUserStatus[$v['usertype']];
                //员工状态
                $_array[$k]['username'] = decryptPhone($v['username'],  C('PHONE_CODE_KEY'));
                //区域名称
                $_where_zone['zone_id'] = $v['zone_id'];
                $zone_list = D('Zone','Service')->getZoneInfo($_where_zone);
                $_array[$k]['zone_id'] = $zone_list['data']['zone_id'];
                $_array[$k]['zonelevel'] = $zone_list['data']['level'];
                $_array[$k]['zonename'] = $zone_list['data']['name'];
                //多职位
                $user_role = $this->getSystemUserRole(array('system_user_id'=>$v['system_user_id']));
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
            $new_info = D('SystemUser')->getFind(array("system_user_id"=>$result));
            $new_info = $this->_addStatus($new_info);
            $cahce_all = F('Cache/systemUsers');
            $cahce_all['data'][] = $new_info;
            $cahce_all['count'] =  $cahce_all['count']+1;
            F('Cache/systemUsers', $cahce_all);
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
    | @author nxx
    */
    public function editSystemUser($data)
    {
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
        $system_user_id = !empty($data['system_user_id'])?$data['system_user_id']:$this->system_user_id;
        $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
        //是否修改手机号码 是：清空密码
        $userInfo = D('SystemUser')->where(array('system_user_id'=>$system_user_id))->find();
        if($userInfo['username']!=$data['username']){
            $data['password'] == '';
            $isUsername = D('SystemUser')->where(array('username'=>$data['username']))->find();
            if(!empty($isUsername)) return array('code'=>201, 'msg'=>'该手机号码已被注册');
        }
        $userInfoCheck = D('SystemUser')->where(array('check_id'=>$data['check_id']))->find();
        if(!empty($userInfoCheck)) {
            if($userInfoCheck['system_user_id'] != $system_user_id){
                return array('code'=>202, 'msg'=>'该指纹编号已存在', 'sign'=>'check_id');
            }
        }
         //启动事务
        D()->startTrans();
        $result = D('SystemUser')->editData($data, $system_user_id);
        $flag_userINfo = true;
        if(!empty($data['role_id'])) {
            D('RoleUser')->delData($system_user_id);
            $edit_role = explode(',',$data['role_id']);
            if(!empty($edit_role)){
                foreach($edit_role as $k=>$v){
                    $where_role['role_id'] = $v;
                    $where_role['user_id'] = $system_user_id;
                    D('RoleUser')->addData($where_role);
                }
                $flag_userINfo = D('SystemUserInfo')->editData($data,$system_user_id);
            }
        }
        if($flag_userINfo!==false && $result!==false){
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
        $where[C('DB_PREFIX').'system_user.status'] = 1;
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
        $roles = $this->getSystemUserRole($systemUserInfo['system_user_id']);
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
        $systemUserInfo['birthday_time'] = !empty($systemUserInfo['birthday'])?date('Y-m-d', $systemUserInfo['birthday']):null;

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
        $username = decryptPhone($param['username'], C('PHONE_CODE_KEY'));
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
        $where['system_user_id'] = $this->system_user_id;
        $result = D('SystemUserColumn')->getList($where);
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
        $where['number_status'] = 1;
        $result = D('CallNumber')->getList($where,'call_number_id,number,number_type,number_start');
        foreach($result as $k=>$v){
            $result[$k]['number_type_name'] = $v['number_type']==1?'固定电话':'手机号码';
            $result[$k]['number_start_name'] = $v['number_start']==1?'已启用':'未启用';
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

}
