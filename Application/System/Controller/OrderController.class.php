<?php
namespace System\Controller;

use Common\Controller\CourseController;
use Common\Controller\CourseProductController;
use Common\Controller\SystemController;
use Common\Controller\OrderController as OrderMainController;
use Common\Controller\DepartmentController;
use Common\Controller\RoleController;
use Common\Controller\SystemUserController;
use Common\Controller\ZoneController;
use Org\Form\Checkform;

class OrderController extends SystemController
{

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
        $where['zone_id'] = !empty($where['zone_id'])?$where['zone_id']:$this->system_user['zone_id'];
        unset($where['page']);
        unset($where['_pjax']);
        $where=array_filter($where);

        //日期转换时间戳
        foreach ($where as $k => $v) {
            if ($k == 'finishtime') {
                $_time = explode('--', str_replace('/', '-', $where[$k]));
                if($_time[1]=='') $_time[1] = 'time';
                if($_time[0]==''){
                    $where['finishtime'] = array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59')));
                }else{
                    $where['finishtime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');
                }
            }elseif ($k == 'createtime') {            
                $_time = explode('--', str_replace('/', '-', $where[$k]));
                if($_time[1]=='') $_time[1] = 'time';
                if($_time[0]==''){
                    $where['createtime'] = array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59')));
                }else{
                    $where['createtime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');
                }
            }
        }

