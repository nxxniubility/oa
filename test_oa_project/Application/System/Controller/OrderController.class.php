<?php
namespace System\Controller;
use Common\Controller\SystemController;
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
                    $where['createtime'] = array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1].'23:59:59')));
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
                $res = D('Order', 'Service')->getOrderCount($where);
                //加载分页类
                $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $res['data'], $requestG, __ACTION__, null, 'system');
                $this->ajaxReturn(0, '', $paging_data);
            }else if($requestP['type']=='getSysUser'){
                //异步获取员工列表
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_ids'] = (!empty($requestP['role_id']))?$requestP['role_id']:0;
                //员工列表
                $reSystemList = D('SystemUser', 'Service')->getSystemUsersList($whereSystem);
                //返回数据操作状态
                if ($reSystemList['data']['data']){
                    $this->ajaxReturn(0, '', $reSystemList['data']['data']);
                }else {
                    $this->ajaxReturn(201, '员工列表加载失败!');
                }
            }
        }
        $limit = (empty($requestG['page'])?'0':($requestG['page']-1)*30).',30';
        //获取数据
        $result = D('Order', 'Service')->getOrderList($where, 'createtime DESC', $limit);
        //获取区域下
        $res1 = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll']['children'] = $res1['data'];
        $centersign = 10;
        $res2 = D('Zone', 'Service')->getZoneCenter($centersign);
        $centerList = $res2['data'];
        //获取职位及部门
        $department = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll']=$department['data'];
        $roleList = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleList['data'];
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
        $request['order_id'] = I('get.order_id');
        if(IS_POST){
            $res = I('post.');
            $res['order_id'] = $request['order_id'];
            $result = D('Order', 'Service')->submitOrder($res);
            if($result['code']==0){
                $this->ajaxReturn($result['code'], $result['msg'], U('System/Order/orderList'));
            }
            $this->ajaxReturn($result['code'], $result['msg']);
        }
        $orderInfo = D('Order', 'Service')->getOrderInfo($request);
        $data['info'] = $orderInfo['data'];
        //课程列表
        $courseList = D('Course', 'Service')->getCourseProductList();
        $data['courseList'] = $courseList['data']['data'];
        //获取区域下
        $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll']['children'] = $zoneList['data'];
        $centersign = 10;
        $centerList = D('Zone', 'Service')->getZoneCenter($centersign);
        $centerList = $centerList['data'];
        //优惠方式
        $discountList = D('Order', 'Service')->getDiscountList();
        $data['discount'] = $discountList['data'];
        foreach ($data['discount'] as $key => $value) {
            if ($value['nums'] == 0) {
                $data['discount'][$key]['nums'] = "不限";
            }else{
                $data['discount'][$key]['nums'] = $value['nums'] - $value['usednums'];
            }
            if ($value['typetime'] != 0) {
                $data['discount'][$key]['typetime'] = date('Y-m-d H:i:s', $value['typetime']);
            }else{
                $data['discount'][$key]['typetime'] = "长期有效";
            }
        }
        //获取配置状态值
        $data['order_loan_institutions'] = C('USER_LOAN_INSTITUTIONS');
        $data['order_studytype'] = C('USER_STUDYTYPE');
        $data['order_receivetype'] = C('USER_RECEIVETYPE');
        //模版赋值
        $data['order_id'] = $request['order_id'];
        $this->assign('centerList', $centerList);
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
        $result = D('Order', 'Service')->auditOrder($request);
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
        //获取接口
        $result = D('Order', 'Service')->payOrder($request);
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
        if(empty($request['order_id'])) {
            $this->ajaxReturn(301, '参数信息有误');
        }
        if(empty($request['practicaltime'])) {
            $this->ajaxReturn(302, '请输入退款日期！');
        }
        $where['order_id'] = $request['order_id'];
        $field = 'practicaltime';
        $order = 'practicaltime desc';
        $orderLog = D('Order', 'Service')->getOrderLogs($where, $field, $order);
        $request['practicaltime'] = strtotime($request['practicaltime']);
        if ($request['practicaltime'] < $orderLog['data']['practicaltime']) {
            $this->ajaxReturn(305,"退款日期不得早于上次缴费时间");
        }
        if(empty($request['payway'])) {
            $this->ajaxReturn(303, '退款方式！');
        }
        if(empty($request['type']) || $request['type']!='deposit'){
            if(empty($request['cost'])) $this->ajaxReturn(304, '退款金额！');
        }
        //添加参数
        $request['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
        //获取接口
        $result = D('Order', 'Service')->refundOrder($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 导出订单
     * nxx
     */
    public function outputOrder()
    {
        set_time_limit(600);
        $system_user_id = $this->system_user_id;
        if (IS_POST) {
            $requestP = I('post.');
            if($requestP['type']=='getSysUser'){
                $whereSystem['usertype'] = array('neq',10);
                $whereSystem['zone_id'] = !empty($requestP['zone_id'])?$requestP['zone_id']:$this->system_user['zone_id'];
                $whereSystem['role_ids'] = $requestP['role_id'];
                //员工列表
                $systemUserAll = D('SystemUser', 'Service')->getSystemUsersList($whereSystem);
                if($systemUserAll['code'] == 0) {
                    $this->ajaxReturn(0, '', $systemUserAll['data']['data']);
                }else {
                    $this->ajaxReturn($systemUserAll['code'], $systemUserAll['msg']);
                }
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
                if (!$requestP['zone_id']) {
                    $requestP['zone_id'] = $this->system_user['zone_id'];
                }
                unset($requestP['system_user_id']);
                $result = D('Order', 'Service')->getOrderList($requestP, '', '0,1000');
                if($result['code'] != 0){
                    $this->ajaxReturn($result['code'],'暂无相关订单数据可供导出!');
                }
                return D('Order', 'Service')->outputOrderList($result['data']);
            }

        }
        //获取区域下
        $zoneList = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll']['children'] = $zoneList['data'];
        //获取职位及部门
        $department = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll']=$department['data'];
        $roleList = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleList['data'];
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
            if ($value['nums'] == 0) {
                $value['nums'] = "不限";
            }else{
                $value['nums'] = $value['nums'] - $value['usednums'];
            }
            if ($value['typetime'] != 0) {
                $value['typetime'] = date('Y-m-d H:i:s', $value['typetime']);
            }else{
                 $value['typetime'] = "长期有效";
            }
            if ($value['type'] != 1) {
                $value['typetime'] = "已下架";
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
            $result = D('Order', 'Service')->createDiscount($request);
            $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
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
            $result = D('Order', 'Service')->createParentDiscount($request);
            $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
        }
    }

    /*
    *修改优惠---优惠分类
    *@author nxx
    */
    public function editDiscount()
    {
        if (IS_POST) {
            $request = I("post.");
            if ($request['sign'] == 10) {
                $discount_parent_id = $request['discount_parent_id'];
                $update = D('Order', 'Service')->editParentDiscount($request, $discount_parent_id);
                $this->ajaxReturn($update['code'], $update['msg']);
            }else{
                $discount_id = $request['discount_id'];
                unset($request['discount_id']);
                $update = D('Order', 'Service')->editDiscount($request, $discount_id);
                $this->ajaxReturn($update['code'],$update['msg']);
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
        $result = D('Order', 'Service')->getBan($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }



}
