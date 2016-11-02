<?php
/*
* 订单服务接口
* @author nxx
*/
namespace Common\Service;
use Common\Service\DataService;
use Common\Service\BaseService;

class OrderService extends BaseService
{

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /**
     * 参数过滤
     * @author nxx
     */
    protected function _dispostWhere($where)
    {
        foreach($where as $k=>$v){
            if($k=='role_id'){
                if(!empty($v)){
                    $ids = $this->_getRoleIds($v);
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
            $zoneIdArr = $this->_getZoneIds($where["{$this->DB_PREFIX}order.zone_id"]);
            $where[$this->DB_PREFIX.'order.zone_id'] = array('IN',$zoneIdArr);
        }
        return $where;
    }

    /**
     * 职位ID  获取对应人员ID
     * @author nxx
     */
    protected function _getRoleIds($role_id)
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
     * @author nxx
     */
    protected function _getZoneIds($zone_id)
    {
        $zoneIds = D('Zone','Service')->getZoneIds(array('zone_id'=>$zone_id));
        $zoneIdArr = array();
        foreach($zoneIds['data'] as $k=>$v){
            $zoneIdArr[] = $v['zone_id'];
        }
        return $zoneIdArr;
    }

    /**
     * 获取限制字段
     * @author nxx
     */
    protected function _getField()
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
     * @author nxx
     */
    protected function _getJoin()
    {
        return 'LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__ORDER__.system_user_id
                LEFT JOIN __SYSTEM_USER__ B ON B.system_user_id=__ORDER__.auditoruser_id
                INNER JOIN __USER__ ON __USER__.user_id=__ORDER__.user_id';
    }

    /*
    |--------------------------------------------------------------------------
    | 创建订单
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function createOrder($request)
    {
        $request = array_filter($request);
        $request['system_user_id'] = $this->system_user_id;
        $request['zone_id'] = $this->system_user['zone_id'];
        //必要参数
        if(empty($request['user_id']) || empty($request['zone_id']) || empty($request['system_user_id']) ) {
            return array('code'=>301, 'msg'=>'缺少参数');
        }
        if ($request['subscription'] > 3000) return array('code'=>201, 'msg'=>'预报金额不能大于3000');
        //验证所属人
        $where['user_id'] = $request['user_id'];
        $userInfo = D('User')->getFind($where, 'realname,system_user_id,username');
        if($userInfo['system_user_id']!=$request['system_user_id']) return array('code'=>202, 'msg'=>'只有所属人才能提交预报订单');
        //更新User
        if(empty($userInfo['realname']) && empty($request['realname'])){
            return array('code'=>300, 'msg'=>'真实姓名不能为空');
        }
        if(!empty($request['realname'])){
            $user_save['realname'] = $request['realname'];
        }
        //是否需要补全手机号码
        if(empty($userInfo['username']) && empty($request['username'])){
            return array('code'=>301, 'msg'=>'请输入手机号码');
        }
        if(!empty($request['username'])){
            if(!$this->checkMobile($request['username'])) return array('code'=>203,'msg'=>'手机号码格式有误');
            $user_new_username = encryptPhone(trim($request['username']), C('PHONE_CODE_KEY'));
            if($user_new_username!=$userInfo['username']){
                $where1['username'] = $user_new_username;
                $isusername = D('User')->getFind($where1, 'system_user_id,username');
                if(!empty($isusername)) return array('code'=>204,'msg'=>'手机号码已存在');
                $user_save['username'] = $user_new_username;
            }
        }
        //交易中
        $user_save['status'] = 70;
        //开启事务
        D()->startTrans();
        $updata_flag = D("User")->editData($user_save,$request['user_id']);
        //创建order，状态：待审核
        $order['user_id'] = $request['user_id'];
        $order['zone_id'] = $request['zone_id'];
        $order['system_user_id'] = $request['system_user_id'];
        $order['status'] = 10;
        $order['paytype'] = 1;
        $order['subscription'] = $request['subscription'];
        $order['createtime'] = time();
        $result = D("Order")->addData($order);
        if ($result['data']!==false && $updata_flag['data']!==false) {
            D()->commit();
            return array('code'=>0, 'msg'=>'订单创建成功', 'data');
        }
        D()->rollback();
        return array('code'=>101, 'msg'=>'预报订单创建失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取用户历史订单与缴费记录
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getUserOrder($where)
    {
        //参数异常
        if( empty($where['user_id']) )  return array('code'=>301, 'msg'=>'参数异常');
        $join = 'LEFT JOIN (select `system_user_id`,`realname` as `system_user_name`,`face` as `system_user_face` from zl_system_user)__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ORDER__.system_user_id';
        //获取订单列表
        $result = D('Order')->getList($where, null, 'createtime DESC', null, $join);
        //获取记录列表
        if(!empty($result)){
            //课程列表
            $courseList = D('Course','Service')->getCourseList();
            //课程列表status
            foreach($courseList['data'] as $k=>$v){
                $courseArr[$v['course_id']] = $v['coursename'];
            }
            foreach($courseList['data'] as $k=>$v){
                $courseArr[$v['course_id']] = $v['coursename'];
            }
            $studytypeArr = C('USER_STUDYTYPE');
            $loan_institutionsArr = C('USER_LOAN_INSTITUTIONS');
            $receivetypeArr = C('USER_RECEIVETYPE');
            $join2 = 'LEFT JOIN (select `system_user_id`,`realname` as `system_user_name` from zl_system_user)__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ORDER_LOGS__.auditoruser_id';
            foreach($result as $k=>$v){
                //添加类型名称
                $result[$k]['course_name'] = (empty($v['course_id']) || $v['course_id']==0)?'无':$courseArr[$v['course_id']];
                if($v['status']<40){
                    $result[$k]['studytype_name'] = '';
                }else{
                    $result[$k]['studytype_name'] = $studytypeArr[$v['studytype']]['text'];
                }
                if(empty($v['subscription'])){
                    $result[$k]['subscription'] = '0.00';
                }
                $result[$k]['loan_institutions_name'] = ($v['status']>=40 && $v['loan_institutions_id']!=0)?$loan_institutionsArr[$v['loan_institutions_id']]['text']:'无';
                $result[$k]['payway_name'] = $receivetypeArr[$v['payway']]['text'];
                $result[$k]['finish_time'] = ($v['finishtime']!=0)?date('Y-m-d H:i:s', $v['finishtime']):'';
                $result[$k]['create_time'] = date('Y-m-d H:i:s', $v['createtime']);
                //获取订单交易记录
                $logs_where['order_id'] = $v['order_id'];
                $relogs = null;
                $relogs = D('OrderLogs')->getList($logs_where, null, 'practicaltime DESC', null, $join2);
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
                    foreach($discount_ids as $v2){
                        $where1['discount_id'] = $v2;
                        $reDiscount = D('Discount')->getFind($where1);
                        if(!empty($reDiscount['data'])){
                            $result[$k]['discount_arr'][] = $reDiscount['data'];
                        }
                    }
                }
            }
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取指定订单详情
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getOrderInfo($where)
    {
        if(empty($where)){
            return array('code'=>301, 'msg'=>'参数异常');
        }
        $field = $this->_getField();
        $join = $this->_getJoin();
        $orderInfo['orderInfo'] = D("Order")->getFind($where, $field, $join);
        // if(!empty($orderInfo['orderInfo'])){
        //     $orderInfo['orderLogList'] = D("OrderLogs")->getList($where, $field, 'practicaltime desc', $limit, $join);
        // }
        return array('code'=>0, 'data'=>$orderInfo);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单缴费记录
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getOrderLogs($where, $field, $order)
    {
        return D("OrderLogs")->getList($where, $field, $order);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getOrderList($where, $order=null, $limit=null)
    {
        //参数过滤
        $where = $this->_dispostWhere($where);
        $field = $this->_getField();
        $join = $this->_getJoin();
        $result = D('Order')->getList($where, $field, $order, $limit, $join);
        if(!empty($result)) {
            //课程列表
            $courseList = D('Course', 'Service')->getCourseList();
            //课程列表status
            foreach ($courseList['data'] as $k => $v) {
                $courseArr[$v['course_id']] = $v['coursename'];
            }
            $studytypeArr = C('USER_STUDYTYPE');
            $loan_institutionsArr = C('USER_LOAN_INSTITUTIONS');
            $receivetypeArr = C('USER_RECEIVETYPE');
            $orderstatusArr = C('ORDER_STATUS');
            foreach($result as $k=>$v){
                //添加类型名称
                $result[$k]['status_name'] = $orderstatusArr[$v['status']];
                $zoneInfo = D('Zone', 'Service')->getZoneInfo($v['zone_id']);
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
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单列表总数
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getOrderCount($where)
    {
        //参数过滤
        $where = $this->_dispostWhere($where);
        $join = $this->_getJoin();
        $result = D('Order')->getCount($where,$join);
        return array('code'=>0, 'data'=>empty($result)?0:$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 根据条件判断该客户是否已有审核订单
    |--------------------------------------------------------------------------
    | user_id --- order_id
    | @author nxx
    */
    public function isAuditOrder($data)
    {
        //必选参数
        if(empty($data['user_id']) && empty($data['order_id'])){
            return array('code'=>301,'msg'=>'参数异常！');
        }
        if ($data['user_id']) {
            $data['status'] = array("IN", '10,30,40');
            $field = 'order_id';
            $result = D("Order")->getFind($data, $field);
            if(!empty($result)) {
                return array('code'=>201, 'msg'=>'该客户已有未完成订单');
            }
            return array('code'=>0, 'msg'=>'该客户无未完成订单');
        }elseif ($data['order_id']) {
            $field = 'user_id';
            $orderInfo = D("Order")->getFind($data, $field);
            if (!$orderInfo){
                return array('code'=>202, 'msg'=>'订单不存在');
            }
            $where['user_id'] = $orderInfo['user_id'];
            $where['status'] = array("IN", '30,40');
            $result = D("Order")->getFind($where, $field);
            //返回数据与状态
            if(!empty($result)) {
                return array('code'=>203, 'msg'=>'该客户已有未完成订单');
            }
            return array('code'=>0, 'msg'=>'该客户无未完成订单');
        }

    }

    /*
    |--------------------------------------------------------------------------
    | 订单审核
    |--------------------------------------------------------------------------
    | subscription payway practicaltime status
    | @author nxx
    */
    public function auditOrder($param)
    {
        if($param['type']=='ishint'){
            $isAuditOrder = $this->isAuditOrder($param);
            if($isAuditOrder['code']!=0) {
                return array('code'=>$isAuditOrder['code'], 'msg'=>$isAuditOrder['msg']);
            }
            return array('code'=>'0', 'msg'=>'审核订单时需注意检查客户名称是否有误！');
        }
        $param['system_user_id'] = $this->system_user_id;
        if (!$param['order_id']) {
            return array('code'=>301, 'msg'=>'参数信息有误');
        }
        if ($param['status'] == 'success') {
            if (!$param['payway']) {
                return array('code'=>302, 'msg'=>'收款方式不能为空');
            }
        }
        if (!$param['practicaltime']) {
            return array('code'=>303, 'msg'=>'请输入收款时间');
        }
        //添加参数
        $param['practicaltime'] = strtotime($param['practicaltime']);
        $field = "zone_id,system_user_id,user_id,order_id,status,payway,subscription,createtime";
        $where['order_id'] = $param['order_id'];
        $orderInfo = D("Order")->getFind($where, $field);
        if (!$orderInfo){
            return array('code'=>201, 'msg'=>'订单不存在');
        }
        if ($orderInfo['status']!=10){
            return array('code'=>202, 'msg'=>'订单已被审核,无法重复操作！');
        }
        //审核人
        $orderInfo['auditoruser_id'] = $param['system_user_id'];
        if ($param['status'] == 'success') {
            $orderInfo['status'] = 30;
            $orderInfo['cost'] = $orderInfo['subscription'];
            $orderInfo['auditoruser_id'] = $param['system_user_id'];
            //启动事务
            D()->startTrans();
            $flag_save = D('Order')->editData($orderInfo,$param['order_id']);
            if($orderInfo['subscription']>0){
                $addLog['zone_id'] = $orderInfo['zone_id'];
                $addLog['order_id'] = $param['order_id'];
                $addLog['paytype'] = 1;
                $addLog['payway'] = $param['payway'];
                $addLog['cost'] = $orderInfo['cost'];
                $addLog['practicaltime'] = $param['practicaltime'];
                $addLog['auditoruser_id'] = $param['system_user_id'];
                $flag_add = D("OrderLogs")->addData($addLog);
                //操作添加数据记录
                $dataLog['operattype'] = 13;
                $dataLog['operator_user_id'] = $orderInfo['system_user_id'];
                $dataLog['user_id'] = $orderInfo['user_id'];
                $dataLog['logtime'] = time();
                D('Data', 'Service')->addDataLogs($dataLog);
            }else{
                $flag_add['data'] = true;
            }

            if($flag_save['data']!==false && $flag_add['data']!==false) {
                D()->commit();
                return array('code'=>0,'msg'=>'订单审核成功');
            }
            D()->rollback();
            return array('code'=>203,'msg'=>'订单审核通过操作失败！');
        }elseif ($param['status'] == 'faile') {
            $save['status'] = 20;
            $flag_save = D("Order")->editData($save,$param['order_id']);
            if ($flag_save['data'] === false) {
                return array('code'=>204, 'msg'=>'订单审核不通过失败');
            }
            return array('code'=>0, 'msg'=>'订单审核结果为不通过');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 提交缴费
    |--------------------------------------------------------------------------
    | order_id system_user_id course_id  discount_id loan_institutions_id loan_institutions_cost? cost?
    | @author nxx
    */
    public function submitOrder($request)
    {
        $request['system_user_id'] = $this->system_user_id;
        if (!$request['order_id']) {
           return array('code'=>301, 'msg'=>'参数信息有误');
        }
        if (empty($request['course_id'])) {
           return array('code'=>302, 'msg'=>'请选择课程');
        }
        if (empty($request['studytype'])) {
           return array('code'=>303, 'msg'=>'请选择学习方式');
        }
        if (empty($request['loan_institutions_id']) && $request['loan_institutions_id']!=='0') {
           return array('code'=>304, 'msg'=>'请选择付款类型');
        }
        $field = 'status, subscription, order_id';
        $where['order_id'] = $request['order_id'];
        $orderInfo = D('Order')->getFind($where, $field);
        if(empty($orderInfo)){
            return array('code'=>201,'msg'=>'订单不存在！');
        }
        if ($orderInfo['status']!=30) {
            return array('code'=>202, 'msg'=>'订单尚未审核通过,无法操作！');
        }
        $save['status'] = 40;
        $save['course_id'] = $request['course_id'];
        $save['discount_id'] = $request['discount_id'];
        $save['studytype'] = $request['studytype'];
        //学费总额
        $where1['course_id'] = $request['course_id'];
        $coursecost = D('Course')->getFind($where1, 'price');
        $save['coursecost'] = (int)$coursecost['price'];
        //优惠金额
        $getDiscountCost = $this->_getDiscountCost($request['discount_id']);
        $save['discountcost'] = (int)$getDiscountCost;
        //实际总额
        $save['paycount'] = ((int)$save['coursecost'])-((int)$save['discountcost']);
        //贷款？
        $save['loan_institutions_id'] = !empty($request['loan_institutions_id'])?$request['loan_institutions_id']:0;
        $save['cost'] = !empty($orderInfo['subscription'])?$orderInfo['subscription']:0;
        if($request['loan_institutions_id']!=0)
        {
            $save['loan_institutions_cost'] = $request['loan_institutions_cost'];
        }
        $save['sparecost'] = ((int)$save['paycount'])-((int)$save['cost']);
        //存在实收款
        if(!empty($request['cost'])){
            $save['cost'] = ((int)$save['cost'])+((int)$request['cost']);
            $save['sparecost'] = ((int)$save['sparecost'])-((int)$request['cost']);
        }
        //启动事务
        D()->startTrans();
        $flag_save = D('Order')->editData($save, $request['order_id']);
        //添加实时收款记录？
        if(!empty($request['cost'])){
            $addLog2['order_id'] = $orderInfo['order_id'];
            $addLog2['status'] = 2;
            $addLog2['paytype'] = 1;
            $addLog2['auditoruser_id'] = $request['system_user_id'];
            $addLog2['cost'] = $request['cost'];
            $addLog2['createtime'] = time();
            $flag_add2 = D('OrderLogs')->addData($addLog2);
        }else{
            $flag_add2['data'] = true;
        }
        if($flag_save['data']!==false && $flag_add2['data']!==false) {
            D()->commit();
            return array('code'=>0,'msg'=>'提交缴费成功');
        }
        D()->rollback();
        return array('code'=>203,'msg'=>'提交缴费操作失败！');
    }

    /**
     * 获取优惠总金额
     */
    protected function _getDiscountCost($str)
    {
        if (!empty($str)) {
            $_discount_id = explode(',', $str);
            $dmoney_num = 0;
            foreach ($_discount_id as $k => $v) {
                $where['discount_id'] = $v;
                $reInfo = D('Discount')->getFind($where, 'dmoney');
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

    /*
    |--------------------------------------------------------------------------
    | 收款
    |--------------------------------------------------------------------------
    | order_id zone_id system_user_id cost payway practicaltime
    | @author nxx
    */
    public function payOrder($param)
    {

        if(empty($param['order_id'])) {
            return array('code'=>301, 'msg'=>'参数异常！');
        }
        $zone_id = $this->system_user['zone_id'];
        if ($zone_id == 6 || $zone_id == 1) {
            $zone_id = 4;
        }else{
            $zoneInfo = D('Zone', 'Service')->getZoneInfo($zone_id);
            if (empty($param['zone_id'])) {
                if ($zoneInfo['data']['centersign'] != 10) {
                    return array('code'=>201, 'msg'=>'请选择中心！');
                }
            }
        }
        if(empty($param['payway'])) {
            return array('code'=>302, 'msg'=>'请输入收款方式！');
        }
        if(empty($param['cost'])) {
            return array('code'=>303, 'msg'=>'请输入收款金额', '', 'receivables_cost');
        }
        if(empty($param['practicaltime'])) {
            return array('code'=>304, 'msg'=>'请输入收款日期！', '', 'receivables_practicaltime');
        }

        //添加参数
        $param['practicaltime'] = strtotime($param['practicaltime']);
        $param['system_user_id'] = $this->system_user_id;
        $param['zone_id'] = !empty($param['zone_id'])?$param['zone_id']:$zone_id;
        if(empty($param['order_id']) || empty($param['zone_id']) || empty($param['system_user_id'])) {
            return array('code'=>301,'msg'=>'参数异常！');
        }
        $where['order_id'] = $param['order_id'];
        $field = 'status, sparecost, cost, order_id';
        $orderInfo = D('Order')->getFind($where, $field);
        if(empty($orderInfo)) {
            return array('code'=>201,'msg'=>'订单不存在！');
        }
        if($orderInfo['status']!=40 && $orderInfo['status']!=60) {
            return array('code'=>202,'msg'=>'订单状态无法继续收款！');
        }
        if($param['cost']<0) {
            return array('code'=>203,'msg'=>'收款金额不能小于0');
        }
        if($orderInfo['status']==60) $save['status'] = 40;
        if(($orderInfo['sparecost']-$param['cost'])<=0){
            $save['status'] = 50;
            $save['finishtime'] = time();
        }
        $save['sparecost'] = ($orderInfo['sparecost']-$param['cost']);
        $save['cost'] = ((int)$orderInfo['cost'])+((int)$param['cost']);
        //启动事务
        D()->startTrans();
        $flag_save = D('Order')->editData($save, $param['order_id']);
        $addLog['order_id'] = $orderInfo['order_id'];
        $addLog['zone_id'] = $param['zone_id'];
        $addLog['paytype'] = 1;
        $addLog['payway'] = $param['payway'];
        $addLog['auditoruser_id'] = $param['system_user_id'];
        $addLog['cost'] = $param['cost'];
        $addLog['practicaltime'] = $param['practicaltime'];
        $addLog['createtime'] = time();
        $flag_add = D('OrderLogs')->addData($addLog);
        if($flag_save['data']!==false && $flag_add['data']!==false) {
            D()->commit();
            return array('code'=>0,'msg'=>'收款操作成功');
        }
        D()->rollback();
        return array('code'=>203,'msg'=>'收款操作失败！');
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
        if(empty($data['order_id']) || empty($data['zone_id']) || empty($data['system_user_id']) || empty($data['payway']) || empty($data['practicaltime'])) {
            return array('code'=>301,'msg'=>'参数异常！');
        }
        if(!empty($data['cost']) && $data['cost']<0) {
            return array('code'=>201,'msg'=>'退款金额不能小于0');
        }
        $where['order_id'] = $data['order_id'];
        $field = 'status, cost, sparecost, system_user_id, user_id';
        $orderInfo = D("Order")->getFind($where, $field);
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
                return array('code'=>202,'msg'=>'退款金额大于缴费金额');
            }
        }
        //启动事务
        D()->startTrans();
        $order['refundtime'] = time();
        //修改订单信息
        $updata = D("Order")->editData($order, $data['order_id']);
        //退款记录
        $addLog['order_id'] = $data['order_id'];
        $addLog['zone_id'] = $data['zone_id'];
        $addLog['paytype'] = 2;
        $addLog['payway'] = $data['payway'];
        $addLog['cost'] = $data['cost'];
        $addLog['auditoruser_id'] = $data['system_user_id'];
        $addLog['practicaltime'] = $data['practicaltime'];
        $addLog['createtime'] = time();
        $flag_add = D('OrderLogs')->addData($addLog);
        if($updata!==false && $flag_add!==false) {
            //操作添加数据记录
            $dataLog['operattype'] = 14;
            $dataLog['operator_user_id'] = $orderInfo['system_user_id'];
            $dataLog['user_id'] = $orderInfo['user_id'];
            $dataLog['logtime'] = time();
            D('Data', 'Service')->addDataLogs($dataLog);
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
    | @author nxx
    */
    public function getDiscountList()
    {
        $where['type'] = 1;
        $where['pid'] != 0;
        $result = D('Discount')->getList($where);
        return array('code'=>0,'data'=>$result);
    }

     /*
     |--------------------------------------------------------------------------
     | 创建优惠
     |--------------------------------------------------------------------------
     | @author nxx
     */
    public function createDiscount($request)
    {
        if (!$request['dname'] && $request['dname']>0) {
           return array('code'=>301, 'msg'=>'请填写优惠名称');
        }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$request['dname'])){
           return array('code'=>302, 'msg'=>'不能包含特殊字符');
        }
        if (strlen($request['dname'])>60) {
           return array('code'=>303, 'msg'=>'优惠名称不得超过20个字');
        }
        if (!$request['dmoney']) {
           return array('code'=>304, 'msg'=>'请填写优惠金额');
        }elseif($request['dmoney']>2000){
           return array('code'=>305, 'msg'=>'优惠金额不能大于2000');
        }
        if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$request['dmoney'])){
           return array('code'=>306, 'msg'=>"请输入正确的优惠金额");
        }
        if(!preg_match("/^-?[0-9]\d*$/",$request['nums'])){
           return array('code'=>307, 'msg'=>"优惠次数请输入整数");
        }
        if (!$request['remark']) {
           return array('code'=>308, 'msg'=>'请填写优惠详情');
        }
        if (strlen($request['remark'])>90) {
           return array('code'=>309, 'msg'=>'优惠详情不得超过30个字');
        }
        if(!$request['pid']){
           return array('code'=>310, 'msg'=>'请选择优惠所属分类');
        }
        if(!$request['typetime']){
           $request['typetime'] = 0;
        }else{
            $request['typetime'] = strtotime($request['typetime']) + 3600*24;
        }
        if (!$request['repeat']) {
            $request['repeat'] = 0;
        }else{
            $repeatList = explode(",",$request['repeat']);
        }
        $result = D("Discount")->addData($request);
        if ($result['data']) {
            if ($repeatList) {
                foreach ($repeatList as $key => $value) {
                    $where['discount_id'] = $value;
                    $discountInfo = D("Discount")->getFind($where, 'repeat');
                    $save = array();
                    if ($discountInfo['repeat']) {
                        $save['repeat'] = $discountInfo['repeat'].",$discount_id";
                    }else{
                        $save['repeat'] = $discount_id;
                    }
                    $updata = D("Discount")->editData($save, $value);
                }
            }
            return array('code'=>0, 'msg'=>'创建优惠成功', 'data'=>$result['data']);
        }else{
            return array('code'=>201, 'msg'=>'创建优惠数据失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改优惠
    |--------------------------------------------------------------------------
    | @author nxx
    */
   public function editDiscount($param, $id)
   {
        if (!$param['pid']) {
            return array(301,'请选择优惠分类');
        }
        if (!$param['dname'] && $param['dname'] > 0) {
            return array(302,'请输入优惠名称');
        }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$param['dname'])){
            return array(303,'不能包含特殊字符');
        }
        if (strlen($param['dname'])>60) {
            return array(304,'优惠名称不得超过20个字');
        }
        if (!$param['dmoney']) {
            return array(305,'请填写优惠金额');
        }elseif($param['dmoney']>2000){
            return array(306,'优惠金额不能大于2000');
        }
        if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$param['dmoney'])){
            return array(307,"请输入正确的优惠金额");
        }
        if (!$param['remark']) {
            return array(308,'请填写优惠详情');
        }
        return D('Discount')->editData($param, $id);
   }

    /*
    |--------------------------------------------------------------------------
    | 创建优惠分类
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function createParentDiscount($param)
    {
        if (!$param['pid']) {
            $param['pid'] = 0;
        }
        if (!$param['dname'] && $param['dname']>0) {
            return array(301,'请填写优惠分类名称');
        }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$param['dname'])){
            return array(302,'不能包含特殊字符');
        }
        if (strlen($param['dname'])>60) {
            return array(303,'优惠名称不得超过20个字');
        }
        if (!$param['remark']) {
            return array(304,'请填写优惠详情');
        }
        if (strlen($param['remark'])>90) {
            return array(305,'优惠详情不得超过30个字');
        }
        $result = D("DiscountParent")->addData($param);
        if ($result['code'] == 0) {
            return array('code'=>0, 'msg'=>'创建分类成功', 'data'=>$result['data']);
        }else{
            return array('code'=>201, 'msg'=>'创建失败');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改优惠分类
    |--------------------------------------------------------------------------
    | @author nxx
    */
   public function editParentDiscount($param, $id)
   {
        if (strlen($param['remark'])>90) {
            return array(305,'优惠详情不得超过30个字');
        }
        
        $save['dname'] = $param['dname'];
        $save['remark'] = $param['remark'];
        $save['type'] = $param['type'];
        if (!$save['dname'] && $save['dname']>0) {
            return array(301,'请填写优惠名称');
        }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$save['dname'])){
            return array(302,'不能包含特殊字符');
        }
        if (strlen($save['dname'])>60) {
            return array(303,'优惠名称不得超过20个字');
        }
        if (!$save['remark']) {
            return array(304,'请填写优惠详情');
        }
        return D('DiscountParent')->editData($save, $id);
   }

