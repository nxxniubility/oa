<?php
/*
|--------------------------------------------------------------------------
| 订单相关的数据接口
|--------------------------------------------------------------------------
| @author nxx
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemOrderService;

class OrderController extends SystemApiController
{

    /*
    |--------------------------------------------------------------------------
    | 创建订单
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function createOrder()
    {
        //获取请求？
        $param['username'] = I('param.username',null);
        $param['subscription'] = I('param.subscription',null);
        $param['user_id'] = I('param.user_id',null);
        $param['realname'] = I('param.realname',null);
        $param['system_user_id'] = $this->system_user_id;
        $param['zone_id'] = $this->system_user['zone_id'];
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Order','Service')->createOrder($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 订单列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function orderList()
    {
        //获取请求
        $param['status'] = I('param.status',null);
        $param['loan_institutions_id'] = I('param.loan_institutions_id',null);
        $param['createtime'] = I('param.createtime',null);
        $param['finishtime'] = I('param.finishtime',null);
        $param['role_id'] = I('param.role_id',null);
        $param['zone_id'] = I('param.zone_id',null);
        $param['system_user_id'] = I('param.system_user_id',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Order','Service')->getOrderList($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
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
        //获取请求
        $param['order_id'] = I('param.order_id',null);
        $param['course_id'] = I('param.course_id',null);
        $param['discount_id'] = I('param.discount_id',null);
        $param['loan_institutions_cost'] = I('param.loan_institutions_cost',null);
        $param['loan_institutions_id'] = I('param.loan_institutions_id',null);
        $param['studytype'] = I('param.studytype',null);
        //去除数组空值
        $param = array_filter($param);
        //获取接口服务层
        $result = D('Order','Service')->submitOrder($param);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);

    }


    /**
     * 审核订单
     * subscription practicaltime status
     * @author nxx
     */
    public function auditingOrder()
    {
        $param['order_id'] = I('param.order_id',null);
        $param['payway'] = I('param.payway',null);
        $param['practicaltime'] = I('param.practicaltime',null);
        $param['status'] = I('param.status',null);
        $result = D('Order', 'Service')->auditOrder($param);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 收款操作
     * @author zgt
     */
    public function payfund()
    {
        //获取参数
        $param['order_id'] = I('param.order_id',null);
        $param['payway'] = I('param.payway',null);
        $param['practicaltime'] = I('param.practicaltime',null);
        $param['cost'] = I('param.cost',null);
        $param['zone_id'] = I('param.zone_id',null);
        //获取接口
        $result = D('Order', 'Service')->payOrder($param);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 退费操作
     * cost order_id payway
     * @author nxx
     */
    public function refund()
    {
        //获取参数
        $param['order_id'] = I('param.order_id',null);
        $param['payway'] = I('param.payway',null);
        $param['practicaltime'] = I('param.practicaltime',null);
        $param['cost'] = I('param.cost',null);
        $param['zone_id'] = I('param.zone_id',null);
        //获取接口
        $result = D('Order', 'Service')->payOrder($param);
        $this->ajaxReturn($result['code'], $result['msg']);
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
        $param['status'] = I('param.status',null);
        $param['loan_institutions_id'] = I('param.loan_institutions_id',null);
        $param['createtime'] = I('param.createtime',null);
        $param['finishtime'] = I('param.finishtime',null);
        $param['role_id'] = I('param.role_id',null);
        $param['zone_id'] = I('param.zone_id',null);
        $param['system_user_id'] = I('param.system_user_id',null);
        //去除数组空值
        $param = array_filter($param);
        $result = D('Order', 'Service')->getOrderList($param, '', '0,1000');
        if($result['code'] != 0){
            $this->ajaxReturn($result['code'],'暂无相关订单数据可供导出!');
        }
        return D('Order', 'Service')->outputOrderList($result['data']);



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
        $param['dmoney'] = I('param.dmoney',null);
        $param['dname'] = I('param.dname',null);
        $param['pid'] = I('param.pid',null);
        $param['remark'] = I('param.remark',null);
        $param['repeat'] = I('param.repeat',null);
        $param['type'] = I('param.type',null);
        $param = array_filter($param);
        $result = D('Order', 'Service')->createDiscount($param);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'],$result['msg']);
        }
        $this->ajaxReturn(0, $result['data']);
    }

    /*
    *添加优惠分类
    *@author nxx
    */
    public function addParentDiscount()
    {
        $param['dname'] = I('param.dname',null);
        $param['remark'] = I('param.remark',null);
        $param['type'] = I('param.type',null);
        $param = array_filter($param);
        $result = D('Order', 'Service')->createDiscount($param);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'],$result['msg']);
        }
        $this->ajaxReturn(0, $result['data']);
    }

    /*
    *修改优惠---优惠分类
    *@author nxx
    */
    public function editDiscount()
    {
        $param['dmoney'] = I('param.dmoney',null);
        $param['dname'] = I('param.dname',null);
        $param['pid'] = I('param.pid',null);
        $param['remark'] = I('param.remark',null);
        $param['repeat'] = I('param.repeat',null);
        $param['type'] = I('param.type',null);
        $param['sign'] = I('param.sign',null);
        $param['discount_id'] = I('param.discount_id',null);
        $param['discount_parent_id'] = I('param.discount_parent_id',null);
        $param = array_filter($param);
        $result = D('Order', 'Service')->editDiscountInfo($param, $param['discount_id']);
       
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'],$result['msg']);
        }
        $this->ajaxReturn(0, $result['data']);
    }

    /*
    *启用、禁用优惠
    *@author nxx
    */
    public function banDiscount()
    {
        $param['discount_parent_id'] = I('param.discount_parent_id',null);
        $param['discount_id'] = I('param.discount_id',null);
        $param['sign'] = I('param.sign',null);
        $result = D('Order', 'Service')->getBan($request);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

}
