<?php

namespace Common\Controller;

use Common\Controller\BaseController;
use Common\Service\DataService;

class OrderController extends BaseController
{
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 创建订单
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function createOrder($request)
    {
        //必要参数
        if(empty($request['user_id']) || empty($request['zone_id']) || empty($request['system_user_id']) )
        {
            return array('code'=>'1', 'msg'=>'参数异常');
        }
//        if ($request['subscription'] < 100) return array('code'=>'1', 'msg'=>'预报金额不能少于100');
        if ($request['subscription'] > 3000) return array('code'=>'1', 'msg'=>'预报金额不能大于3000');
        $userInfo = D('User')->field('system_user_id,username')->where(array('user_id'=>$request['user_id']))->find();
        if($userInfo['system_user_id']!=$request['system_user_id'])  return array('code'=>'2', 'msg'=>'只有所属人才能提交预报订单');
        //加密手机号，更新User
        if(!empty($request['realname'])) $user_save['realname'] = $request['realname'];
        if(!empty($request['username'])){
            //实例验证类
            $checkform = new \Org\Form\Checkform();
            if(!$checkform->checkMobile($request['username'])) return array('code'=>1,'msg'=>'手机号码格式有误');
            $user_new_username = encryptPhone(trim($request['username']), C('PHONE_CODE_KEY'));
            if($user_new_username!=$userInfo['username']){
                $isusername = D('User')->where(array('username'=>$user_new_username))->find();
                if(!empty($isusername)) return array('code'=>'5','msg'=>'手机号码已存在');
                $user_save['username'] = $user_new_username;
            }
        }
        $user_save['status'] = 70;//交易中
        //开启事务
        D()->startTrans();
        $updata_flag = D("User")->where(array('user_id'=>$request['user_id']))->save($user_save);
        //创建order，状态：待审核
        $order['user_id'] = $request['user_id'];
        $order['zone_id'] = $request['zone_id'];
        $order['system_user_id'] = $request['system_user_id'];
        $order['status'] = 10;
        $order['paytype'] = 1;
        $order['subscription'] = $request['subscription'];
        $order['createtime'] = time();
        $order_id = D("Order")->add($order);
        if ($order_id!==false && $updata_flag!==false) {
            D()->commit();
            return array('code'=>'0', 'msg'=>'订单创建成功');
        }
        D()->rollback();
        return array('code'=>'4', 'msg'=>'预报订单创建失败');

    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户历史订单与缴费记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserOrder($where)
    {
        //参数异常
        if( empty($where['user_id']) )  return array('code'=>'1', 'msg'=>'参数异常');
        $field = '*';
        $join = 'LEFT JOIN (select `system_user_id`,`realname` as `system_user_name`,`face` as `system_user_face` from zl_system_user)__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ORDER__.system_user_id';
        //获取订单列表
        $result = D('Order')->getList($where, 'createtime DESC', null, $field, $join);
        //获取记录列表
        if(!empty($result)){
            //课程列表
            $courseMain = new CourseController();
            $discountController = new DiscountController();
            $courseList = $courseMain->getList();
            //课程列表status
            foreach($courseList['data'] as $k=>$v){
                $courseArr[$v['course_id']] = $v['coursename'];
            }
            $studytypeArr = C('USER_STUDYTYPE');
            $loan_institutionsArr = C('USER_LOAN_INSTITUTIONS');
            $receivetypeArr = C('USER_RECEIVETYPE');
            $field2 = '*';
            $join2 = 'LEFT JOIN (select `system_user_id`,`realname` as `system_user_name` from zl_system_user)__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ORDER_LOGS__.auditoruser_id';
            foreach($result as $k=>$v){
                //添加类型名称
                $result[$k]['course_name'] = (empty($v['course_id']) || $v['course_id']==0)?'':$courseArr[$v['course_id']];
                if($v['status']<40){
                    $result[$k]['studytype_name'] = '';
                }else{
                    $result[$k]['studytype_name'] = $studytypeArr[$v['studytype']]['text'];
                }
                $result[$k]['loan_institutions_name'] = ($v['status']>=40 && $v['loan_institutions_id']!=0)?$loan_institutionsArr[$v['loan_institutions_id']]['text']:'';
                $result[$k]['payway_name'] = $receivetypeArr[$v['payway']]['text'];
                $result[$k]['finish_time'] = ($v['finishtime']!=0)?date('Y-m-d H:i:s', $v['finishtime']):'';
                $result[$k]['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
                //获取订单交易记录
                $logs_where['order_id'] = $v['order_id'];
                $relogs = null;
                $relogs = D('OrderLogs')->getList($logs_where, 'practicaltime DESC', $field2, $join2);
                if(!empty($relogs)){
                    foreach($relogs as $k2=>$v2){
                        //添加类型名称
                        $relogs[$k2]['payway_name'] = $receivetypeArr[$v2['payway']]['text'];
                        $relogs[$k2]['practicaltime'] = date('Y-m-d', $v2['practicaltime']);
                    }
                    $result[$k]['logs'] = $relogs;
                }
                //是否存在 优惠ID字符串
                if(!empty($v['discount_id'])){
                    $discount_ids = explode(',', $v['discount_id']);
                    foreach($discount_ids as $v){
                        $reDiscount = $discountController->getInfo($v);
                        if($reDiscount['code']==0){
                            $result[$k]['discount_arr'][] = $reDiscount['data'];
                        }
                    }
                }
            }
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户历史订单与缴费记录
    |--------------------------------------------------------------------------
    | @author
    */
    public function getOrderInfo($order_id)
    {
        //参数异常
        if( empty($order_id) )  return array('code'=>'1', 'msg'=>'参数异常');
        $field = $this->getField();
        $join = $this->getJoin();
        $orderInfo['orderInfo'] = D("Order")->field($field)->join($join)->where(array("order_id"=>$order_id))->find();
        //获取记录列表
        if(!empty($orderInfo['orderInfo'])){
            $where['order_id'] = $order_id;
            $orderInfo['orderLogList'] = D("OrderLogs")->where($where)->order('practicaltime desc')->select();
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$orderInfo);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where, $order=null, $limit=null)
    {
        //参数过滤
        $where = $this->dispostWhere($where);
        $field = $this->getField();
        $join = $this->getJoin();
        //获取Model数据
        if(!empty($order)){
            $order = "{$this->DB_PREFIX}order.".$order;
        }
        $result = D('Order')->getList($where, $order, $limit, $field, $join);
        if(!empty($result)) {
            //课程列表
            $courseMain = new CourseController();
            $courseList = $courseMain->getList();
            //课程列表status
            foreach ($courseList['data'] as $k => $v) {
                $courseArr[$v['course_id']] = $v['coursename'];
            }
            $studytypeArr = C('USER_STUDYTYPE');
            $loan_institutionsArr = C('USER_LOAN_INSTITUTIONS');
            $receivetypeArr = C('USER_RECEIVETYPE');
            $orderstatusArr = C('ORDER_STATUS');
            $zoneController = new ZoneController();
            foreach($result as $k=>$v){
                //添加类型名称
                $result[$k]['status_name'] = $orderstatusArr[$v['status']];
                $zoneInfo = $zoneController->getZoneInfo($v['zone_id']);
                $result[$k]['zone_name'] =$zoneInfo['name'];
                $result[$k]['mobile'] = decryptPhone($v['username'], C('PHONE_CODE_KEY'));
                $result[$k]['course_name'] = (empty($v['course_id']) || $v['course_id']==0)?'':$courseArr[$v['course_id']];
                $result[$k]['studytype_name'] = $studytypeArr[$v['studytype']]['text'];
                $result[$k]['loan_institutions_name'] = ($v['status']>=40 && $v['loan_institutions_id']!=0)?$loan_institutionsArr[$v['loan_institutions_id']]['text']:'';
                $result[$k]['payway_name'] = $receivetypeArr[$v['payway']]['text'];
                $result[$k]['finish_time'] = ($v['finishtime']!=0)?date('Y-m-d H:i:s', $v['finishtime']):'';
                $result[$k]['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
            }
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单列表总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where)
    {
        //参数过滤
        $where = $this->dispostWhere($where);
        $join = $this->getJoin();
        //获取Model数据
        $result = D('Order')->getCount($where,$join);
        //返回数据与状态
        return array('code'=>'0', 'data'=>empty($result)?0:$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 根据客户ID 该客户是否已有审核订单
    |--------------------------------------------------------------------------
    | user_id
    | @author zgt
    */
    public function isUserOrder($data)
    {
        //必选参数
        if(empty($data['user_id'])) return array('code'=>1,'msg'=>'参数异常！');
        $isempty = D("Order")->where(array('user_id'=>$data['user_id'],'status'=>array('IN','10,30,40')))->find();

        //返回数据与状态
        if(!empty($isempty)) {
            return array('code'=>'1', 'msg'=>'该客户已有未完成订单');
        }
        return array('code'=>'0', 'msg'=>'该客户无未完成订单');
    }

    /*
    |--------------------------------------------------------------------------
    | 该客户是否已有审核订单
    |--------------------------------------------------------------------------
    | order_id
    | @author zgt
    */
    public function isAuditOrder($data)
    {
        //必选参数
        if(empty($data['order_id'])) return array('code'=>1,'msg'=>'参数异常！');
        $orderInfo = D("Order")->where(array("order_id"=>$data['order_id']))->field("zone_id,user_id,status,payway,subscription,createtime")->find();
        if (!$orderInfo) return array('code'=>'2', 'msg'=>'订单不存在');
        $isempty = D("Order")->where(array('user_id'=>$orderInfo['user_id'],'status'=>array('IN','30,40')))->find();

        //返回数据与状态
        if(!empty($isempty)) {
            return array('code'=>'1', 'msg'=>'该客户已有未完成订单');
        }
        return array('code'=>'0', 'msg'=>'该客户无未完成订单');
    }

    /*
    |--------------------------------------------------------------------------
    | 订单审核
    |--------------------------------------------------------------------------
    | subscription payway practicaltime status
    | @author nxx
    */
    public function auditOrder($data)
    {
        $orderInfo = D("Order")->where("order_id = $data[order_id]")->field("zone_id,system_user_id,user_id,order_id,status,payway,subscription,createtime")->find();
        if (!$orderInfo) return array('code'=>'2', 'msg'=>'订单不存在');
        if ($orderInfo['status']!=10) return array('code'=>'2', 'msg'=>'订单已被审核,无法重复操作！');
        //审核人
        $orderInfo['auditoruser_id'] = $data['system_user_id'];
        if ($data['status'] == 'success') {
            $orderInfo['status'] = 30;
            $orderInfo['cost'] = $orderInfo['subscription'];
            $orderInfo['auditoruser_id'] = $data['system_user_id'];
            //启动事务
            D()->startTrans();
            $flag_save = D('Order')->where(array('order_id'=>$data['order_id']))->save($orderInfo);
            if($orderInfo['subscription']>0){
                $addLog['zone_id'] = $orderInfo['zone_id'];
                $addLog['order_id'] = $data['order_id'];
                $addLog['paytype'] = 1;
                $addLog['payway'] = $data['payway'];
                $addLog['cost'] = $orderInfo['cost'];
                $addLog['practicaltime'] = $data['practicaltime'];
                $addLog['auditoruser_id'] = $data['system_user_id'];
                $flag_add = $this->addLog($addLog);
                //操作添加数据记录
                $dataLog['operattype'] = 13;
                $dataLog['operator_user_id'] = $orderInfo['system_user_id'];
                $dataLog['user_id'] = $orderInfo['user_id'];
                $dataLog['logtime'] = time();
                $DataService = new DataService();
                $DataService->addDataLogs($dataLog);
            }else{
                $flag_add = true;
            }

            if($flag_save!==false && $flag_add!==false) {
                D()->commit();
                return array('code'=>0,'msg'=>'订单审核成功');
            }
            D()->rollback();
            return array('code'=>1,'msg'=>'订单审核通过操作失败！');
        }elseif ($data['status'] == 'faile') {
            $save['status'] = 20;
            $flag_save = D("Order")->where(array('order_id'=>$data['order_id']))->save($save);
            if ($flag_save === false) {
                return array('code'=>'3', 'msg'=>'订单审核不通过失败');
            }
            return array('code'=>'0', 'msg'=>'订单审核结果为不通过');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 提交缴费
    |--------------------------------------------------------------------------
    | order_id system_user_id course_id  discount_id loan_institutions_id loan_institutions_cost? cost?
    | @author zgt
    */
    public function submitOrder($data)
    {
        $orderInfo = D('Order')->where(array('order_id'=>$data['order_id']))->find();
        if(empty($orderInfo)) return array('code'=>1,'msg'=>'订单不存在！');
        if ($orderInfo['status']!=30) return array('code'=>'2', 'msg'=>'订单未在审核通过状态,无法操作！');
        $save['status'] = 40;
        $save['course_id'] = $data['course_id'];
        $save['discount_id'] = $data['discount_id'];
        $save['studytype'] = $data['studytype'];
        //学费总额
        $coursecost = D('Course')->getCourseInfo($data['course_id']);
        $save['coursecost'] = (int)$coursecost['price'];
        //优惠金额
        $getDiscountCost = $this->getDiscountCost($data['discount_id']);
        $save['discountcost'] = (int)$getDiscountCost;
        //实际总额
        $save['paycount'] = ((int)$save['coursecost'])-((int)$save['discountcost']);
        //贷款？
        $save['loan_institutions_id'] = !empty($data['loan_institutions_id'])?$data['loan_institutions_id']:0;
        $save['cost'] = !empty($orderInfo['subscription'])?$orderInfo['subscription']:0;
        if($data['loan_institutions_id']!=0)
        {
//            if($data['loan_institutions_cost']>$save['paycount']) return array('code'=>1,'msg'=>'贷款金额不能大于实际总额！');
            $save['loan_institutions_cost'] = $data['loan_institutions_cost'];
        }
        $save['sparecost'] = ((int)$save['paycount'])-((int)$save['cost']);
        //存在实收款
        if(!empty($data['cost'])){
            $save['cost'] = ((int)$save['cost'])+((int)$data['cost']);
            $save['sparecost'] = ((int)$save['sparecost'])-((int)$data['cost']);
        }
        //启动事务
        D()->startTrans();
        $flag_save = D('Order')->where(array('order_id'=>$data['order_id']))->save($save);
        //添加实时收款记录？
        if(!empty($data['cost'])){
            $addLog2['order_id'] = $orderInfo['order_id'];
            $addLog2['status'] = 2;
            $addLog2['paytype'] = 1;
            $addLog2['auditoruser_id'] = $data['system_user_id'];
            $addLog2['cost'] = $data['cost'];
            $addLog2['createtime'] = time();
            $flag_add2 = D('OrderLogs')->data($addLog2)->add();
        }else{
            $flag_add2 = true;
        }
        if($flag_save!==false && $flag_add2!==false) {
            D()->commit();
            return array('code'=>0,'msg'=>'提交缴费成功');
        }
        D()->rollback();
        return array('code'=>2,'msg'=>'提交缴费操作失败！');
    }

    /*
    |--------------------------------------------------------------------------
    | 收款
    |--------------------------------------------------------------------------
    | order_id zone_id system_user_id cost payway practicaltime
    | @author zgt
    */
    public function payOrder($data)
    {
        if(empty($data['order_id']) || empty($data['zone_id']) || empty($data['system_user_id'])) return array('code'=>1,'msg'=>'参数异常！');
        $orderInfo = D('Order')->where(array('order_id'=>$data['order_id']))->find();
        if(empty($orderInfo)) return array('code'=>2,'msg'=>'订单不存在！');
        if($orderInfo['status']!=40 && $orderInfo['status']!=60) return array('code'=>3,'msg'=>'订单状态无法继续收款！');
        if($data['cost']<0) return array('code'=>5,'msg'=>'收款金额不能小于0');
//        if($data['cost']>((int)$orderInfo['sparecost'])) return array('code'=>6,'msg'=>'收款金额不能大于欠费金额！');
        if($orderInfo['status']==60) $save['status'] = 40;
        if(($orderInfo['sparecost']-$data['cost'])<=0){
            $save['status'] = 50;
            $save['finishtime'] = time();
        }
        $save['sparecost'] = ($orderInfo['sparecost']-$data['cost']);
        $save['cost'] = ((int)$orderInfo['cost'])+((int)$data['cost']);
        //启动事务
        D()->startTrans();
        $flag_save = D('Order')->where(array('order_id'=>$data['order_id']))->save($save);
        $addLog['order_id'] = $orderInfo['order_id'];
        $addLog['zone_id'] = $data['zone_id'];
        $addLog['paytype'] = 1;
        $addLog['payway'] = $data['payway'];
        $addLog['auditoruser_id'] = $data['system_user_id'];
        $addLog['cost'] = $data['cost'];
        $addLog['practicaltime'] = $data['practicaltime'];
        $addLog['createtime'] = time();
        $flag_add = D('OrderLogs')->data($addLog)->add();
        if($flag_save!==false && $flag_add!==false) {
            D()->commit();
            return array('code'=>0,'msg'=>'收款操作成功');
        }
        D()->rollback();
        return array('code'=>3,'msg'=>'收款操作失败！');
    }


    /*
    |--------------------------------------------------------------------------
    | 退款操作
    |--------------------------------------------------------------------------
    | cost order_id
    | @author nxx
    */
    public function refundOrder($data)
    {
        //参数异常
        if(empty($data['order_id']) || empty($data['zone_id']) || empty($data['system_user_id']) || empty($data['payway']) || empty($data['practicaltime'])) return array('code'=>1,'msg'=>'参数异常！');
        if(!empty($data['cost']) && $data['cost']<0) return array('code'=>5,'msg'=>'退款金额不能小于0');
        $orderInfo = D("Order")->where(array("order_id"=>$data['order_id']))->find();
        if($orderInfo['status']==30){
            $order['status'] = 70;
            $data['cost'] = $orderInfo['cost'];
            $order['cost'] = '0.00';
        }else{
            if ($data['cost'] == $orderInfo['cost']) {
                $order['status'] = 70;
                $order['cost'] = '0.00';
                $order['sparecost'] = '0.00';
            }elseif(((int)$data['cost']) < ((int)$orderInfo['cost'])){
                $order['status'] = 60;
                $order['cost'] = ((int)$orderInfo['cost']) - ((int)$data['cost']);
                $order['sparecost'] = ((int)$orderInfo['sparecost']) + ((int)$data['cost']);
            }elseif(((int)$data['cost']) > ((int)$orderInfo['cost'])){
                return array('code'=>2,'msg'=>'退款金额大于缴费金额');
            }
        }
        //启动事务
        D()->startTrans();
        $order['refundtime'] = time();
        //修改订单信息
        $updata = D("Order")->where(array("order_id"=>$data['order_id']))->save($order);
        //退款记录
        $addLog['order_id'] = $data['order_id'];
        $addLog['zone_id'] = $data['zone_id'];
        $addLog['paytype'] = 2;
        $addLog['payway'] = $data['payway'];
        $addLog['cost'] = $data['cost'];
        $addLog['auditoruser_id'] = $data['system_user_id'];
        $addLog['practicaltime'] = $data['practicaltime'];
        $addLog['createtime'] = time();
        $flag_add = D('OrderLogs')->add($addLog);
        if($updata!==false && $flag_add!==false) {
            //操作添加数据记录
            $dataLog['operattype'] = 14;
            $dataLog['operator_user_id'] = $orderInfo['system_user_id'];
            $dataLog['user_id'] = $orderInfo['user_id'];
            $dataLog['logtime'] = time();
            $DataService = new DataService();
            $DataService->addDataLogs($dataLog);
            D()->commit();
            return array('code'=>0,'msg'=>'退费成功');
        }
        D()->rollback();
        return array('code'=>0,'msg'=>'退费失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 缴费优惠项目表
    |--------------------------------------------------------------------------
    | cost order_id
    | @author nxx
    */
    public function getDiscount()
    {
        $discountList = D('Discount')->where('type=1 and pid!=0')->select();
        // foreach ($parentList as $key => $value) {
        //     $value['children'] = D('Discount')->where("pid = $value[discount_parent_id] and type=1")->select();
        //     $discountList[$key] = $value;
        // }
        return $discountList;
    }


    /*
    |--------------------------------------------------------------------------
    | 导出订单列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function outputOrderList($requestP)
    {
        if (!$requestP['zone_id']) {
            $sysUser = D("SystemUser")->where("system_user_id = $requestP[system_user_id]")->find();
            $requestP['zone_id'] = $sysUser['zone_id'];
        }
        unset($requestP['system_user_id']);
        $result = $this->getList($requestP, '', '0,1000');
        $orderList = $result['data'];
        foreach ($orderList as $key => $order) {
            $userInfo = D("User")->where("user_id = $order[user_id]")->find();
            $channel = D("Channel")->where("channel_id = $userInfo[channel_id]")->find();
            if ($userInfo) {
                $orderList[$key]['user_realname'] = $userInfo['realname'];
                $orderList[$key]['username'] = decryptPhone($userInfo['username'],C('PHONE_CODE_KEY'));
            }
            $updataSystemUser = D("SystemUser")->where("system_user_id = $userInfo[updateuser_id]")->field("realname")->find();
            $createSystemUser = D("SystemUser")->where("system_user_id = $userInfo[createuser_id]")->field("realname")->find();
            if ($order['createtime']) {
                $order['createtime'] = date('Y-m-d H:i:s', $order['createtime']);
            }
            if ($order['finishtime']) {
                $order['finishtime'] = date('Y-m-d H:i:s', $order['finishtime']);
            }
            $newOrderList[$key]['user_realname'] = $order['user_realname'];
            $newOrderList[$key]['channelname'] = $channel['channelname'];
            $newOrderList[$key]['mobile'] = $order['mobile'];
            $newOrderList[$key]['system_user_realname'] = $order['system_user_realname'];
            $newOrderList[$key]['updata_realname'] = $updataSystemUser['realname'];
            $newOrderList[$key]['create_user_realname'] = $createSystemUser['realname'];
            $newOrderList[$key]['subscription'] = $order['subscription'];
            $newOrderList[$key]['course_name'] = $order['course_name'];
            $newOrderList[$key]['coursecost'] = $order['coursecost'];
            $newOrderList[$key]['discountcost'] = $order['discountcost'];
            $newOrderList[$key]['paycount'] = $order['paycount'];
            $newOrderList[$key]['cost'] = $order['cost'];
            $newOrderList[$key]['sparecost'] = $order['sparecost'];
            $newOrderList[$key]['payway_name'] = $order['payway_name'];
            $newOrderList[$key]['loan_institutions_name'] = $order['loan_institutions_name'];
            $newOrderList[$key]['loan_institutions_cost'] = $order['loan_institutions_cost'];
            $newOrderList[$key]['status_name'] = $order['status_name'];
            $newOrderList[$key]['studytype_name'] = $order['studytype_name'];
            $newOrderList[$key]['auditoruser_realname'] = $order['auditoruser_realname'];
            $newOrderList[$key]['create_time'] = $order['create_time'];
            $newOrderList[$key]['finish_time'] = $order['finish_time'];
        }
        $excel_key_value = array(
            'user_realname'=>'客户姓名',
            'channelname'=>'渠道',
            'mobile'=>'客户电话',
            'system_user_realname'=>'所属人',
            'updata_realname'=>'出库人',
            'create_user_realname'=>'创建人',
            'subscription'=>'预报金额',
            'course_name'=>'进班课程',
            'coursecost'=>'学费总额',
            'discountcost'=>'优惠金额',
            'paycount'=>'实际总额',
            'cost'=>'已缴金额',
            'sparecost'=>'欠费金额',
            'payway_name'=>'预报收款方式',
            'loan_institutions_name'=>'贷款机构',
            'loan_institutions_cost'=>'贷款金额',
            'status_name'=>'审核状态',
            'studytype_name'=>'学习方式',
            'auditoruser_realname'=>'财务',
            'create_time'=>'订单创建时间',
            'finish_time'=>'订单完成时间',
        );
        $letter = array('A','B','C','D','E','F','G','H','I','J','H','I','J','K','L','M','N','O');
        $filename = "outputOrder";
        return outExecl($filename,array_values($excel_key_value),$newOrderList,$letter);
    }

    /**
     * 获取优惠总金额
     */
    protected function getDiscountCost($str)
    {
        if (!empty($str)) {
            $_discount_id = explode(',', $str);
            $dmoney_num = 0;
            foreach ($_discount_id as $k => $v) {
                $reInfo = D('Discount')->field('dmoney')->where(array('discount_id'=>$v))->find();
                $dmoney_num = $dmoney_num + round($reInfo['dmoney']);
            }
            if ($dmoney_num > C('ADMIN_USER_MAX_DISCOUNT')) {
                $discount_cost = C('ADMIN_USER_MAX_DISCOUNT');
            } else {
                $discount_cost = $dmoney_num;
            }
            return $discount_cost;
        }
        return 0;
    }


    /**
     * 添加交易记录
     * order_id
     * @author nxx
     */
    protected function addLog($data)
    {
        if(empty($data['order_id']) && empty($data['paytype']) && empty($data['payway']) && empty($data['auditoruser_id']) && empty($data['cost']) && empty($data['practicaltime'])) return array('code'=>2,'msg'=>'缺少参数');
//        $data['order_id'] = $data['order_id'];
//        $data['paytype'] = $data['paytype'];
//        $data['payway'] = $data['payway'];
//        $data['auditoruser_id'] = $data['auditoruser_id'];
//        $data['cost'] = $data['cost'];
//        $data['practicaltime'] = $data['practicaltime'];
        $data['createtime'] = time();
        $flag_add = D('OrderLogs')->data($data)->add();
        return array('code'=>0, 'data'=>$flag_add);
    }

    /**
     * 参数过滤
     * @author zgt
     */
    protected function dispostWhere($where)
    {
        foreach($where as $k=>$v){
            if($k=='role_id'){
                if(!empty($v)){
                    $ids = $this->getRoleIds($v);
                    if($ids!==false){
                        $where["{$this->DB_PREFIX}order.system_user_id"] = array('IN', $ids);
                    }
                }
            }elseif($k=='status'){
                $where["{$this->DB_PREFIX}order.".$k] = array('IN', $v);;
            }elseif(!empty($v)){
                $where["{$this->DB_PREFIX}order.".$k] = $v;
            }
            unset($where[$k]);
        }
        if (!empty($where["{$this->DB_PREFIX}order.key_name"]) && !empty($where["{$this->DB_PREFIX}order.key_value"])) {
            if ($where["{$this->DB_PREFIX}order.key_name"] == 'username'){
                $where["{$this->DB_PREFIX}user.username"] = encryptPhone(trim($where["{$this->DB_PREFIX}order.key_value"]), C('PHONE_CODE_KEY'));
            }else{
                $where["{$this->DB_PREFIX}user.".$where["{$this->DB_PREFIX}order.key_name"]] = array('like', '%' . $where["{$this->DB_PREFIX}order.key_value"] . '%');
            }
            unset($where["{$this->DB_PREFIX}order.zone_id"]);
        }
        unset($where["{$this->DB_PREFIX}order.key_name"]);
        unset($where["{$this->DB_PREFIX}order.key_value"]);
        if(!empty($where["{$this->DB_PREFIX}order.zone_id"])){
            $zoneIdArr = $this->getZoneIds($where["{$this->DB_PREFIX}order.zone_id"]);
            $where[$this->DB_PREFIX.'order.zone_id'] = array('IN',$zoneIdArr);
//            unset($where["{$this->DB_PREFIX}order.zone_id"]);
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
        if(!empty($reList)){
            foreach($reList as $v){
                $systemUserArr[] = $v['user_id'];
            }
            return $systemUserArr;
        }
        return '0';
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
     * 获取限制字段
     * @author zgt
     */
    protected function getField()
    {
        return array(
            "{$this->DB_PREFIX}order.order_id",
            "{$this->DB_PREFIX}order.status",
            "{$this->DB_PREFIX}order.zone_id",
            "{$this->DB_PREFIX}order.course_id",
            "{$this->DB_PREFIX}order.studytype",
            "{$this->DB_PREFIX}order.discount_id",
            "{$this->DB_PREFIX}order.coursecost",
            "{$this->DB_PREFIX}order.paycount",
            "{$this->DB_PREFIX}order.discountcost",
            "{$this->DB_PREFIX}order.subscription",
            "{$this->DB_PREFIX}order.payway",
            "{$this->DB_PREFIX}order.cost",
            "{$this->DB_PREFIX}order.loan_institutions_id",
            "{$this->DB_PREFIX}order.loan_institutions_cost",
            "{$this->DB_PREFIX}order.sparecost",
            "{$this->DB_PREFIX}order.createtime",
            "{$this->DB_PREFIX}order.finishtime",
            "{$this->DB_PREFIX}user.realname as user_realname",
            "{$this->DB_PREFIX}user.username",
            "{$this->DB_PREFIX}user.user_id",
            "A.system_user_id",
            "A.realname as system_user_realname",
            "B.system_user_id as auditoruser_id",
            "B.realname as auditoruser_realname"
        );
    }




    /**
     * 获取json
     * @author zgt
     */
    protected function getJoin()
    {
        return 'LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__ORDER__.system_user_id
                 LEFT JOIN __SYSTEM_USER__ B ON B.system_user_id=__ORDER__.auditoruser_id
                 INNER JOIN __USER__ ON __USER__.user_id=__ORDER__.user_id';
    }



    /**
     * 创建优惠
     */
    public function createDiscount($request)
    {
        if (!$request['repeat']) {
            $request['repeat'] = 0;
        }else{
            $repeatList = explode(",",$request['repeat']);
        }
        $discount_id = D("Discount")->data($request)->add();
        if ($discount_id) {
            if ($repeatList) {
                foreach ($repeatList as $key => $value) {
                    $discountInfo = D("Discount")->where("discount_id = $value")->find();
                    $save = array();
                    if ($discountInfo['repeat']) {
                        $save['repeat'] = $discountInfo['repeat'].",$discount_id";
                    }else{
                        $save['repeat'] = $discount_id;
                    }
                    $updata = D("Discount")->where("discount_id = $value")->save($save);
                }
            }
            return $discount_id;
        }else{
            return false;
        }
    }

    /**
     * 创建优惠分类
     */
    public function createParentDiscount($request)
    {
        $discount_parent_id = D("DiscountParent")->data($request)->add();
        if ($discount_parent_id) {
            return $discount_parent_id;
        }else{
            return false;
        }
    }
}