        if(IS_POST){
            $requestP = I('post.');
            if($requestP['type']=='getPaging') {
                if(!empty($requestP['page'])) $requestG['page'] = $requestP['page'];
                //异步获取分页数据
                $orderMainController = new OrderMainController();
                $result = $orderMainController->getCount($where);
                //加载分页类
                $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $result['data'], $requestG, __ACTION__, null, 'system');
                $this->ajaxReturn(0, '', $paging_data);
            }else if($requestP['type']=='getSysUser'){
                //异步获取员工列表
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_id'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                //员工列表
                $systemUserMain = new SystemUserController();
                $reSystemList = $systemUserMain->getListCache($whereSystem);
                //返回数据操作状态
                if ($reSystemList !== false) $this->ajaxReturn(0, '', $reSystemList['data']);
                else $this->ajaxReturn(1, '');
            }
        }
        $limit = (empty($requestG['page'])?'0':($requestG['page']-1)*30).',30';
        //获取数据
        $orderMainController = new OrderMainController();
        $result = $orderMainController->getList($where, 'createtime DESC', $limit);
        //获取区域下
        $zoneMain = new ZoneController();
        $data['zoneAll']['children'] = $zoneMain->getZoneList($this->system_user['zone_id']);
        $centersign = 10;
        $centerList = $zoneMain->getZoneCenter($centersign);
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
        $data['zone_id'] = $this->system_user['zone_id'];
        $this->assign('data', $data);
        $this->assign('centerList', $centerList);
        $this->display();
    }
    /*
    |--------------------------------------------------------------------------
    | 缴费信息
    |--------------------------------------------------------------------------
    | order_id system_user_id course_id  discount_id loan_institutions_id loan_institutions_cost? cost?
    | @author zgt
    */
    public function addOrderInfo()
    {
        $order_id = I('get.order_id');
        if(IS_POST){
            $request = I('post.');
            $request['system_user_id'] = $this->system_user_id;
            if (!$order_id) $this->ajaxReturn(1, '参数信息有误');
            if (empty($request['course_id'])) $this->ajaxReturn(2, '请选择课程');
            if (empty($request['studytype'])) $this->ajaxReturn(3, '请选择学习方式');
            if (empty($request['loan_institutions_id']) && $request['loan_institutions_id']!=='0') $this->ajaxReturn(4, '请选择付款类型');
            $request['order_id'] = $order_id;
            $orderMainController = new OrderMainController();
            $result = $orderMainController->submitOrder($request);
            if($result['code']==0){
                $this->ajaxReturn($result['code'], $result['msg'], U('System/Order/orderList'));
            }
            $this->ajaxReturn($result['code'], $result['msg']);
        }
        $orderMainController = new OrderMainController();
        $orderInfo = $orderMainController->getOrderInfo($order_id);
        $data['info'] = $orderInfo['data'];
        //课程列表
        $CourseProductController = new CourseProductController();
        $courseList = $CourseProductController->getList();
        $data['courseList'] = $courseList['data'];
        //优惠方式
        $data['discount'] = $orderMainController->getDiscount();
        //获取配置状态值
        $data['order_loan_institutions'] = C('USER_LOAN_INSTITUTIONS');
        $data['order_studytype'] = C('USER_STUDYTYPE');
        $data['order_receivetype'] = C('USER_RECEIVETYPE');
        //模版赋值
        $data['order_id'] = $order_id;
        $this->assign('data', $data);
        $this->display();
    }


    /**
     * 审核订单
     * subscription practicaltime status
     * @author nxx
     */
    public function auditingOrder()
    {
        $request = I("post.");
        $orderMainController = new OrderMainController();
        //是否提示
        if($request['type']=='ishint'){
            $isAuditOrder = $orderMainController->isAuditOrder($request);
            if($isAuditOrder['code']!=0) $this->ajaxReturn('20', '该客户有未完成订单，请谨慎操作！');
            $this->ajaxReturn('0', '审核订单时需注意检查客户名称是否有误！');
        }
        $request['system_user_id'] = $this->system_user_id;
        if (!$request['order_id']) $this->ajaxReturn(1, '参数信息有误');
        if ($request['status'] == 'success') {
            if (!$request['payway']) $this->ajaxReturn(2, '收款方式不能为空');
        }
        if (!$request['practicaltime']) $this->ajaxReturn(3, '请输入收款时间');
        //添加参数
        $request['practicaltime'] = strtotime($request['practicaltime']);
        $result = $orderMainController->auditOrder($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 收款操作
     * @author zgt
     */
    public function payfund()
    {
        //获取参数
        $request = I('post.');
        $checkform = new Checkform();
        if(empty($request['order_id'])) $this->ajaxReturn(1, '参数异常！');
        if(empty($request['cost']) || !$checkform->checkInt($request['cost'])) $this->ajaxReturn(1, '请输入收款金额', '', 'receivables_cost');
        if(empty($request['payway'])) $this->ajaxReturn(1, '请输入收款方式！');
        if(empty($request['practicaltime'])) $this->ajaxReturn(1, '请输入收款日期！', '', 'receivables_practicaltime');
//        if (empty($request['zone_id'])) {
//            $this->ajaxReturn(1, '请选择中心！', '', 'receivables_practicaltime');
//        }
        //添加参数
        $request['practicaltime'] = strtotime($request['practicaltime']);
        $request['system_user_id'] = $this->system_user_id;
        $request['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
        //获取接口
        $orderMainController = new OrderMainController();
        $result = $orderMainController->payOrder($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 退费操作
     * cost order_id payway
     * @author nxx
     */
    public function refund()
    {
        $request = I("post.");
        $request['system_user_id'] = $this->system_user_id;
        if(empty($request['order_id'])) $this->ajaxReturn(1, '参数信息有误');
        if(empty($request['practicaltime'])) $this->ajaxReturn(1, '请输入退款日期！');
        $orderLog = D("OrderLogs")->where("order_id = $request[order_id]")->field("practicaltime")->order("practicaltime desc")->find();
        $request['practicaltime'] = strtotime($request['practicaltime']);
        if ($request['practicaltime'] < $orderLog['practicaltime']) {
            $this->ajaxReturn(1,"退款日期不得早于上次缴费时间");
        }       
        if(empty($request['payway'])) $this->ajaxReturn(1, '退款方式！');
        if(empty($request['type']) || $request['type']!='deposit'){
            if(empty($request['cost'])) $this->ajaxReturn(1, '退款金额！');
        }
        //添加参数
        $request['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
        //获取接口
        $orderMainController = new OrderMainController();
        $result = $orderMainController->refundOrder($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 订单详情
     * @author nxx
     */
    public function orderDetail()
    {
        $request = I('post.');
        if (!$request['user_id']) {
            $this->ajaxReturn(1, '参数信息有误');
        }
        $orderMainController = new OrderMainController();
        $orderInfo = $orderMainController->getOrderInfo($request);
        $this->assign('orderInfo', $orderInfo);
    }



    /**
     * 导出订单
     * nxx
     */
    public function outputOrder()
    {
        set_time_limit(600);
        $system_user_id = $this->system_user_id;
        $orderMainController = new OrderMainController();
        if (IS_POST) {
            $requestP = I('post.');
            if($requestP['type']=='getSysUser'){
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_id'] = $requestP['role_id'];
                //员工列表
                $systemUserMain = new SystemUserController();
                $systemUserAll = $systemUserMain->getList($whereSystem);
                $systemList = $systemUserAll['data'];
                if($systemList) $this->ajaxReturn(0, '', $systemList);
                else $this->ajaxReturn(1, '');
            }else{
                foreach ($requestP as $k => $v) {
                    if ($k == 'finishtime') {
                        $_time = explode('--', str_replace('/', '-', $requestP[$k]));
                        if($_time[1]=='') $_time[1] = 'time';
                        if($_time[0]==''){
                            $requestP['finishtime'] = array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59')));
                        }else{
                            $requestP['finishtime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');
                        }
                    }elseif ($k == 'createtime') {            
                        $_time = explode('--', str_replace('/', '-', $requestP[$k]));
                        if($_time[1]=='') $_time[1] = 'time';
                        if($_time[0]==''){
                            $requestP['createtime'] = array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59')));
                        }else{
                            $requestP['createtime'] = array(array('EGT', ($_time[0] == 'time' ? time() : strtotime($_time[0]))), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1] . ' 23:59:59'))), 'AND');
                        }
                    }
                }
                $requestP['system_user_id'] = $system_user_id;
                return $orderMainController->outputOrderList($requestP);
            }
            
        }
        $orderMainController = new OrderMainController();
        //$result = $orderMainController->getList($where, 'createtime DESC', $limit);
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
        //模版赋值
        //$data['order_list'] = $result['data'];
        $this->assign('data', $data);
        $this->display();

    }


        /*
    |--------------------------------------------------------------------------
    | 优惠列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function discountList()
    {
        $data = D("DiscountParent")->where("type = 1")->select();
        $datas = D("Discount")->where("pid != 0")->select();
        foreach ($datas as $key => $value) {
            if ($value['type'] == 1) {
                $value['typeName'] = '启用中';
            }else{
                $value['typeName'] = '已下架';
            }
            $parent = D("DiscountParent")->where("discount_parent_id = $value[pid]")->find();
            $value['pname'] = $parent['dname'];
            $discountList[$value['discount_id']] = $value;
        }
        $this->assign('discountList', $discountList);

        $this->assign('data',$data);
        $this->display();

    }

    /**
     * [discountParentList description]
     * @return [type] [description]
     */
    public function discountParentList()
    {
        $discountParentList = D("DiscountParent")->select();
        foreach ($discountParentList as $key => $value) {
            if ($value['type'] == 1) {
                $value['typeName'] = '启用中';
            }else{
                $value['typeName'] = '已下架';
            }
            $discountParentList[$key] = $value;
        }
        $this->assign('discountParentList', $discountParentList);
        $this->display();

    }

    /*
    *添加优惠
    *@author nxx
    */
    public function addDiscount()
    {
        if (IS_POST) {
            $request = I("post.");
            if (!$request['dname']) {
                $this->ajaxReturn(1,'请填写优惠名称');
            }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$request['dname'])){
                $this->ajaxReturn(2,'不能包含特殊字符');
            }
            if (strlen($request['dname'])>40) {
                $this->ajaxReturn(2,'优惠名称不得超过40个字符');
            }
            if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$request['dmoney'])){
                $this->ajaxReturn(3,"请输入正确的优惠金额");
            }
            if (!$request['dmoney']) {
                $this->ajaxReturn(4,'请填写优惠金额');
            }elseif($request['dmoney']>2000){
                $this->ajaxReturn(6,'优惠金额不能大于2000');
            }
            if (!$request['remark']) {
                $this->ajaxReturn(7,'请填写优惠详情');
            }
            if (strlen($request['remark'])>100) {
                $this->ajaxReturn(8,'优惠详情不得超过100个字符');
            } 
            if(!$request['pid']){
                $this->ajaxReturn(9,'请选择优惠所属分类');
            }
            $orderMain = new OrderMainController();
            $result = $orderMain->createDiscount($request);
            if ($result === false) {
                $this->ajaxReturn(9,'创建优惠失败');
            }
            $this->ajaxReturn(0,"创建优惠成功");
        }
    }

    /*
    *添加优惠分类
    *@author nxx
    */
    public function addParentDiscount()
    {
        if (IS_POST) {
            $request = I("post.");
            if (!$request['pid']) {
                $request['pid'] = 0;
            }
            if (!$request['dname']) {
                $this->ajaxReturn(1,'请填写优惠分类名称');
            }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$request['dname'])){
                $this->ajaxReturn(2,'不能包含特殊字符');
            }
            if (strlen($request['dname'])>40) {
                $this->ajaxReturn(3,'优惠分类名称不得超过40个字符');
            }
            if (!$request['remark']) {
                $this->ajaxReturn(4,'请填写优惠详情');
            }
            if (strlen($request['remark'])>100) {
                $this->ajaxReturn(5,'优惠详情不得超过100个字符');
            }
            $orderMain = new OrderMainController();
            $result = $orderMain->createParentDiscount($request);
            if ($result === false) {
                $this->ajaxReturn(6,'创建优惠失败');
            }
            $this->ajaxReturn(0,"创建优惠成功");
        }
    }

    /*
    *修改优惠
    *@author nxx
    */
    public function editDiscount()
    {
        if (IS_POST) {
            $request = I("post.");
            if (strlen($request['remark'])>100) {
                $this->ajaxReturn(5,'优惠详情不得超过100个字符');
            }
            if ($request['sign'] == 10) {
                $discount_parent_id = $request['discount_parent_id'];
                $save['dname'] = $request['dname'];
                $save['remark'] = $request['remark'];
                $save['type'] = $request['type'];
                if (!$save['dname']) {
                    $this->ajaxReturn(1,'请填写优惠名称');
                }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$save['dname'])){
                    $this->ajaxReturn(2,'不能包含特殊字符');
                }
                if (strlen($save['dname'])>40) {
                    $this->ajaxReturn(3,'优惠名称不得超过40个字符');
                }
                $update = D("DiscountParent")->where("discount_parent_id = $discount_parent_id")->save($save);
                if ($update === false || $update<0) {
                    $this->ajaxReturn(1,'修改优惠类型失败');
                }
                $this->ajaxReturn(0,'修改优惠类型成功');
            }else{
                if (!$request['pid']) {
                    $request['pid'] = 0;
                }
                if (!$request['dname']) {
                    $this->ajaxReturn(1,'请填写优惠名称');
                }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$request['dname'])){
                    $this->ajaxReturn(2,'不能包含特殊字符');
                }
                if (strlen($request['dname'])>40) {
                    $this->ajaxReturn(3,'优惠名称不得超过40个字符');
                }
                if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$request['dmoney'])){
                    $this->ajaxReturn(3,"请输入正确的优惠金额");
                }
                if (!$request['dmoney']) {
                    $this->ajaxReturn(4,'请填写优惠金额');
                }elseif($request['dmoney']>2000){
                    $this->ajaxReturn(6,'优惠金额不能大于2000');
                }
                $update = D("Discount")->where("discount_id = $request[discount_id]")->save($request);
                if ($update === false) {
                    $this->ajaxReturn(4,'修改优惠失败');
                }
                $this->ajaxReturn(0,'修改优惠成功');
            }
            
        }
        $discountList = D("Discount")->where("pid = 0")->select();
        $this->assign('discountList', $discountList);
    }

    /*
    *启用、禁用优惠
    *@author nxx
    */
    public function banDiscount()
    {
        $request = I("post.");
        if ($request['sign'] == 10) { //分类操作
            if(!$request['discount_parent_id']){
                $this->ajaxReturn(1,"参数有误");
            }
            $discountParentInfo = D("DiscountParent")->where("discount_parent_id = $request[discount_parent_id]")->find();
            D("DiscountParent")->startTrans();
            if ($discountParentInfo['type'] == 1) { //如果是启用，则变成禁用
                $save['type'] = 0;
                $sons = D("Discount")->where("pid = $discountParentInfo[discount_parent_id]")->select();
                if ($sons) {
                    $updateSons = D("Discount")->where("pid = $discountParentInfo[discount_parent_id]")->save($save);
                }else{
                    $updateSons = true;
                }
            }else{
                $save['type'] = 1;
                $updateSons = true;
            }
            $update = D("DiscountParent")->where("discount_parent_id = $request[discount_parent_id]")->save($save);
            if ($update===false || $updateSons===false) {
                D("DiscountParent")->rollback();
                if ($save['type'] == 0) {
                    $this->ajaxReturn(2,'下架优惠失败');
                }
                $this->ajaxReturn(3,'启用优惠失败');
            }
            D("DiscountParent")->commit();
            if ($save['type'] == 0) {
                $this->ajaxReturn(0,'下架优惠成功');
            }
            $this->ajaxReturn(0,'启用优惠成功');
        }else{
            if(!$request['discount_id']){
                $this->ajaxReturn(3,"参数有误");
            }
            $discountInfo = D("Discount")->where("discount_id = $request[discount_id]")->find();
            D("Discount")->startTrans();
            if ($discountInfo['type'] == 1) { //如果是启用，则变成禁用
                $save['type'] = 0;
            }else{
                $save['type'] = 1;
            }
            $update = D("Discount")->where("discount_id = $request[discount_id]")->save($save);
            if ($update ==false) {
                D("Discount")->rollback();
                if ($save['type'] == 0) {
                    $this->ajaxReturn(4,'下架优惠失败');
                }
                $this->ajaxReturn(5,'启用优惠失败');
            }
            D("Discount")->commit();
            if ($save['type'] == 0) {
                $this->ajaxReturn(0,'下架优惠成功');
            }
            $this->ajaxReturn(0,'启用优惠成功');
        }
        
    }



}