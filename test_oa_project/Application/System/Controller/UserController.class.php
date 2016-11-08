<?php
namespace System\Controller;

use Common\Controller\SystemController;

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
        if(!empty($request['pagenum']) && session('user_list_pagenum')!=$request['pagenum']){
            session('user_list_pagenum',$request['pagenum']);
        }
        $re_pagenum = session('user_list_pagenum') ? session('user_list_pagenum') : 30;
        unset($request['page']);unset($where['pagenum']);unset($where['page']);unset($where['type']);unset($where['_pjax']);
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
        $re_userAll = D('User','Service')->getUserList($where, $order, (($re_page-1)*$re_pagenum).','.$re_pagenum);
        $data['userAll'] = $re_userAll['data']['data'];
        //加载分页类
        $data['paging_data'] = $this->Paging($re_page, $re_pagenum, $re_userAll['data']['count'], $request, __ACTION__, null, 'system_userlist');
        //获取自定义列
        $column_where['columntype'] = 1;
        $column_list = D('SystemUser','Service')->getColumnList($column_where);
        $data['column'] = $column_list['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //课程列表
        $courseList = D('Course','Service')->getCourseList();
        $data['courseAll'] = $courseList['data']['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('FIELD_STATUS.USER_INFOQUALITY');
        //用户状态转换
        $data['USER_STATUS'] = C('FIELD_STATUS.USER_STATUS');
        //跟进结果转换
        $data['USER_ATTITUDE'] = C('FIELD_STATUS.USER_ATTITUDE');
        //学习平台
        $data['LEARNINGTYPE'] = C('FIELD_STATUS.USER_LEARNINGTYPE');
        //学习方式
        $data['studytype'] = C('FIELD_STATUS.USER_STUDYTYPE');

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
            //添加客户
            $reflag = D('User','Service')->addUser($request);
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
        $courseList = D('Course','Service')->getCourseList();
        $data['course'] = $courseList['data']['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
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
                $reflag = D('User','Service')->editUser($request);
                if($reflag['code']==0){
                    if(!empty($request['remark'])){
                        $_request['user_id'] = $user_id;
                        $_request['remark'] = $request['remark'];
                        $reflag_info = D('User','Service')->editUserInfo($_request);
                    }
                    //返回数据操作状态
                    if ($reflag_info['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                    else  $this->ajaxReturn(1, $reflag_info['msg'], '', !empty($reflag_info['sign']) ? $reflag_info['sign'] : '');
                }else{
                    $this->ajaxReturn(1, $reflag['msg']?$reflag['msg']:'数据操作失败');
                }
                 //修改用户详情
            } else if ($request['type'] == 'editinfo') {
                $request['user_id'] = $user_id;
                $reflag = D('User','Service')->editUserInfo($request);
                //返回数据操作状态
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            } else if ($request['type'] == 'addcallback') {
                $request['nexttime'] = strtotime(($request['nextvisit']). ' ' .($request['nextvisit_hi']));
                $request['user_id'] = $user_id;
                $reflag = D('User','Service')->addUserCallback($request);
                //返回数据操作状态
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else  $this->ajaxReturn(1, $reflag['msg'], '', !empty($reflag['sign']) ? $reflag['sign'] : '');
            } else if ($request['type'] == 'getFeeLogs') {
                //缴费方式
                $getWhere['user_id'] = $user_id;
                $userOrder =  D('Order','Service')->getUserOrder($getWhere);
                //返回数据操作状态
                if ($userOrder['code'] == 0) $this->ajaxReturn(0, '', $userOrder['data']);
                else  $this->ajaxReturn($userOrder['code'], $userOrder['msg']);
            } else if ($request['type'] == 'getSmsLogs') {
                //短信记录
                $getWhere['user_id'] = $user_id;
                $userLog = D('User','Service')->getUserSmsLog($getWhere,$callbackType);
                //返回数据操作状态
                if ($userLog['code'] == 0) $this->ajaxReturn(0, '', $userLog['data']);
                else  $this->ajaxReturn($userLog['code'], $userLog['msg']);
            }
        }
        //客户详情
        $userInfo = D('User','Service')->getUserInfo(array('user_id'=>$user_id));
        $data['userInfo'] = $userInfo['data'];
        if ($data['userInfo']['status'] != 160 && $data['userInfo']['system_user_id'] == $this->system_user_id) $data['isSelf'] = 1;
        //回访记录
        $callbackList = D('User','Service')->getUserCallback(array('user_id'=>$user_id,'rank'=>$callbackType));
        $data['callbackList'] = $callbackList['data'];
        //通话记录
        $call_List = D('User','Service')->getCallList(array('user_id'=>$user_id,'system_user_id'=>$this->system_user_id,'rank'=>$callbackType));
        $data['call_List'] = $call_List['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取学历表
        $data['educationAll'] = C('FIELD_STATUS.EDUCATION_ARRAY');
        //课程列表
        $courseList = D('Course','Service')->getCourseList();
        $data['course'] = $courseList['data']['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
        //学习平台
        $data['LEARNINGTYPE'] = C('FIELD_STATUS.USER_LEARNINGTYPE');
        //跟进结果
        $data['ATTITUDE'] = C('FIELD_STATUS.USER_ATTITUDE');
        //回访方式
        $data['CALLBACK'] = C('FIELD_STATUS.USER_CALLBACK');
        //邀约状态转换
        $data['USER_STATUS'] = C('FIELD_STATUS.USER_STATUS');
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('FIELD_STATUS.USER_INFOQUALITY');
        $data['user_id'] = $user_id;
        $data['type'] = $type;
        //判断是否从审核列表进来的  cq
        if(!empty($isAuditList)){
           $data['isAuditList'] = $isAuditList;
        }
        $this->assign('data', $data);
        $this->display();

    }

    /*
    * 申请转入（客户详情） 异步处理方法
    * @author zgt
    */
    public function applyUser()
    {
        $request = I('post.');
        if ($request['type'] == 'getSystemUser') {
            $requestP = I('post.');
            $page = I('post.page',1);
            //异步获取员工列表
            $where_system['usertype'] = array('NEQ',10);
            $where_system['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
            $where_system['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
            if(!empty($request['search'])) $where_system['realname'] = array('like',$request['search']);
            $where_system['order'] = 'sign asc';
            $where_system['page'] = $page.',10';
            //员工列表
            $reSystemList = D('SystemUser','Service')->getSystemUsersList($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'getInfoquality') {
            $where_system['systemUserId'] = I('post.systemUserId');
            //获取员工渠道出库量统计
            $reSystemList = D('SystemUser','Service')->getInfoqualityCount($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'submit') {
            $reflag = D('User','Service')->applyUser($request);
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
           $reUser = D('User')->getFind($where);
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
            $result = D('SystemUser', 'Service')->getSmsTemplate();
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='createTemplate'){
            $result = D('SystemUser', 'Service')->createSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='editTemplate'){
            $result = D('SystemUser', 'Service')->editSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='delTemplate'){
            $result = D('SystemUser', 'Service')->delSmsTemplate($request);
            //返回数据操作状态
            if ($result['code'] == 0) $this->ajaxReturn(0, $result['msg'], $result['data']);
            else  $this->ajaxReturn(1, $result['msg'], '', !empty($result['sign']) ? $result['sign'] : '');
        }elseif($request['type']=='send'){
            $result = D('SystemUser', 'Service')->sendSmsUser($request);
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
        $reflag = D('User','Service')->redeemUser($request);
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
        if($param['type']=='getcall'){
            $reflag = D('User', 'Service')->getCall();
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
            else  $this->ajaxReturn(1, $reflag['msg'], $reflag['data']);
        }elseif($param['type']=='calltel'){
            //只拨固定电话
            $param['system_user_id'] = $this->system_user_id;
            $reflag = D('User', 'Service')->callUser($param,2);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], $reflag['data']);
            else  $this->ajaxReturn(1, $reflag['msg'], $reflag['data']);
        }elseif($param['type']=='callphone'){
            //拨打电话
            $param['system_user_id'] = $this->system_user_id;
            $reflag = D('User', 'Service')->callUser($param);
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
        //是否提示
        if($request['type']=='ishint'){
            $isAuditOrder = D('Order','Service')->isUserOrder($request);
            if($isAuditOrder['code']!=0) $this->ajaxReturn(20, '该客户有未完成订单，请查询详情后再操作！');
            $this->ajaxReturn(0, '创建订单时需注意检查客户名称是否有误！');
        }
        //获取接口
        $refalg = D('Order','Service')->createOrder($request);
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
        $reflag = D('User', 'Service')->abandonUser($data);
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
        $reflag = D('User','Service')->MarkUser($request);
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
        $reflag = D('SystemUser','Service')->editColumn($request);
//        返回数据操作状态
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
        $reflag = D('User', 'Service')->affirmVisit($request);
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
            $requestP = I('post.');
            $page = I('post.page',1);
            //异步获取员工列表
            $where_system['usertype'] = array('NEQ',10);
            $where_system['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
            $where_system['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
            if(!empty($request['search'])) $where_system['realname'] = array('like',$request['search']);
            $where_system['order'] = 'sign asc';
            $where_system['page'] = $page.',10';
            //员工列表
            $reSystemList = D('SystemUser','Service')->getSystemUsersList($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'getInfoquality') {
            $where_system['systemUserId'] = I('post.systemUserId');
            //获取员工渠道出库量统计
            $reSystemList = D('SystemUser','Service')->getInfoqualityCount($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $reflag = D('User','Service')->allocationUser($request);
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
            $requestP = I('post.');
            $page = I('post.page',1);
            //异步获取员工列表
            $where_system['usertype'] = array('NEQ',10);
            $where_system['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
            $where_system['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
            if(!empty($request['search'])) $where_system['realname'] = array('like',$request['search']);
            $where_system['order'] = 'sign asc';
            $where_system['page'] = $page.',10';
            //员工列表
            $reSystemList = D('SystemUser','Service')->getSystemUsersList($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'getInfoquality') {
            $where_system['systemUserId'] = I('post.systemUserId');
            //获取员工渠道出库量统计
            $reSystemList = D('SystemUser','Service')->getInfoqualityCount($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $reflag = D('User','Service')->restartUser($request);
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
            }
            if($requestP['type']=='getSysUser'){
                //异步获取员工列表
                $whereSystem['usertype'] = array('NEQ',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                $whereSystem['order'] = 'sign asc';
                //员工列表
                $reSystemList = D('SystemUser','Service')->getSystemUsersList($whereSystem);
                //返回数据操作状态
                if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']['data']);
                else $this->ajaxReturn(1, '获取失败');
            }
        }
        $limit = (empty($requestG['page'])?'0':($requestG['page']-1)*30).',30';
        //获取数据
        $orderList = D('Order', 'Service')->getOrderList($where, 'createtime DESC', $limit);
        //获取区域下
        $resultZone = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll']['children'] = $resultZone['data'];
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取配置状态值
        $data['order_status'] = C('ORDER_STATUS');
        $data['order_loan_institutions'] = C('USER_LOAN_INSTITUTIONS');
        $data['order_receivetype'] = C('USER_RECEIVETYPE');
        //模版赋值
        $data['order_list'] = $orderList['data'];
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
        if(!empty($request['pagenum']) && session('user_librarylist_pagenum')!=$request['pagenum']){
            session('user_librarylist_pagenum',$request['pagenum']);
        }
        $re_pagenum = session('user_librarylist_pagenum') ? session('user_librarylist_pagenum') : 30;
        unset($request['page']);unset($where['pagenum']);unset($where['page']);unset($where['type']);unset($where['_pjax']);
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
                $whereSystem['usertype'] = array('NEQ',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                $whereSystem['order'] = 'sign asc';
                //员工列表
                $reSystemList = D('SystemUser','Service')->getSystemUsersList($whereSystem);
                //返回数据操作状态
                if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']['data']);
                else $this->ajaxReturn(1, '获取失败');
            }
        }
        //客户列表
        $re_userAll = D('User','Service')->getUserList($where, $order, (($re_page-1)*$re_pagenum).','.$re_pagenum);
        $data['userAll'] = $re_userAll['data']['data'];
        //加载分页类
        $data['paging_data'] = $this->Paging($re_page, $re_pagenum, $re_userAll['data']['count'], $request, __ACTION__, null, 'system_userlist');
        //获取自定义列
        $column_where['columntype'] = 2;
        $column_list = D('SystemUser','Service')->getColumnList($column_where);
        $data['column'] = $column_list['data'];
        //获取区域ID 获取下拉框
        $zoneAll = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll']['children'] = $zoneAll['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //课程列表
        $courseList = D('Course','Service')->getCourseList();
        $data['courseAll'] = $courseList['data']['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
        //信息质量转换
        $data['USER_INFOQUALITY'] = C('FIELD_STATUS.USER_INFOQUALITY');
        //用户状态转换
        $data['USER_STATUS'] = C('FIELD_STATUS.USER_STATUS');
        //跟进结果转换
        $data['USER_ATTITUDE'] = C('FIELD_STATUS.USER_ATTITUDE');
        //学习平台
        $data['LEARNINGTYPE'] = C('FIELD_STATUS.USER_LEARNINGTYPE');
        //学习方式
        $data['studytype'] = C('FIELD_STATUS.USER_STUDYTYPE');

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
            $requestP = I('post.');
            $page = I('post.page',1);
            //异步获取员工列表
            $where_system['usertype'] = array('NEQ',10);
            $where_system['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
            $where_system['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
            if(!empty($request['search'])) $where_system['realname'] = array('like',$request['search']);
            $where_system['order'] = 'sign asc';
            $where_system['page'] = $page.',10';
            //员工列表
            $reSystemList = D('SystemUser','Service')->getSystemUsersList($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'getInfoquality') {
            $where_system['systemUserId'] = I('post.systemUserId');
            //获取员工渠道出库量统计
            $reSystemList = D('SystemUser','Service')->getInfoqualityCount($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $reflag = D('User','Service')->restartUser($request, 2);
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
        $data['user_id'] = $request['user_id'];
        $data['attitude_id'] =0;
        $data['remark'] = $request['abandon_remark'];
        $reflag = D('User', 'Service')->abandonUser($data, 2);
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
            $requestP = I('post.');
            $page = I('post.page',1);
            //异步获取员工列表
            $where_system['usertype'] = array('NEQ',10);
            $where_system['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
            $where_system['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
            if(!empty($request['search'])) $where_system['realname'] = array('like',$request['search']);
            $where_system['order'] = 'sign asc';
            $where_system['page'] = $page.',10';
            //员工列表
            $reSystemList = D('SystemUser','Service')->getSystemUsersList($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList['data']);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'getInfoquality') {
            $where_system['systemUserId'] = I('post.systemUserId');
            //获取员工渠道出库量统计
            $reSystemList = D('SystemUser','Service')->getInfoqualityCount($where_system);
            //返回数据操作状态
            if ($reSystemList['code'] == 0) $this->ajaxReturn(0, '', $reSystemList);
            else $this->ajaxReturn(1, '获取失败');
        }else if ($request['type'] == 'submit') {
            $request['tosystem_user_id'] = $request['system_user_id'];
            $reflag = D('User','Service')->allocationUser($request, 2);
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
                $where['user_allocation_id'] = $request['user_allocation_id'];
                $reflag = D('User','Service')->allocationDel($where);
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }elseif($request['type'] == 'start'){
                $where['user_allocation_id'] = $request['user_allocation_id'];
                $where['start'] = $request['start'];
                $reflag = D('User','Service')->allocationStart($where);
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }
        }
        $re_page = I('get.page', 1);
        $where['page'] =  (($re_page - 1) * 15) . ',15';
        $data['allocationList'] = D('User', 'Service')->allocationList($where);

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
                $where['role_ids'] = $request['role_id'];
                $where['realname'] = !empty($request['keyname'])?array('LIKE', $request['keyname']):null;
                //员工列表
                $reflag = D('SystemUser','Service')->getSystemUsersList($where);
                if ($reflag['code']==0) $this->ajaxReturn(0, '获取成功', $reflag['data']['data']);
                else $this->ajaxReturn(1);
            }else{
                $reflag = D('User', 'Service')->addAllocation($request);
                if ($reflag['code'] == 0) $this->ajaxReturn(0, '分配规则添加成功', U('System/User/allocationList'));
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }
        }
        //区域
        $zoneList = D("Zone", 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneList['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
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
                $param['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $param['role_ids'] = array('IN', $request['role_id']);
                $param['usertype'] = array('NEQ', 10);
                $param['realname'] = !empty($request['keyname'])?array('LIKE', $request['keyname']):null;
                //员工列表
                $reflag = D('SystemUser','Service')->getSystemUsersList($param);
                if ($reflag['code']== 0) $this->ajaxReturn(0, '获取成功', $reflag['data']['data']);
                else $this->ajaxReturn(1);
            }else{
                $request['user_allocation_id'] = $id;
                $reflag = D('User','Service')->editAllocation($request);
                if ($reflag['code']==0) $this->ajaxReturn(0, '分配规则修改成功', U('System/User/allocationList'));
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }
        }
        //详情
        $allocation_list = D('User','Service')->allocationDetail(array('user_allocation_id'=>$id));
        $data['allocationAll'] = $allocation_list['data'];
        $allocationnums = explode(',', $data['allocationAll']['allocationnum']);
        //区域
        $zoneList = D("Zone", 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneList['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
        $this->assign('data', $data);
        $this->assign('allocationnums', $allocationnums);
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
                $where['user_abandon_id'] = $request['user_abandon_id'];
                $reflag = D('User','Service')->abandonDel($where);
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }elseif($request['type'] == 'start'){
                $where['user_abandon_id'] = $request['user_abandon_id'];
                $where['start'] = $request['start'];
                $reflag = D('User','Service')->abandonStart($where, $request['user_abandon_id']);
                if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg']);
                else $this->ajaxReturn($reflag['code'], $reflag['msg']);
            }
        }
        $re_page = I('get.page', 1);
        $where['page'] = (($re_page - 1) * 15) . ',15';
        $abandonList = D('User', 'Service')->abandonList($where);
        $data['abandonList'] = $abandonList['data'];
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
            $reflag = D('User', 'Service')->addAbandon($request);
            if ($reflag['code']==0) $this->ajaxReturn(0, '回收规则添加成功', U('System/User/abandonList'));
            else $this->ajaxReturn($reflag['code'], $reflag['msg']);
        }
        //区域
        $zoneList = D("Zone", 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneList['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
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
            $request['user_abandon_id'] = $id;
            $reflag = D('User', 'Service')->editAbandon($request);
            if ($reflag['code']==0) $this->ajaxReturn(0, '回收规则修改成功', U('System/User/abandonList'));
            else $this->ajaxReturn(1, $reflag['msg']);
        }
        //详情
        $data['abandonAll'] = D('User', 'Service')->abandonDetail(array('user_abandon_id'=>$id));
        //区域
        $zoneList = D("Zone", 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneList['data'];
        //获取部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取职位
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //渠道列表
        $channeList = D('Channel','Service')->getChannelList();
        $data['channel'] = $channeList['data'];
        $this->assign('data', $data);
        $this->display();
    }


    /*
    |--------------------------------------------------------------------------
    | 申请转入列表
    |--------------------------------------------------------------------------
    | @author cq
    */
    public function applyList() {
        $request = $_where = I('get.');
        $_where['system_user_id'] = $this->system_user_id;
        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($_where['page']);
        //时间格式转化
        if (!empty($request['applytime'])) {
            $_time = explode('@', str_replace('/', '-', $request['applytime']));
            $_where['applytime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59'))), 'AND');
        }
        $_where['page'] = (($re_page - 1) * 30) . ',30';
        $applyList = D('User', 'Service')->getApplyUserList($_where);
        $data['applyList'] = $applyList['data']['data'];
        //加载分页类
        $data['paging_div'] = $this->Paging($re_page, 30, $applyList['data']['count'], $request);
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 申请/审核详情
    |--------------------------------------------------------------------------
    | @author cq
    */
    public function  applyDetails() {
        $user_apply_id = I('get.id');
        $where['user_apply_id'] = $user_apply_id;
        $get_info = D('User', 'Service')->getApplyUserInfo($where);
        $data['info'] = $get_info['data'];
        $this->assign('data', $data);
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
            $reflag = D("User","Service")->auditTransfer($post);
            //返回数据操作状态
            if ($reflag['code'] == 0) $this->ajaxReturn(0, $reflag['msg'], U('System/User/auditList'));
            else  $this->ajaxReturn(1, $reflag['msg']);
        }
        $user_apply_id = I("get.id");
        $where['user_apply_id'] = $user_apply_id;
        $get_info = D('User', 'Service')->getApplyUserInfo($where);
        $data['info'] = $get_info['data'];
        $this->assign('data', $data);
        $this->display();

    }


    /*
   |--------------------------------------------------------------------------
   | 审核转入列表
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public  function  auditList() {
        $request = $_where = I('get.');
        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($_where['page']);
        //时间格式转化
        if (!empty($request['applytime'])) {
            $_time = explode('@', str_replace('/', '-', $request['applytime']));
            $_where['applytime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59'))), 'AND');
        }
        $_where['zone_id'] = $this->system_user['zone_id'];
        $_where['admin_system_user_id'] = $this->system_user_id;
        //获取列表数据
        $_where['page'] = (($re_page - 1) * 30) . ',30';
        $auditList = D('User', 'Service')->getApplyUserList($_where);
        $data['auditList'] = $auditList['data']['data'];
        //加载分页类
        $data['paging_div'] = $this->Paging($re_page, 30, $auditList['data']['count'], $request);
        //获取区域下员工
        $data['systemList'] = D('SystemUser', 'Service')->getSystemUsersList(array('zone_id' => $this->system_user['zone_id'],'order'=>'sign asc'));
        $data['systemList'] = $data['systemList']['data']['data'];
        $data['request'] = $request;
        $this->assign('data', $data);
        $this->display();
    }

    /* 导入客户模板列表页
    * @author
    */
    public function importTemplateList()
    {
        $setPages['type'] = 2; //推广计划导入模板类型
        $result = D('Proid', 'Service')->getSetPages($setPages);
        $pages = $result['data'];
        foreach ($pages as $key => $page) {
            $result = D('Channel', 'Service')->getChannelInfo(array('channel_id'=>$page['channel_id']));
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
        $res = D('Channel', 'Service')->getChannelList();
        $type = I("get.type");
        if (IS_POST) {
            $setPages = I("post.");
            $setPages['type'] = $type;
            $result = D("Proid", 'Service')->createSetPages($setPages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            if ($setPages['type'] == 2) {
                $this->ajaxReturn(0, '设置模板成功', U('System/User/importTemplateList', array('type' => $setPages['type'])));
            } elseif ($setPages['type'] == 3) {
                $this->ajaxReturn(0, '设置模板成功', U('System/User/outputTemplateList', array('type' => $setPages['type'])));
            }
        }
        $this->assign("type", $type);
        $this->assign("channelList", $res['data']);
        $this->display();

    }

    /**
     * 修改模板
     * @author
     *
     */
    public function editTemplate()
    {
        $res = D('Channel', 'Service')->getChannelList();
        $data['setpages_id'] = I("get.setpages_id");
        $type = I("get.type");
        if (IS_POST) {
            $setpages = I("post.");
            $setpages['setpages_id'] = $data['setpages_id'];
            $setpages['type'] = $type;
            $result = D('Proid', 'Service')->editSetPages($setpages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            if ($type == 2) {
                $this->ajaxReturn(0, '修改模板成功', U('System/User/importTemplateList', array('type' => $type)));
            } elseif ($type == 3) {
                $this->ajaxReturn(0, '修改模板成功', U('System/User/outputTemplateList', array('type' => $type)));
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
        $result = D('Proid', 'Service')->getSetPages($data);
        $pagesInfo = $result['data'];
        $result = D('Channel', 'Service')->getChannelInfo(array('channel_id'=>$pagesInfo[0]['channel_id']));
        $channelInfo = $result['data'];
        $this->assign("channelInfo", $result['data']);
        $this->assign('channelList', $res['data']);
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
        $setpages['setpages_id'] = I("post.setpages_id");
        $setpages['type'] = I("get.type");
        $backInfo = D('Proid', 'Service')->delSetPages($setpages);
        if ($backInfo['code'] == 0) {
            if ($setpages['type'] == 2) {
                $this->ajaxReturn(0, '删除模板成功', U('System/User/importTemplateList', array('type' => $setpages['type'])));
            } else {
                $this->ajaxReturn(0, '删除模板成功', U('System/User/outputTemplateList', array('type' => $setpages['type'])));
            }
        }
        $this->ajaxReturn(201, '删除失败');

    }


    /**
     * 导入客户
     * @author Nixx
     */
    public function importUser()
    {
        set_time_limit(3000);
        $zone_id = $this->system_user['zone_id'];
        $type = I("get.type");     
        if (IS_POST) {
            $request['setpages_id'] = I("post.setpages_id"); //模板ID
            if (!empty($_FILES['file'])) {
                $exts = array('xls', 'xlsx');
                $rootPath = './Public/';
                $savePath = 'User/';
                $uploadFile = $this->uploadFile($exts, $rootPath, $savePath);
                $filename = $rootPath . $uploadFile['file']['savepath'] . $uploadFile['file']['savename'];
            }
            $datas = importExecl($filename);
            unlink($filename);
            foreach ($datas as $key => $data) {
                array_unique($data);
            }
            D('User', 'Service')->inputUser($request, $datas);
            $this->redirect('/System/User/importClient', array('type' => $type));
        } else {
            $this->assign("system_user_id", $this->system_user_id);
            $set['type'] = $type;
            $res = D('Proid', 'Service')->getSetPages($set);
            $setPages = $res['data'];
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
        set_time_limit(3000);
        $zone_id = $this->system_user['zone_id'];
        $type = I("get.type");
        if (IS_POST) {
            $request['setpages_id'] = I("post.setpages_id"); //模板ID
            if (!empty($_FILES['file'])) {
                $exts = array('xls', 'xlsx');
                $rootPath = './Public/';
                $savePath = 'User/';
                $uploadFile = $this->uploadFile($exts, $rootPath, $savePath);
                $filename = $rootPath . $uploadFile['file']['savepath'] . $uploadFile['file']['savename'];
            }
            $datas = importExecl($filename);
            unlink($filename);
            foreach ($datas as $key => $data) {
                array_unique($data);
            }
            D('User', 'Service')->inputUser($request, $datas);
            $this->redirect('/System/User/importClient', array('type' => $type));
    
        } else {
            $this->assign("system_user_id", $this->system_user_id);
            $set['type'] = $type;
            $res = D('Proid', 'Service')->getSetPages($set);
            $setPages = $res['data'];
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
        $setPages['type'] = 3; 
        $result = D('Proid', 'Service')->getSetPages($setPages);
        $pages = $result['data'];
        foreach ($pages as $key => $page) {
            $res = D('Channel', 'Service')->getChannelInfo(array('channel_id'=>$page['channel_id']));
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
        $setpages['type'] = 3;
        $setpages['system_user_id'] = $this->system_user_id;
        $res = D('Proid', 'Service')->getSetPages($setpages);
        $setpagesList = $res['data'];
        if (IS_POST) {
            $request = I('post.');
            if($request['type']=='succ'){
                if(session("outputUser_path")){
                    $code = session("outputUser_path");
                    $this->ajaxReturn($code['code'],$code['msg'],$code['data']);
                }
                $this->ajaxReturn(1,'');
            }else{
                D('User','Service')->outputUser($request);
            }
        }
        //跟进结果
        $data['attitude'] = C('USER_ATTITUDE');
        $dataChannel = D('Channel', 'Service')->getChannelList();
        $data['channel'] = $dataChannel['data'];
        //学习平台：
        $data['learningtype'] = C('USER_LEARNINGTYPE');
        //学习方式
        $data['studytype'] = C('USER_STUDYTYPE');
        //课程列表
        $courseResult = D('Course', 'Service')->getCourseList();
        $data['courseAll'] = $courseResult['data']['data'];
        $this->assign('data', $data);   
        $this->assign("setpagesList", $setpagesList);
        $this->display();

    }

}