   /*
    |--------------------------------------------------------------------------
    | 修改优惠分类
    |--------------------------------------------------------------------------
    | @author nxx
    */
   public function editDiscountInfo($param, $id)
   {
        if (!$param['dname'] && $param['dname']>0) {
            return array(301,'请填写优惠名称');
        }elseif(!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",$param['dname'])){
            return array(302,'不能包含特殊字符');
        }
        if (strlen($param['dname'])>60) {
            return array(303,'优惠名称不得超过20个字');
        }
        if (!$param['remark']) {
            return array(304,'请填写优惠详情');
        }
        if ($param['sign'] == 10) {
            $save['dname'] = $param['dname'];
            $save['remark'] = $param['remark'];
            $save['type'] = $param['type'];
            return D('DiscountParent')->editData($save, $id);
        }else{
            if (!$param['pid']) {
                return array(305,'请选择优惠分类');
            }
            if (!$param['dmoney']) {
                return array(306,'请填写优惠金额');
            }elseif($param['dmoney']>2000){
                return array(307,'优惠金额不能大于2000');
            }
            if(!preg_match("/^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/",$param['dmoney'])){
                return array(308,"请输入正确的优惠金额");
            }
            return D('Discount')->editData($param, $id);
        }

        
   }

    /*
    |--------------------------------------------------------------------------
    | 导出订单列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function outputOrderList($orderList)
    {
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

    /*
    启用禁选优惠
    */
    public function getBan($param)
    {
        if ($param['sign'] == 10) { //分类操作
            if(!$param['discount_parent_id']){
                return array('code'=>301, 'msg'=>"参数有误");
            }
            $discountParentInfo = D("DiscountParent")->getFind(array('discount_parent_id'=>$param['discount_parent_id']));
            D("DiscountParent")->startTrans();
            if ($discountParentInfo['type'] == 1) { //如果是启用，则变成禁用
                $save['type'] = 0;
                $sons = D("Discount")->getList(array('pid'=>$discountParentInfo['discount_parent_id']));
                if ($sons) {
                    $updateSons = D("Discount")->where("pid = $discountParentInfo[discount_parent_id]")->save($save);
                }else{
                    $updateSons = true;
                }
            }else{
                $save['type'] = 1;
                $updateSons = true;
            }
            $update = D("DiscountParent")->editData($save, $param['discount_parent_id']);
            if ($update===false || $updateSons===false) {
                D("DiscountParent")->rollback();
                if ($save['type'] == 0) {
                    return array('code'=>201, 'msg'=>'下架优惠失败');
                }
                return array('code'=>202, 'msg'=>'启用优惠失败');
            }
            D("DiscountParent")->commit();
            if ($save['type'] == 0) {
                return array('code'=>0, 'msg'=>'下架优惠成功');
            }
            return array('code'=>0, 'msg'=>'启用优惠成功');
        }else{
            if(!$param['discount_id']){
                return array('code'=>302, 'msg'=>"参数有误");
            }
            $discountInfo = D("Discount")->getFind(array('discount_id'=>$param['discount_id']));
            D("Discount")->startTrans();
            if ($discountInfo['type'] == 1) { //如果是启用，则变成禁用
                $save['type'] = 0;
            }else{
                $save['type'] = 1;
            }
            $update = D("Discount")->editData($save, $param['discount_id']);
            if ($update['code'] != 0) {
                D("Discount")->rollback();
                if ($save['type'] == 0) {
                    return array('code'=>203, 'msg'=>'下架优惠失败');
                }
                return array('code'=>204, 'msg'=>'启用优惠失败');
            }
            D("Discount")->commit();
            if ($save['type'] == 0) {
                return array('code'=>0, 'msg'=>'下架优惠成功');
            }
            return array('code'=>0, 'msg'=>'启用优惠成功');
        }
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
        if(empty($data['user_id'])) return array('code'=>300,'msg'=>'参数异常！');
        $isempty = D("Order")->getFind(array('user_id'=>$data['user_id'],'status'=>array('IN','10,30,40')),'order_id');
        //返回数据与状态
        if(!empty($isempty)) {
            return array('code'=>1, 'msg'=>'该客户已有未完成订单');
        }
        return array('code'=>0, 'msg'=>'该客户无未完成订单');
    }
}
