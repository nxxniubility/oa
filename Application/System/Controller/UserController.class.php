<?php
namespace System\Controller;

use Common\Controller\ChannelController;
use Common\Controller\CourseController;
use Common\Controller\DepartmentController;
use Common\Controller\EducationController;
use Common\Controller\OrderController;
use Common\Controller\RoleController;
use Common\Controller\SystemController;
use Common\Controller\SystemUserController;
use Common\Controller\ProidController;
use Common\Controller\UserController as UserMain;
use Common\Controller\ZoneController;
use Api\Controller\UserController as ApiUser;
use Common\Service\ApiService;
use Common\Service\UserService;

class UserController extends SystemController
{


   /*
   |--------------------------------------------------------------------------
   | 客户列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function userList()
    {
        //获取参数 页码
        $request = $where = I('get.');
        //参数判断
        $where['system_user_id|updateuser_id'] = $this->system_user_id;
        $re_page = isset($where['page']) ? $where['page'] : 1;
        unset($request['page']);unset($where['page']);unset($where['type']);unset($where['_pjax']);
        if ($where['status'] == 0) {
            $where['status'] = array('IN', array(20, 30, 70, 120));
        }
        //排序
        if ($where['status'] == 30){
            $order = 'nextvisit ASC';
        }else{
            $order = 'createtime DESC';
        }
        //日期转换时间戳
        foreach ($where as $k => $v) {
            if (!empty($where[$k]) && $where[$k]!='1@0') {
                if ($k == 'allocationtime' || $k == 'updatetime' || $k == 'lastvisit' || $k == 'nextvisit' || $k == 'visittime' || $k == 'createtime') {
                    $_time = explode('@', str_replace('/', '-', $where[$k]));
                    $where[$k] = array(array('EGT', ($_time[1] == 'time' ? time() : strtotime($_time[1]))), array('LT', ($_time[2] == 'time' ? time() : strtotime($_time[2] . ' 23:59:59'))), 'AND');
                }
            }else{
                unset($where[$k]);
            }
        }
        //客户列表
        $userMain = new UserMain();
        $re_userAll = $userMain->getList($where, $order, (($re_page-1)*30).',30');
        $data['userAll'] = $re_userAll['data'];
        //获取总数
        $result = $userMain->getCount($where);
        //加载分页类
        $data['paging_data'] = $this->Paging($re_page, 30, $result['data'], $request, __ACTION__, null, 'system');
        //获取自定义列
        $systemUserMain = new SystemUserController();
        $column_data['columntype'] = 1;
        $column_data['system_user_id'] = $this->system_user_id;
        $data['column'] = $systemUserMain->getColumn($column_data);
        //获取职位及部门
        $departmentMain = new DepartmentController();
        $data['departmentAll'] = $departmentMain->getList();
        $roleMain = new RoleController();
        $data['roleAll'] = $roleMain->getAllRole();
        //学习平台
        $data['learningtype'] = C('USER_LEARNINGTYPE');
        //跟进结果
        $data['attitude'] = C('USER_ATTITUDE');
        //缴费方式
        $data['receivetype'] = C('USER_RECEIVETYPE');
        //渠道列表
        $channelMain = new ChannelController();
        $channeList = $channelMain->getList();
        $data['channel'] = $channeList['data'];
        //课程列表
        $courseMain = new CourseController();
        $courseList = $courseMain->getList();
        $data['courseAll'] = $courseList['data'];
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('USER_INFOQUALITY');
        $data['url'] = U('System/User/userList');
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加客户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUser()
    {
        if (IS_POST) {
            //获取参数请求
            $request = I('post.');
            $request['system_user_id'] = $this->system_user_id;
            $request['zone_id'] = $this->system_user['zone_id'];
            //添加客户
            $ApiUser = new ApiUser();
            $reflag = $ApiUser->addUser($request);
            //返回数据操作状态 是否前台添加的客户
            $type = I('get.type');
            if ($type == 'visit') {
               if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            } else {
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], U('System/User/userList'));
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            }
        }
        $request = I('get.');
        //课程列表
        $courseMain = new CourseController();
        $courseList = $courseMain->getList();
        $data['course'] = $courseList['data'];
        //渠道列表
        $channelMain = new ChannelController();
        $channeList = $channelMain->getList();
        $data['channel'] = $channeList['data'];
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 客户详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function detailUser()
    {
        $user_id = I('get.id',null);
        $keyname = I('get.keyname', null);
        $search = I('get.search', null);
        $type = I('get.type', null);
        $isAuditList = I('get.auditList',null);
        //非法操作
        if (empty($user_id) && empty($search)) $this->error('非法操作', 1, U('System/Index/main'));
        //搜索入口
        if (!empty($search) && !empty($keyname)) $user_id = $this->searchUser($keyname, $search);
        //查看权限
        $callbackType = 1;
        if(!empty($type)){
            if($type=='library')$callbackType = 2;
        }
        //数据处理
        if (IS_POST) {
            $request = I('post.');
            //修改用户信息
            if ($request['type'] == 'edituser') {
                $request['user_id'] = $user_id;
                $request['system_user_id'] = $this->system_user_id;
                $ApiUser = new ApiUser();
                $reflag = $ApiUser->editUser($request);
                if($reflag['code']==0){
                    if(!empty($request['remark'])){
                        $_request['user_id'] = $user_id;
                        $_request['system_user_id'] = $this->system_user_id;
                        $_request['remark'] = $request['remark'];
                        $reflag_info = $ApiUser->editUser($_request);
                    }
                    //返回数据操作状态
                    if ($reflag_info['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                    else  $this->ajaxReturn(1, $reflag_info['msg'], '', !empty($reflag_info['sign']) ? $reflag_info['sign'] : '');
                }else{
                    $this->ajaxReturn(1, $reflag['msg']?$reflag['msg']:'数据操作失败');
                }
                 //修改用户详情
            } else if ($request['type'] == 'editinfo') {
                //实例验证类
                $checkform = new \Org\Form\Checkform();
                //年龄转换时间戳
                if (!empty($request['birthday'])) $request['birthday'] = strtotime("-{$checkform->getStrInt($request['birthday'])} year");
                $request['user_id'] = $user_id;
                $request['system_user_id'] = $this->system_user_id;
                $ApiUser = new ApiUser();
                $reflag = $ApiUser->editUserInfo($request);
                //返回数据操作状态
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            } else if ($request['type'] == 'addcallback') {
                $request['nexttime'] = strtotime(($request['nextvisit']). ' ' .($request['nextvisit_hi']));
                $request['user_id'] = $user_id;
                $request['system_user_id'] = $this->system_user_id;
                $ApiUser = new ApiUser();
                $reflag = $ApiUser->addCallback($request,1);
                //返回数据操作状态
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            } else if ($request['type'] == 'getFeeLogs') {
                //缴费方式
                $getWhere['user_id'] = $user_id;
                $orderController = new OrderController();
                $userOrder = $orderController->getUserOrder($getWhere);
                //返回数据操作状态
                if ($userOrder['code'] == 0) $this->ajaxReturn(0, '', $userOrder['data']);
                else  $this->ajaxReturn($userOrder['code'], $userOrder['msg']);
            } else if ($request['type'] == 'getSmsLogs') {
                //短信记录
                $getWhere['user_id'] = $user_id;
                $ApiUser = new ApiUser();
                $userLog = $ApiUser->getUserSmsLog($getWhere,$callbackType);
                //返回数据操作状态
                if ($userLog['code'] == 0) $this->ajaxReturn(0, '', $userLog['data']);
                else  $this->ajaxReturn($userLog['code'], $userLog['msg']);
            }
        }else{
            //客户详情
            $ApiUser = new ApiUser();
            $userInfo = $ApiUser->getUserInfo(array('user_id'=>$user_id));
            $data['userInfo'] = $userInfo['data'];
            if ($data['userInfo']['status'] != 160 && $data['userInfo']['system_user_id'] == $this->system_user_id) $data['isSelf'] = 1;
            //回访记录
            $callbackList = $ApiUser->getUserCallback(array('user_id'=>$user_id,'callbackType'=>$callbackType));
            $data['callbackList'] = $callbackList['data'];
            //通话记录
            $UserService = new UserService();
            $call_List = $UserService->getCallList(array('user_id'=>$user_id,'system_user_id'=>$this->system_user_id,'rank'=>$callbackType));
            $data['call_List'] = $call_List['data'];
            //获取学历表
            $educationMain = new EducationController();
            $educationList = $educationMain->getList();
            $data['educationAll'] = $educationList['data'];
            //课程列表
            $courseMain = new CourseController();
            $courseList = $courseMain->getList();
            $data['course'] = $courseList['data'];
            //渠道列表
            $channelMain = new ChannelController();
            $channeList = $channelMain->getList();
            $data['channel'] = $channeList['data'];
            //获取职位及部门
            $departmentMain = new DepartmentController();
            $data['departmentAll'] = $departmentMain->getList();
            $roleMain = new RoleController();
            $data['roleAll'] = $roleMain->getAllRole();
            //学习平台
            $data['learningtype'] = C('USER_LEARNINGTYPE');
            //跟进结果
            $data['attitude'] = C('USER_ATTITUDE');
            //回访方式
            $data['callback'] = C('USER_CALLBACK');
            //邀约状态转换
            $data['USER_STATUS'] = C('USER_STATUS');
            //信息质量转换
            $data['USER_INFOQUALITY'] = C('USER_INFOQUALITY');
            //学习平台转换
            $data['USER_LEARNINGTYPE'] = C('USER_LEARNINGTYPE');
            //学习方式转换
            $data['USER_STUDYTYPE'] = C('USER_STUDYTYPE');
            //跟进结果转换
            $data['USER_ATTITUDE'] = C('USER_ATTITUDE');
            //回访方式转换
            $data['USER_CALLBACK'] = C('USER_CALLBACK');
            //缴费方式
            $data['USER_RECEIVETYPE'] = C('USER_RECEIVETYPE');
            //贷款机构
            $data['USER_LOAN_INSTITUTIONS'] = C('USER_LOAN_INSTITUTIONS');
            //异步按钮操作地址
            $data['url_dispost'] = U('System/User/dispostUser');
            //转出客户操作
            $data['url_allocationUser'] = U('System/User/allocationUser');
            $data['user_id'] = $user_id;
            $data['type'] = $type;
            //判断是否从审核列表进来的  cq
            if(!empty($isAuditList)){
               $data['isAuditList'] = $isAuditList;
            }
            $this->assign('data', $data);
            $this->display();
        }
    }

    /*
    * 申请转入（客户详情） 异步处理方法
    * @author zgt
    */
    public function applyUser()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $page = I('post.page',1);
            $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
            $where['status'] = 1;
            $where['usertype'] = array('neq', 10);
            if(!empty($request['search'])) $where['realname'] = array('like','%'.$request['search'].'%');
            if(!empty($request['role_id'])) $where['role_id'] = $request['role_id'];
            $systemUserMain = new SystemUserController();
            $reSystemList = $systemUserMain->getListCache($where, null, (($page-1)*10).",10", 1);
            //返回数据操作状态
            if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList);
            else  $this->ajaxReturn(1);
        } else if ($request['type'] == 'submit') {
            $userMain = new UserMain();
            $request['system_user_id'] = $this->system_user_id;
            $reflag = $userMain->applyUser($request);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
            else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
        }
    }

    /*
    * 客户搜索
    * @author zgt
    */
    protected function searchUser($keyname,$search)
    {
       $search = trim($search);
       if($keyname=='username'){
           $where[$keyname] = encryptPhone($search, C('PHONE_CODE_KEY'));
       }else{
           $where[$keyname] = $search;
       }
       if($keyname!='user_id'){
           $reUser = D('User')->getUser($where);
           if (empty($reUser)) {
               $this->assign('search', $search);
               $this->display('Error/notSearch');
               exit();
           }
           $user_id = $reUser['user_id'];
       }else{
           $user_id = $search;
       }
       return $user_id;
    }

    /*
    * 发送短信（客户列表） 异步处理方法
    * @author zgt
    */
    public function sendSms()
    {
        $request = I('post.');
        $request['system_user_id'] = $this->system_user_id;
        if($request['type']=='getTemplate'){
            $systemUserController = new SystemUserController();
            $result = $systemUserController->getSmsTemplate($this->system_user_id);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='createTemplate'){
            $request['template'] = trim($request['template']);
            $systemUserController = new SystemUserController();
            $result = $systemUserController->createSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='editTemplate'){
            $request['template'] = trim($request['template']);
            $systemUserController = new SystemUserController();
            $result = $systemUserController->editSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='delTemplate'){
            $systemUserController = new SystemUserController();
            $result = $systemUserController->delSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='send'){
            $system_user = $this->system_user;
            $request['myname'] = $system_user['realname'];
            $systemUserController = new SystemUserController();
            $result = $systemUserController->sendSmsUser($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }
    }

    /*
    * 赎回客户（客户详情） 异步处理方法
    * @author zgt
    */
    public function redeemUser()
    {
        $request = I('post.');
        //获取转入申请不可分配的数据
        $request['system_user_id'] = $this->system_user_id;
        $ApiUser = new ApiUser();
        $reflag = $ApiUser->redeemUser($request);
        //返回数据操作状态
        if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
        else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
    }

    /*
    * 呼叫客户（客户详情） 异步处理方法
    * @author zgt
    */
    public function callUser()
    {
        $param = I('post.');
        $UserService = new UserService();
        if($param['type']=='getcall'){
            $reflag = $UserService->getCall();
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
            else  $this->ajaxReturn(1, $reflag['msg'], $reflag['data']);
        }elseif($param['type']=='calltel'){
            //只拨固定电话
            $param['system_user_id'] = $this->system_user_id;
            $reflag = $UserService->callUser($param,2);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
            else  $this->ajaxReturn(1, $reflag['msg'], $reflag['data']);
        }elseif($param['type']=='callphone'){
            //拨打电话
            $param['system_user_id'] = $this->system_user_id;
            $reflag = $UserService->callUser($param);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
            else  $this->ajaxReturn(1, $reflag['msg'], $reflag['data']);
        }
    }

    /*
    * 创建订单（客户列表/客户详情） 异步处理方法
    * @author zgt
    */
    public function createOrder()
    {
        $request = I('post.');
        $orderController = new OrderController();
        //是否提示
        if($request['type']=='ishint'){
            $isAuditOrder = $orderController->isUserOrder($request);
            if($isAuditOrder['code']!=0) $this->ajaxReturn('20', '该客户有未完成订单，请查询详情后再操作！');
            $this->ajaxReturn('0', '创建订单时需注意检查客户名称是否有误！');
        }
        if (empty($request['realname'])) $this->ajaxReturn(1, '真实姓名不能为空', '', 'reserve_realname');
        if (empty($request['username'])) $this->ajaxReturn(1, '手机号码不能为空', '', 'reserve_username');
        if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$request['subscription'])){
            $this->ajaxReturn(3,"请输入正确的订金金额");
        }
        //添加参数
        $request['system_user_id'] = $this->system_user_id;
        $request['zone_id'] = $this->system_user['zone_id'];
        //获取接口
        $refalg = $orderController->createOrder($request);
        if ($refalg['code']==0){
            $this->ajaxReturn(0, '创建订单成功,请等待审核,并且客户已转到“交易中”状态');
        }else{
            $this->ajaxReturn($refalg['code'], $refalg['msg']);
        }
    }

    /*
    * 放弃客户（客户列表/客户详情） 异步处理方法
    * @author zgt
    */
    public function abandonUser()
    {
        $request = I('post.');
        $data['user_id'] = $request['user_id'];
        $data['attitude_id'] = $request['abandon_attitude_id'];
        $data['remark'] = $request['abandon_remark'];
        $data['system_user_id'] = $this->system_user_id;
        $ApiUser = new ApiUser();
        $reflag = $ApiUser->abandonUser($data);
        //返回数据操作状态
        if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
        else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
    }

    /*
    * 设置重点客户（客户列表/客户详情） 异步处理方法
    * @author zgt
    */
    public function editUserMark()
    {
        $request = I('post.');
        $request['system_user_id'] = $this->system_user_id;
        $ApiUser = new ApiUser();
        $reflag = $ApiUser->editUser($request);
        //返回数据操作状态
        if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
        else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
    }

    /*
    * 设置自定义显示列（客户列表/客户中心列表） 异步处理方法
    * @author zgt
    */
    public function editColumn()
    {
        $request = I('post.');
        $request['system_user_id'] = $this->system_user_id;
        $systemUserController = new SystemUserController();
        $reflag = $systemUserController->editColumn($request);
        //返回数据操作状态
        if ($reflag['code']==0) {
            $this->ajaxReturn(0, '自定义显示列设置成功');
        } else {
            $this->ajaxReturn(1, '设置失败');
        }
    }

    /*
    * 确认到访（客户列表/客户详情） 异步处理方法
    * @author zgt
    */
    public function affirmVisit()
    {
        $request = I('post.');
        $request['system_user_id'] = $this->system_user_id;
        $ApiUser = new ApiUser();
        $reflag = $ApiUser->affirmVisit($request);
        //返回数据操作状态
        if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
        else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
    }

    /*
    * 转出客户（客户列表/客户详情） 异步处理方法
    * @author zgt
    */
    public function allocationUser()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $page = I('post.page',1);
            $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
            $where['status'] = 1;
            $where['usertype'] = array('neq', 10);
            if(!empty($request['search'])) $where['realname'] = array('like','%'.$request['search'].'%');
            $where['role_id'] = (!empty($request['role_id']))?$request['role_id']:0;
            $systemUserMain = new SystemUserController();
            $reSystemList = $systemUserMain->getListCache($where, null, (($page-1)*8).",8", 1);
            //返回数据操作状态
            if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList);
            else  $this->ajaxReturn(1);
        } else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $ApiUser = new ApiUser();
            $userInfo = $ApiUser->getUserInfo($request);
            $request['system_user_id'] = $userInfo['data']['system_user_id'];
            $reflag = $ApiUser->allocationUser($request);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
            else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
        }
    }

    /*
    * 出库客户（客户列表） 异步处理方法
    * @author zgt
    */
    public function restartUser()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $page = I('post.page',1);
            $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
            $where['status'] = 1;
            $where['usertype'] = array('neq', 10);
            if(!empty($request['search'])) $where['realname'] = array('like','%'.$request['search'].'%');
            $where['role_id'] = (!empty($request['role_id']))?$request['role_id']:0;
            $systemUserMain = new SystemUserController();
            $reSystemList = $systemUserMain->getListCache($where, null, (($page-1)*8).",8", 1);
            //返回数据操作状态
            if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList);
            else  $this->ajaxReturn(1);
        } else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $request['system_user_id'] = $this->system_user_id;
            $ApiUser = new ApiUser();
            $reflag = $ApiUser->restartUser($request);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
            else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 订单列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function orderList()
    {
        //获取请求
        $requestG = $where = I('get.');
        $where['system_user_id'] = $this->system_user_id;
        unset($where['page']);
        unset($where['_pjax']);
        //日期转换时间戳
        foreach ($where as $k => $v) {
            if ($k == 'finishtime' || $k == 'createtime') {
                $_time = explode('--', str_replace('/', '-', $where[$k]));
                $where[$k] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');
            }
        }
        if(IS_POST){
            $requestP = I('post.');
            if($requestP['type']=='getPaging') {
                if(!empty($requestP['page'])) $requestG['page'] = $requestP['page'];
                //异步获取分页数据
                $orderMainController = new OrderController();
                $result = $orderMainController->getCount($where);
                //加载分页类
                $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $result['data'], $where, __ACTION__, null, 'system');
                $this->ajaxReturn(0, '', $paging_data);
            }else if($requestP['type']=='getSysUser'){
                //异步获取员工列表
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_id'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                //员工列表
                $systemUserMain = new SystemUserController();
                $systemUserAll = $systemUserMain->getList($whereSystem);
                $systemList = $systemUserAll['data'];
                if($systemList) $this->ajaxReturn(0, '', $systemList);
                else $this->ajaxReturn(1, '');
            }
        }
        $limit = (empty($requestG['page'])?'0':($requestG['page']-1)*30).',30';
        //获取数据
        $orderMainController = new OrderController();
        $result = $orderMainController->getList($where, 'createtime DESC', $limit);
        //获取区域下
        $zoneMain = new ZoneController();
        $data['zoneAll']['children'] = $zoneMain->getZoneList($this->system_user['zone_id']);
        //获取职位及部门
        $departmentMain = new DepartmentController();
        $data['departmentAll'] = $departmentMain->getList();
        $roleMain = new RoleController();
        $data['roleAll'] = $roleMain->getAllRole();
        //获取配置状态值
        $data['order_status'] = C('ORDER_STATUS');
        $data['order_loan_institutions'] = C('USER_LOAN_INSTITUTIONS');
        $data['order_receivetype'] = C('USER_RECEIVETYPE');
        //模版赋值
        $data['order_list'] = $result['data'];
        $data['request'] = $requestG;
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 中心客户管理
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function userLibrary()
    {
        //获取参数 页码
        $request = $where = I('get.');
        //参数判断
        $where['zone_id'] = !empty($where['zone_id'])?$where['zone_id']:$this->system_user['zone_id'];
        $re_page = isset($where['page']) ? $where['page'] : 1;
        unset($request['page']);unset($where['page']);unset($where['type']);unset($where['_pjax']);
        //排序
        if ($where['status'] == 30){
            $order = 'nextvisit ASC';
        }else{
            $order = 'createtime DESC';
        }
        if ($where['status'] == 0) {
            $where['status'] = array('IN', array(20, 30, 70, 160));
        }else{
            $where['status'] = array('IN', $where['status']);
        }
        unset($where['callbackTimeout']);
        //日期转换时间戳
        foreach ($where as $k => $v) {
            if (!empty($where[$k]) && $where[$k]!='1@0') {
                if ($k == 'allocationtime' || $k == 'updatetime' || $k == 'lastvisit' || $k == 'nextvisit' || $k == 'visittime' || $k == 'createtime') {
                    $_time = explode('@', str_replace('/', '-', $where[$k]));
                    if($k=='nextvisit' && $_time[0]=='7'){
                        //跟进超时？  2018-08-22新增
                        $where['nextvisit'] = array('LT',time());
                    }else{
                        $where[$k] = array(array('EGT', ($_time[1] == 'time' ? time() : strtotime($_time[1]))), array('LT', ($_time[2] == 'time' ? time() : strtotime($_time[2] . ' 23:59:59'))), 'AND');
                    }
                }
            }else{
                unset($where[$k]);
            }
        }
        if(IS_POST){
            $requestP = I('post.');
            if($requestP['type']=='getSysUser'){
                //异步获取员工列表
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_id'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                //员工列表
                $systemUserMain = new SystemUserController();
                $reSystemList = $systemUserMain->getListCache($whereSystem);
                //返回数据操作状态
                if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList['data']);
                else $this->ajaxReturn(1, '获取失败');
            }else if($requestP['type']=='getPaging'){
                //异步获取分页数据
                $userMain = new UserMain();
                $result = $userMain->getCount($where);
                //加载分页类
                $paging_data = $this->Paging($re_page, 30, $result['data'], $request, __ACTION__, null, 'system');
                $this->ajaxReturn(0, '', $paging_data);
            }
        }
        //客户列表
        $userMain = new UserMain();
        $re_userAll = $userMain->getList($where, $order, (($re_page-1)*30).',30');
        $data['userAll'] = $re_userAll['data'];
        //总数
        $result = $userMain->getCount($where);
        //加载分页类
        $data['paging_data'] = $this->Paging($re_page, 30, $result['data'], $request, __ACTION__, null, 'system');
        //获取自定义列
        $systemUserMain = new SystemUserController();
        $column_data['columntype'] = 2;
        $column_data['system_user_id'] = $this->system_user_id;
        $data['column'] = $systemUserMain->getColumn($column_data);
        //获取区域下
        $zoneMain = new ZoneController();
        $data['zoneAll']['children'] = $zoneMain->getZoneList($this->system_user['zone_id']);
        //获取职位及部门
        $departmentMain = new DepartmentController();
        $data['departmentAll'] = $departmentMain->getList();
        $roleMain = new RoleController();
        $data['roleAll'] = $roleMain->getAllRole();
        //最近跟进结果
        $data['attitude'] = C('USER_ATTITUDE');
        //学习平台
        $data['learningtype'] = C('USER_LEARNINGTYPE');
        //学习方式
        $data['studytype'] = C('USER_STUDYTYPE');
        //课程列表
        $courseMain = new CourseController();
        $courseList = $courseMain->getList();
        $data['courseAll'] = $courseList['data'];
        //课程列表status
        foreach($courseList['data'] as $k=>$v){
            $data['course_status'][$v['course_id']] = $v['coursename'];
        }
        //渠道列表
        $channelMain = new ChannelController();
        $channeList = $channelMain->getList();
        $data['channel'] = $channeList['data'];
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('USER_INFOQUALITY');
        //预报审核状态
        $data['USER_APPLY_STATUS'] = C('USER_APPLY_STATUS');
        //用户状态转换
        $data['USER_STATUS'] = C('USER_STATUS');
        //跟进结果转换
        $data['USER_ATTITUDE'] = C('USER_ATTITUDE');
        //异步按钮操作地址
        $data['url_dispost'] = U('System/User/dispostUser');
        //转出客户操作
        $data['url_allocationUser'] = U('System/User/allocationUser');
        $data['url'] = U('System/User/userLibrary');
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }



    /*
    * 出库客户（客户中心列表） 异步处理方法
    * @author zgt
    */
    public function restartUserControl()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $page = I('post.page',1);
            $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
            $where['status'] = 1;
            $where['usertype'] = array('neq', 10);
            if(!empty($request['search'])) $where['realname'] = array('like','%'.$request['search'].'%');
            $where['role_id'] = (!empty($request['role_id']))?$request['role_id']:0;
            $systemUserMain = new SystemUserController();
            $reSystemList = $systemUserMain->getListCache($where, null, (($page-1)*8).",8", 1);
            //返回数据操作状态
            if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList);
            else  $this->ajaxReturn(1);
        } else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $request['system_user_id'] = $this->system_user_id;
            $ApiUser = new ApiUser();
            $reflag = $ApiUser->restartUserManage($request);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
            else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
        }
    }

    /*
    * 回库客户（客户中心列表） 异步处理方法
    * @author zgt
    */
    public function abandonUserControl()
    {
        $request = I('post.');
        if (empty($request['abandon_remark'])) $this->ajaxReturn(1, '备注不能为空');
        $data['user_id'] = $request['user_id'];
        $data['attitude_id'] =0;
        $data['remark'] = $request['abandon_remark'];
        $data['system_user_id'] = $this->system_user_id;
        $ApiUser = new ApiUser();
        $reflag = $ApiUser->abandonUserManage($data, 2);
        //返回数据操作状态
        if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
        else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
    }

    /*
    * 转出客户（客户中心列表） 异步处理方法
    * @author zgt
    */
    public function allocationUserControl()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $page = I('post.page',1);
            $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
            $where['status'] = 1;
            $where['usertype'] = array('neq', 10);
            if(!empty($request['search'])) $where['realname'] = array('like','%'.$request['search'].'%');
            $where['role_id'] = (!empty($request['role_id']))?$request['role_id']:0;
            $systemUserMain = new SystemUserController();
            $reSystemList = $systemUserMain->getListCache($where, null, (($page-1)*8).",8", 1);
            //返回数据操作状态
            if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList);
            else  $this->ajaxReturn(1);
        } else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $request['system_user_id'] = $this->system_user_id;
            $ApiUser = new ApiUser();
            $reflag = $ApiUser->allocationUserManage($request);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
            else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function allocationList() {
        if (IS_POST) {
            $request = I('post.');
            if ($request['type'] == 'del') {
                $reflag = D('User')->allocationDel($request['user_allocation_id']);
                if ($reflag !== false) $this->ajaxReturn(0, '分配规则删除成功');
                else $this->ajaxReturn(1, '删除失败');
            }elseif($request['type'] == 'start'){
                $reflag = D('User')->allocationStart($request['user_allocation_id'],$request['start']);
                if ($reflag !== false) $this->ajaxReturn(0, '操作成功');
                else $this->ajaxReturn(1, '操作失败');
            }
        }
        $re_page = I('get.page', 1);
        $zoneIds = D("Zone")->getZoneIds($this->system_user['zone_id']);
        foreach ($zoneIds as $key => $value) {
            $zidString[] = $value['zone_id'];
        }
        $where[C('DB_PREFIX').'user_allocation.zone_id'] = array("IN", $zidString);
        $data['allocationList'] = D('User')->allocationList($where, (($re_page - 1) * 15) . ',15');
        foreach($data['allocationList']['data'] as $k=>$v){
            $data['allocationList']['data'][$k]['channelnames'] = D('Channel')->getChannelNames($v['channel_id']);
            if(!empty($v['allocation_roles'])){
                $_roles = explode(',',$v['allocation_roles']);
                $_rolesName = '';
                foreach($_roles as $v2){
                    $getRole = D('Role')->getRoleInfo($v2);
                    if(empty($_rolesName)){
                        $_rolesName = $getRole['name'];
                    }else{
                        $_rolesName .= ','.$getRole['name'];
                    }
                }
            }
            $data['allocationList']['data'][$k]['rolenames'] = $_rolesName;
        }
        //加载分页类
        $data['paging'] = $this->Paging($re_page, 15, $data['allocationList']['count']);
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function allocationRule() {

        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='getSystem'){
                if(empty($request['zone_id'])) $this->ajaxReturn(1, '请先选择区域');
                if(empty($request['role_id'])) $this->ajaxReturn(1, '请先选中职位');
                $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $where['role_id'] = $request['role_id'];
                $where[C('DB_PREFIX').'system_user.usertype'] = array('NEQ', 10);
                //员工列表
                $reflag = D('SystemUser','Controller')->getListCache($where,'','0,80');
                if ($reflag['data'] !== false) $this->ajaxReturn(0, '获取成功', $reflag['data']);
                else $this->ajaxReturn(1);
            }else{
                if (empty($request['zone_id'])) $this->ajaxReturn(1, '区域不能为空');
                if (empty($request['allocationname'])) $this->ajaxReturn(1, '名称不能为空', '', 'allocationname');
                if (empty($request['allocationnum'])) $this->ajaxReturn(1, '分配数量不能为空', '', 'allocationnum');
                if (empty($request['channel_id'])) $this->ajaxReturn(1, '请选择渠道');
                if (empty($request['allocation_roles'])) $this->ajaxReturn(1, '请添加分配职位', '', 'role_name');
                $request['system_user_id'] = $this->system_user_id;
                $request['zone_id'] = $request['zone_id'];
                $request['createtime'] = time();
                $reflag = D('User')->allocationAdd($request);
                if ($reflag !== false) $this->ajaxReturn(0, '分配规则添加成功', U('System/User/allocationList'));
                else $this->ajaxReturn(1, '添加失败');
            }
        }

        $data['zoneAll']['children'] = D("Zone")->getZoneList($this->system_user['zone_id']);

        //获取职位及部门
        $data['departmentAll'] = D('Department')->getAllDepartment();
        $data['roleAll'] = D('Role')->getAllRole();
        //渠道列表
        $data['channel'] = D('Channel')->getAllChannel();
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 修改分配规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editAllocationRule() {
        $id = I('get.id');
        if (empty($id)) $this->error('非法操作', 1);
        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='getSystem'){
                if(empty($request['zone_id'])) $this->ajaxReturn(1, '请先选择区域');
                if(empty($request['role_id'])) $this->ajaxReturn(1, '请先选中职位');
                $where['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $where['role_id'] = $request['role_id'];
                $where[C('DB_PREFIX').'system_user.usertype'] = array('NEQ', 10);
                //员工列表
                $reflag = D('SystemUser','Controller')->getListCache($where,'','0,80');
                if ($reflag['data'] !== false) $this->ajaxReturn(0, '获取成功', $reflag['data']);
                else $this->ajaxReturn(1);
            }else{
                if (empty($request['zone_id'])) $this->ajaxReturn(1, '区域不能为空');
                if (empty($request['allocationname'])) $this->ajaxReturn(1, '名称不能为空', '', 'allocationname');
                if (empty($request['allocationnum'])) $this->ajaxReturn(1, '分配数量不能为空', '', 'allocationnum');
                if (empty($request['channel_id'])) $this->ajaxReturn(1, '请选择渠道');
                if (empty($request['allocation_roles'])) $this->ajaxReturn(1, '请添加分配职位', '', 'role_name');
                $request['system_user_id'] = $this->system_user_id;
                $request['zone_id'] = $request['zone_id'];
                $reflag = D('User')->allocationEdit($request, $id);
                if ($reflag !== false) $this->ajaxReturn(0, '分配规则修改成功', U('System/User/allocationList'));
                else $this->ajaxReturn(1, '修改失败');
            }
        }
        //详情
        $data['allocationAll'] = D('User')->allocationDetail($id);  
        $systemUserId = M("allocation_systemuser")->where("user_allocation_id = {$id}")->select();
        $systemUserIds = '';
        foreach ($systemUserId as $key => $value) {
            $systemuserInfo = M("system_user")->where("system_user_id = {$value['system_user_id']}")->find();
            if (!empty($realnames)) {
                $realnames = $realnames.",$systemuserInfo[realname]";
                $systemUserIds = $systemUserIds.",$value[system_user_id]";
            }else{
                $realnames = $systemuserInfo['realname'];
                $systemUserIds = $value['system_user_id'];
            }
        }
        $data['allocationAll']['realname'] = $realnames;
        $data['allocationAll']['systemuser_ids'] = $systemUserIds;


        if($data['allocationAll']['roles']){
            foreach ($data['allocationAll']['roles'] as $k => $v) {
                if ($k == 0) $data['allocationAll']['rolesname'] = $v['name'];
                else $data['allocationAll']['rolesname'] .= ',' . $v['name'];
                $data['is_roles'][] = $v['id'];
            }
        }
        if($data['allocationAll']['systemuser']){
            foreach ($data['allocationAll']['systemuser'] as $k => $v) {
                if($k==0){
                    $data['allocationAll']['systemuser_names'] = $v['realname'];
                    $data['allocationAll']['systemuser_ids'] = $v['system_user_id'];
                }else{
                    $data['allocationAll']['systemuser_names'] = $data['allocationAll']['systemuser_names'].','.$v['realname'];
                    $data['allocationAll']['systemuser_ids'] = $data['allocationAll']['systemuser_ids'].','.$v['system_user_id'];
                }
            }
        }
        $data['zoneAll']['children'] = D("Zone")->getZoneList($this->system_user['zone_id']);
        //获取职位及部门
        $data['departmentAll'] = D('Department')->getAllDepartment();
        $data['roleAll'] = D('Role')->getAllRole();
        //渠道列表
        $data['channel'] = D('Channel')->getAllChannel();
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function abandonList() {
        if (IS_POST) {
            $request = I('post.');
            if ($request['type'] == 'del') {
                $where['status'] = 0;
                $reflag = D('User')->abandonEdit($where, $request['user_abandon_id']);
                if ($reflag !== false) $this->ajaxReturn(0, '回收规则删除成功');
                else $this->ajaxReturn(1, '删除失败');
            }elseif($request['type'] == 'start'){
                $where['start'] = $request['start'];
                $reflag = D('User')->abandonEdit($where, $request['user_abandon_id']);
                if ($reflag !== false) $this->ajaxReturn(0, '操作成功');
                else $this->ajaxReturn(1, '操作失败');
            }
        }
        $re_page = I('get.page', 1);
        $zoneIds = D("Zone")->getZoneIds($this->system_user['zone_id']);
        foreach ($zoneIds as $key => $value) {
            $zidString[] = $value['zone_id'];
        }
        $where[C('DB_PREFIX').'user_abandon.zone_id'] = array("IN", $zidString);
        $data['abandonList'] = D('User')->abandonList($where, (($re_page - 1) * 15) . ',15');
        if(!empty($data['abandonList']['data'])){
            foreach($data['abandonList']['data'] as $k=>$v){
                $data['abandonList']['data'][$k]['channelnames'] = D('Channel')->getChannelNames($v['channel_id']);
                if(!empty($v['abandon_roles'])){
                    $_roles = explode(',',$v['abandon_roles']);
                    $_rolesName = '';
                    foreach($_roles as $v2){
                        $getRole = D('Role')->getRoleInfo($v2);
                        if(empty($_rolesName)){
                            $_rolesName = $getRole['name'];
                        }else{
                            $_rolesName .= ','.$getRole['name'];
                        }
                    }
                }
                $data['abandonList']['data'][$k]['rolenames'] = $_rolesName;
            }
        }

        //加载分页类
        $data['paging'] = $this->Paging($re_page, 15, $data['abandonList']['count']);
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function abandonRule() {
        if (IS_POST) {
            $request = I('post.');
            if (empty($request['abandonname'])) $this->ajaxReturn(1, '名称不能为空', '', 'abandonname');
            if ($request['callbacknum'] == "") $this->ajaxReturn(1, '要求回访次数不能为空', '', 'callbacknum');
            if(!is_numeric($request['callbacknum'])) $this->ajaxReturn(1, '必须为数字', '', 'callbacknum');
            if (empty($request['unsatisfieddays'])) $this->ajaxReturn(1, '未达到要求保护天数不能为空', '', 'unsatisfieddays');
            if (empty($request['attaindays'])) $this->ajaxReturn(1, '达到要求保护天数不能为空', '', 'attaindays');
            if (empty($request['zone_id'])) $this->ajaxReturn(1, '区域不能为空');
            if (empty($request['channel_id'])) $this->ajaxReturn(1, '请选择渠道');
            if (empty($request['abandon_roles'])) $this->ajaxReturn(1, '请添加回收职位', '', 'role_name');
            $request['system_user_id'] = $this->system_user_id;
            $request['createtime'] = time();
            $reflag = D('User')->abandonAdd($request);
            if ($reflag !== false) $this->ajaxReturn(0, '回收规则添加成功', U('System/User/abandonList'));
            else $this->ajaxReturn(1, '添加失败');
        }

        $data['zoneAll']['children'] = D("Zone")->getZoneList($this->system_user['zone_id']);
        //获取职位及部门
        $data['departmentAll'] = D('Department')->getAllDepartment();
        $data['roleAll'] = D('Role')->getAllRole();
        //渠道列表
        $data['channel'] = D('Channel')->getAllChannel();
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 修改回收规则
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editAbandonRule() {
        $id = I('get.id');
        if (empty($id)) $this->error('非法操作', 1);
        if (IS_POST) {
            $request = I('post.');
            if (empty($request['abandonname'])) $this->ajaxReturn(1, '名称不能为空', '', 'abandonname');
            if ($request['callbacknum'] == "") $this->ajaxReturn(1, '要求回访次数不能为空', '', 'callbacknum');
            if (empty($request['unsatisfieddays'])) $this->ajaxReturn(1, '未达到要求保护天数不能为空', '', 'unsatisfieddays');
            if (empty($request['attaindays'])) $this->ajaxReturn(1, '达到要求保护天数不能为空', '', 'attaindays');
            if (empty($request['zone_id'])) $this->ajaxReturn(1, '区域不能为空');
            if (empty($request['channel_id'])) $this->ajaxReturn(1, '请选择渠道');
            if (empty($request['abandon_roles'])) $this->ajaxReturn(1, '请添加回收职位', '', 'role_name');
            $request['system_user_id'] = $this->system_user_id;
            $reflag = D('User')->abandonEdit($request, $id);
            if ($reflag !== false) $this->ajaxReturn(0, '回收规则修改成功', U('System/User/abandonList'));
            else $this->ajaxReturn(1, '修改失败');
        }
        //详情
        $data['abandonAll'] = D('User')->abandonDetail($id);
        foreach ($data['abandonAll']['roles'] as $k => $v) {
            if ($k == 0) $data['abandonAll']['rolesname'] = $v['name'];
            else $data['abandonAll']['rolesname'] .= ',' . $v['name'];
            $data['is_roles'][] = $v['id'];
        }

        $data['zoneAll']['children'] = D("Zone")->getZoneList($this->system_user['zone_id']);
        //获取职位及部门
        $data['departmentAll'] = D('Department')->getAllDepartment();
        $data['roleAll'] = D('Role')->getAllRole();
        //渠道列表
        $data['channel'] = D('Channel')->getAllChannel();
        $this->assign('data', $data);
        $this->display();
    }


    /*
    申请转入列表
    @author cq
    */
    public function applyList() {
        $request = I('get.');

        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($request['page']);

        $where = array();
        $where['B.system_user_id'] = $this->system_user_id;
        if (!empty($request['key_value'])) {
            $request['key_name'] = trim($request['key_name']);
            $request['key_value'] = trim($request['key_value']);

            if ($request['key_name'] == 'username') {
                    $where['zl_user.username'] = encryptPhone($request['key_value'], C('PHONE_CODE_KEY'));
            }else{
                $where['zl_user.'.$request['key_name']] = array('like', "%{$request['key_value']}%");
            }
        }
        $where['zl_user_apply.system_user_id'] = $this->system_user_id;
        if (!empty($request['status'])) $where['zl_user_apply.status'] = $request['status'];
        //今天申请, 三日内申请, 一周内申请筛选
        if (!empty($request['applytime'])) {
            $days = $request['applytime'];
            $curTimestamp = time() - ($days-1) * 24 * 60 * 60; //获取当天,3日内, 1周内的时间戳
            $curTimestamp = date('Y-m-d',$curTimestamp);  //从0点开始
            $curTimestamp = strtotime($curTimestamp);
            $where['zl_user_apply.applytime'] = array('EGT', $curTimestamp);
        }
        //自定义时间段筛选
        if (!empty($request['dateStart']) && !empty($request['dateEnd'])) {
            $request['dateStart2'] =  $request['dateStart']; //保留日期格式
            $request['dateEnd2'] =  $request['dateEnd'];

            $request['dateStart'] = strtotime($request['dateStart']);
            $request['dateEnd'] = strtotime($request['dateEnd']);

            if (!empty($request['status'])) $where['zl_user_apply.status'] = $request['status'];

            if ($request['dateEnd'] >= $request['dateStart']) {
                $request['dateEnd'] = $request['dateEnd'] + 24 * 60 * 60 - 1; //到当天23:59:59
                $where['zl_user_apply.applytime'] = array(
                    array('egt', $request['dateStart']),
                    array('lt', $request['dateEnd']),
                    'and'
                );
            } else {
                $request['dateStart'] = $request['dateStart'] + 24 * 60 * 60 - 1; //到当天23:59:59
                $where['zl_user_apply.applytime'] = array(
                    array('egt', $request['dateEnd']),
                    array('lt', $request['dateStart']),
                    'and'
                );
            }
        }

        $applyList = D("User")->getApplyList($where, (($re_page - 1) * 15) . ',15');

        if ($applyList['count'] > 0) {
            $statusArray = C('USER_STATUS');
            foreach ($applyList['data'] as $k => $v) {
                $applyList['data'][$k]['username'] = decryptPhone($v['username'], C('PHONE_CODE_KEY'));
                $infoquality = C("USER_INFOQUALITY");
                $key = $applyList['data'][$k]['infoquality'];
                $applyList['data'][$k]['infoquality'] = $infoquality[$key];

                switch ($applyList['data'][$k]['applystatus']) {
                    case 10:
                        $applyList['data'][$k]['status2'] = '待审核';
                        $applyList['data'][$k]['operate'] = '查看';
                        break;
                    case 20:
                        $applyList['data'][$k]['status2'] = '不通过';
                        if ($applyList['data'][$k]['userstatus'] != $statusArray['distribution']['num']) {  //160回库状态
                            $applyList['data'][$k]['operate'] = '查看';  //未回库
                        } else {
                            $applyList['data'][$k]['operate'] = '重新申请';
                        }
                        break;
                    case 30:
                        $applyList['data'][$k]['status2'] = '通过';
                        $applyList['data'][$k]['operate'] = '查看';
                        //保持原来所属人
                        /*$applyList['data'][$k]['affiliation_realname'] = $applyList['data'][$k]['apply_realname'];
                        $applyList['data'][$k]['channelname'] =  $applyList['data'][$k]['apply_channelname'];*/
                        break;
                }
            }

            //加载分页类
            $paging = $this->Paging($re_page, 15, $applyList['count'], $request);
            $this->assign('paging', $paging);
            $this->assign('applyList', $applyList['data']);
            $this->assign('url_dispostUser', U('System/User/dispostApply'));
        }
        $this->assign('request', $request);
        $this->display();
    }

    /**
     *申请/审核详情
     * @author cq
     */
    public function  applyDetails() {
        $PHONE_CODE_KEY = C('PHONE_CODE_KEY');
        $userData = I('get.');

        if (!empty($userData)) {
            $where['zl_user_apply.user_apply_id'] = $userData['id'];
            $applyRecord = D('User')->getApplyRecord($where);
        }
        $where['zl_user.user_id'] = $applyRecord['user_id'];
        $applyUserDetails = D('User')->getApplyUserDetails($where);

        if (!empty($applyUserDetails)) {
            $applyUserDetails[0]['username'] = decryptPhone($applyUserDetails[0]['username'], C('PHONE_CODE_KEY'));
            $applyUserDetails[0]['introducermobile'] = decryptPhone($applyUserDetails[0]['introducermobile'], C('PHONE_CODE_KEY'));

            $infoquality = C("USER_INFOQUALITY");
            $key = $applyUserDetails[0]['infoquality'];
            $applyUserDetails[0]['infoquality'] = $infoquality[$key];

            $statusArray = C('USER_STATUS');
            switch ($applyUserDetails[0]['applystatus']) {
                case 10:
                    $applyUserDetails[0]['status2'] = '待审核';
                    $applyUserDetails[0]['reapply'] = 0;
                    break;
                case 20:
                    $applyUserDetails[0]['status2'] = '不通过';
                    if ($applyUserDetails[0]['userstatus'] == $statusArray['160']['num']) { //160回库状态
                        $applyUserDetails[0]['reapply'] = 1;
                    } else {
                        $applyUserDetails[0]['reapply'] = 0;//没有申请的 机会了
                    };
                    break;
                case 30:
                    $applyUserDetails[0]['status2'] = '通过';
                    $applyUserDetails[0]['reapply'] = 0;
                    break;
            }

            $data['channel'] = D('Channel')->getAllChannel();
            if ($applyUserDetails[0]['apply_system_user_id'] == $this->system_user_id) {
                $data['canReApply'] = 1;
            } else {
                $data['canReApply'] = 0;
            }
            ///////////////////
            $where1['user_id'] = $applyRecord['user_id'];
            $userApply = D('UserApply')->where(array('user_id'=>$where1['user_id'],'status'=>10))->find();
            if(empty($userApply)){
                $data['auditFlag'] = 0;   //未审核
            }else{
                $data['auditFlag'] = 1;   //审核中
            }
            $this->assign('data', $data);
            $this->assign('applyUserDetails', $applyUserDetails);
        }
        $this->display();
    }


    /**
     *处理申请转入的操作
     * @author cq
     */
    public function  dispostApply() {
        $type = I('post.type');
        if ($type == 'dels') {
            $users = I('post.users');
            if (empty($users)) $this->ajaxReturn(1, '请先选中所需删除项');

            $users = explode(',', $users);
            foreach ($users as $k => $v) {
                $where = array('user_apply_id' => $v);
                $result = D('User')->DelUserApply($where);
            }
            if ($result) {
                $this->ajaxReturn(0, '删除客户成功', U('System/User/applyList'));
            } else {
                $this->ajaxReturn(1, '删除客户失败');
            }
        }
    }

    /*
    审核转入操作
    @author cq
    */
    public function auditTransfer() {
        if(IS_POST){
            $post = I('post.');
            $post['system_user_id'] = $this->system_user_id;
            $userMain = new UserMain();
            $reflag = $userMain->auditTransfer($post);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'],U('System/User/auditList'));
            else  $this->ajaxReturn(1, $reflag['msg']);
        }
        $apply['zl_user_apply.user_apply_id'] = I("get.id");
        $auditDetails = D("User")->getAuditUserDetails($apply);
        if (!empty($auditDetails)) {
            $PHONE_CODE_KEY = C('PHONE_CODE_KEY');
            $auditDetails[0]['username'] = decryptPhone($auditDetails[0]['username'],$PHONE_CODE_KEY);
            $auditDetails[0]['introducermobile'] = decryptPhone($auditDetails[0]['introducermobile'],$PHONE_CODE_KEY);
            $infoquality = C("USER_INFOQUALITY");
            $key = $auditDetails[0]['infoquality'];
            $auditDetails[0]['infoquality'] = $infoquality[$key];
        }
        if ($auditDetails[0]['to_system_user_id'] != 0) {
            $systemUser = D("SystemUser")->getSystemUserInfo($auditDetails[0]['to_system_user_id']);
            $auditDetails[0]['to_system_user'] = $systemUser['realname'];
        }else{
            $auditDetails[0]['to_system_user'] = $auditDetails[0]['apply_realname'];
        }
        $this->assign('auditDetails', $auditDetails);
        $this->display();

    }


    /*
   |--------------------------------------------------------------------------
   | 审核转入列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public  function  auditList() {
        $request = $where = I('get.');
        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($where['page']);
        //时间格式转化
        if (!empty($where['applytime'])) {
            $_time = explode('@', str_replace('/', '-', $where['applytime']));
            $where['applytime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59'))), 'AND');
        }
        $where['zone_id'] = $this->system_user['zone_id'];
        $where['admin_system_user_id'] = $this->system_user_id;
        $userMain = new UserMain();
        $data['auditList'] = $userMain->getApplyList($where,null, (($re_page - 1) * 15) . ',15');
        //获取区域下员工
        $data['systemList'] = D('User')->getUserSystem(array(C('DB_PREFIX') . 'user.zone_id' => $this->system_user['zone_id']));
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('USER_INFOQUALITY');
        //加载分页类
        $data['paging'] = $this->Paging($re_page, 15, $data['auditList']['count'], $request);
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }

    /* 导入客户模板列表页
    * @author
    *
    */
    public function importTemplateList()
    {
        $proidMain = new ProidController();
        $channelMain = new ChannelController();
        $setPages['system_user_id'] = $this->system_user_id;
        $setPages['type'] = 2; //推广计划导入模板类型
        $result = $proidMain->getSetPages($setPages);
        $pages = $result['msg'];
        foreach ($pages as $key => $page) {
            $result = $channelMain->getChannel($page['channel_id']);
            $channel = $result['data'];
            $page['channelname'] = $channel['channelname'];
            $page['createtime'] = date('Y-m-d H:d:s', $page['createtime']);
            $pagesList[$key] = $page;
        }
        $this->assign('urlDelSetPages', U("System/User/delSetPages"));
        $this->assign('pagesList', $pagesList);
        $this->display();

    }


    /**
     * 添加导入、导出模板 -- 客户
     * @author
     *
     */
    public function addTemplate()
    {
        $channelMain = new ChannelController();
        $res = $channelMain->getAllChannel();
        $channelList = $res['data'];
        $type = I("get.type");
        if (IS_POST) {
            $setPages = I("post.");
            if (!$setPages['pagesname']) {
                $this->ajaxReturn(1, '请填写模板名称');
            }
            $setPages['system_user_id'] = $this->system_user_id;
            $setPages['type'] = $type;
            if ($setPages['type'] == 2) {
                if (!$setPages['channel_id']) {
                    $this->ajaxReturn(2, '请选择渠道');
                }
            }
            if (!$setPages['sign']) {
                $this->ajaxReturn(3, '请至少选择1个表头');
            }
            $setPages['sign'] = explode(',', $setPages['sign']);

            foreach ($setPages['sign'] as $key => $sign) {
                $setPages['sign'][$key] = explode('-', $sign);
                $array[] = $setPages['sign'][$key][1];
            }
            if (!in_array('username', $array) && !in_array('qq', $array) && !in_array('tel', $array)) {
                $this->ajaxReturn(4, '手机-QQ-固话至少有一个');
            }
            $result = D("User")->createSetPages($setPages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }

            if ($setPages['type'] == 2) {
                $this->success('设置模板成功', 0, U('System/User/importTemplateList', array('type' => $setPages['type'])));
            } elseif ($setPages['type'] == 3) {
                $this->success('设置模板成功', 0, U('System/User/outputTemplateList', array('type' => $setPages['type'])));
            }
        }
        $this->assign("type", $type);
        $this->assign("channelList", $channelList);
        $this->display();

    }

    /**
     * 修改模板
     * @author
     *
     */
    public function editTemplate()
    {
        $proidMain = new ProidController();
        $channelMain = new ChannelController();
        $result = $channelMain->getAllChannel();
        $channelList = $result['data'];
        $data['setpages_id'] = I("get.setpages_id");
        $type = I("get.type");
        if (IS_POST) {
            $setpages = I("post.");
            if (!$setpages['pagesname']) {
                $this->ajaxReturn(1, '请填写模板名称');
            }
            $setpages['system_user_id'] = $this->system_user_id;
            if ($type == 2) {
                if (!$setpages['channel_id']) {
                    $this->ajaxReturn(2, '请选择渠道');
                }
            }
            if (!$setpages['sign']) {
                $this->ajaxReturn(3, '请至少选择1个表头');
            }
            foreach ($setpages['sign'] as $key => $pages) {
                
            }
            $setpages['sign'] = explode(',', $setpages['sign']);
            foreach ($setpages['sign'] as $key => $sign) {
                $setpages['sign'][$key] = explode('-', $sign);
                $array[] = $setpages['sign'][$key][1];
                $arr[] = $sign[0];
            }
            if (count($arr)>count(array_unique($arr))) {
                $this->ajaxReturn(3, '请不要重复选择表头');
            }
            if (!in_array('username', $array) && !in_array('qq', $array) && !in_array('tel', $array)) {
                $this->ajaxReturn(4, '手机-QQ-固话至少有一个');
            }
            $setpages['setpages_id'] = $data['setpages_id'];
            $setpages['type'] = $type;
            $result = $proidMain->editSetPages($setpages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            if ($type == 2) {
                $this->success('修改模板成功', 0, U('System/User/importTemplateList', array('type' => $type)));
            } elseif ($type == 3) {
                $this->success('修改模板成功', 0, U('System/User/outputTemplateList', array('type' => $type)));
            }

        }
        $pagesInfos=$result[0];
        $head_info=D('Setpageinfo')->where(array('setpages_id'=> $data['setpages_id']))->select();  
        $head_name_arr=array();
        foreach ($head_info as $key => $value) {
                $head_name_arr[]=$value['headname'];
        }
        $pagesInfos['head_info']=$head_info;
        $this->assign('page_heads',C('page_heads'));
        $this->assign('page_headss',C('page_headss'));
        $this->assign('head_name_arr',$head_name_arr);     
        $this->assign('pagesInfos', $pagesInfos);

        $setpages['system_user_id'] = $this->system_user_id;
        $result = $proidMain->getSetPages($data);
        $pagesInfo = $result['msg'];
        $result = $channelMain->getChannel($pagesInfo[0]['channel_id']);
        $channelInfo = $result['data'];
        $this->assign("channelInfo", $channelInfo);
        $this->assign('channelList', $channelList);
        $this->assign('pagesInfo', $pagesInfo);
        $this->assign('type', $type);
        $this->display();

    }


    /**
     * 删除模板
     * @author
     *
     */
    public function delSetPages()
    {
        $proidMain = new ProidController();
        $setpages['setpages_id'] = I("post.setpages_id");
        $setpages['type'] = I("get.type");
        $backInfo = $proidMain->delSetPages($setpages);
        if ($backInfo['code'] == 0) {
            if ($setpages['type'] == 2) {
                $this->success('删除模板成功', 0, U('System/User/importTemplateList', array('type' => $setpages['type'])));
            } else {
                $this->success('删除模板成功', 0, U('System/User/outputTemplateList', array('type' => $setpages['type'])));
            }
        }
        $this->ajaxReturn(1, '删除失败');

    }


    /**
     * 导入客户
     * @author Nixx
     */
    public function importUser()
    {
        set_time_limit(0);
        $proidMain = new ProidController();
        $system_user_id = $this->system_user_id;
        $zone_id = $this->system_user['zone_id'];
        $type = I("get.type");     
        if (IS_POST) {
            session('faile_import', null);
            session('success_import', null);
            $setPages['setpages_id'] = I("post.setpages_id"); //模板ID
            $res = $proidMain->getSetPages($setPages);
            $setpagesInfo = $res['msg'];
            if (!empty($_FILES['file'])) {
                $exts = array('xls', 'xlsx');
                $rootPath = './Uploads/File';
                $savePath = '/ImportUser/';
                $uploadFile = $this->uploadFile($exts, $rootPath, $savePath);
                $filename = $rootPath . $uploadFile['file']['savepath'] . $uploadFile['file']['savename'];
            }         
            $datas = importExecl($filename);
            foreach ($datas as $key => $data) {
                array_unique($data);
            }
            $res = $proidMain->getSetPagesInfo($setPages['setpages_id']);
            $letters = $res['msg'];
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
                    } elseif ($num < 11) {
                        unset($user['username']);
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
                if ($user['lastsalary']) {  //目前薪资
                    $user['lastsalary'] = str_replace('万', '0000', $user['lastsalary']);
                    $user['lastsalary'] = str_replace('k', '000', $user['lastsalary']);
                    $user['lastsalary'] = str_replace('-', '_', $user['lastsalary']);
                    $user['lastsalary'] = preg_replace('[\W]', '', $user['lastsalary']);

                    $user['lastsalary'] = explode('_', $user['lastsalary']);
                    $user['lastsalary'] = max($user['lastsalary']);
                } else {
                    $user['lastsalary'] = 0;//  0-表示面议    年薪未处理
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
                $result = D("User")->addUser($user, $zone_id, $system_user_id);
                if ($result['code'] != 0) {
                    $userList[$key]['msg'] = $result['msg'];
                    $errorData[$key] = $userList[$key];
                    unset($userList[$key]);
                    continue;
                }

            } 
            session('faile_import', $errorData);
            session('success_import', $userList);
            $this->redirect('/System/User/importClient', array('type' => $type));
        } else {
            $this->assign("system_user_id", $system_user_id);
            $set['system_user_id'] = $system_user_id;
            $set['type'] = $type;
            $res = $proidMain->getSetPages($set);
            $setPages = $res['msg'];
            $this->assign('setPages', $setPages);
            $this->display();
        }

    }

    /**
     * 导入客户
     * @author Nixx
     */
    public function importUserLibrary()
    {
        set_time_limit(0);
        $system_user_id = $this->system_user_id;
        $zone_id = $this->system_user['zone_id'];
        $proidMain = new ProidController();
        $type = I("get.type");
        if (IS_POST) {
            session('faile_import', null);
            session('success_import', null);
            $setPages['setpages_id'] = I("post.setpages_id"); //模板ID
            $res = $proidMain->getSetPages($setPages);
            $setpagesInfo = $res['msg'];
            if (!empty($_FILES['file'])) {
                $exts = array('xls', 'xlsx');
                $rootPath = './Uploads/File';
                $savePath = '/ImportLibraryUser/';
                $uploadFile = $this->uploadFile($exts, $rootPath, $savePath);
                $filename = $rootPath . $uploadFile['file']['savepath'] . $uploadFile['file']['savename'];
            }         
            $datas = importExecl($filename);
            foreach ($datas as $key => $data) {
                array_unique($data);
            }
            $result = $proidMain->getSetPagesInfo($setPages['setpages_id']);
            $letters = $result['msg'];
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
                    } elseif ($num < 11) {
                        unset($user['username']);
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
    
                if ($user['lastsalary']) {  //目前薪资
                    $user['lastsalary'] = str_replace('万', '0000', $user['lastsalary']);
                    $user['lastsalary'] = str_replace('k', '000', $user['lastsalary']);
                    $user['lastsalary'] = str_replace('-', '_', $user['lastsalary']);
                    $user['lastsalary'] = preg_replace('[\W]', '', $user['lastsalary']);
    
                    $user['lastsalary'] = explode('_', $user['lastsalary']);
                    $user['lastsalary'] = max($user['lastsalary']);
                } else {
                    $user['lastsalary'] = 0;//  0-表示面议    年薪未处理
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
                $USER_STATUS = C('USER_STATUS');
                $user['status'] = $USER_STATUS['160']['num'];
                $result = D("User")->addUser($user, $zone_id, $system_user_id);
                if ($result['code'] != 0) {
                    $userList[$key]['msg'] = $result['msg'];
                    $errorData[$key] = $userList[$key];
                    unset($userList[$key]);
                    continue;
                }
            }
            session('faile_import', $errorData);
            session('success_import', $userList);
            $this->redirect('/System/User/importClient', array('type' => $type));
    
        } else {
            $this->assign("system_user_id", $system_user_id);
            $set['system_user_id'] = $system_user_id;
            $set['type'] = $type;
            $res = $proidMain->getSetPages($set);
            $setPages = $res['msg'];
            $this->assign('setPages', $setPages);
            $this->display();
        }
    
    }

    /*
     * 导入 结果页
     * @author Nxx
     */
    public function importClient() {
        $type = I("get.type");
        $failes = session('faile_import');
        $successs = session('success_import');
        foreach ($failes as $k1 => $f) {
            $k = $k1 + 2;
            $faile[$k] = $f;
        }
        foreach ($successs as $k2 => $s) {
            $k = $k2 + 2;
            $success[$k] = $s;
        }
        $failecount = count($faile);
        $successcount = count($success);
        $this->assign('failecount', $failecount);
        $this->assign('successcount', $successcount);
        $this->assign('type', $type);
        $this->assign('faile', $faile);
        $this->assign('success', $success);
        $this->display();
    }


    /**
     * 导出客户模板列表页
     * @author
     *
     */
    public function outputTemplateList() 
    {
        $proidMain = new ProidController();
        $channelMain = new ChannelController();
        $setPages['system_user_id'] = $this->system_user_id;
        $setPages['type'] = 3; //推广计划导入模板类型
        $result = $proidMain->getSetPages($setPages);
        $pages = $result['msg'];
        foreach ($pages as $key => $page) {
            $res = $channelMain->getChannel($page['channel_id']);
            $channel = $res['data'];
            $page['channelname'] = $channel['channelname'];
            $page['createtime'] = date('Y-m-d H:d:s', $page['createtime']);
            $pagesList[$key] = $page;
        }

        $this->assign('urlDelSetPages', U("System/User/delSetPages"));
        $this->assign('pagesList', $pagesList);
        $this->display();

    }


    /**
     * 导出客户
     * @author Nixx
     */
    public function outputUser() {	
        set_time_limit(3600);    
		$channelMain = new ChannelController();
        $proidMain = new ProidController();
        $courseMain = new CourseController();
        $setpages['type'] = 3;
        $setpages['system_user_id'] = $this->system_user_id;
		$user_mod=D("User");
        $res = $proidMain->getSetPages($setpages);
        $setpagesList = $res['msg'];
        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='succ'){
                if(session("outputUser_path")){
                    $code = session("outputUser_path");
                    $this->ajaxReturn($code['code'],$code['msg'],$code['data']);
                }
                $this->ajaxReturn(1,'');
            }else{
                session('outputUser_path',null);
                if (!$request['setpages_id']) {
                    $this->error("请选择模板");
                }
                $res = $proidMain->getSetPagesInfos($request['setpages_id']);
                $letters = $res['msg'];
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
                $zoneIds = D('Zone')->getZoneIds($zone_id);
                
                foreach ($zoneIds as $key => $value) {
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
                $userList = $user_mod->getOutPutUser($where, $order, '0,30000',$heads);
                if ($userList['count'] == 0) {
                    session('outputUser_path',array('code'=>4, 'msg'=>'此条件下没有数据可导出！'));
                    exit;
                }
                foreach ($userList['data'] as $key => $user) {
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
        }
        //跟进结果
        $data['attitude'] = C('USER_ATTITUDE');
        $data['channel'] = D('Channel')->getAllChannel();
        //学习平台：
        $data['learningtype'] = C('USER_LEARNINGTYPE');
        //学习方式
        $data['studytype'] = C('USER_STUDYTYPE');
        //课程列表
        $res = $courseMain->getAllCourse();
        $data['courseAll'] = $res['data'];
        $this->assign('data', $data);   
        $this->assign("setpagesList", $setpagesList);
        $this->display();

    }

}

