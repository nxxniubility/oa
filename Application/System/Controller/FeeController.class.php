<?php
namespace System\Controller;

use Common\Controller\SystemController;

class FeeController extends SystemController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *
     * author cq预报管理-客户列表
     */
    public function signing()
    {
        $request = I('get.');
        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($request['page']);
        if (!empty($request)) {
            $searchtype = trim($request['key_name']);
            $keyValue = trim($request['key_value']);
            if (!empty($keyValue) || $keyValue == '0') {
                switch ($searchtype) {
                    case 'qq':
                        $where['zl_user.qq'] = array('like', '%' . $keyValue . '%');
                        break;
                    case 'username':
                        $where['zl_user.username'] = encryptPhone($keyValue, C('PHONE_CODE_KEY'));
                        break;
                    case 'tel':
                        $where['zl_user.tel'] = array('like', '%' . $keyValue . '%');
                        break;
                    case 'realname':
                        $where['zl_user.realname'] = array('like', '%' . $keyValue . '%');
                        break;
                }
            }
            if (!empty($request['receivetype'])) {
                $where['zl_fee_logs.receivetype'] = $request['receivetype'];
            }
        }
        
        $where['zl_fee_logs.paytype'] = array('IN', array('1', '3')); //1是预报状态, 3是退费状态
        $where['zl_fee_logs.auditor_status'] = array('IN', array('10', '30'));//10是审核中, 30是通过, 20失败不显示
        $where['zl_user.status'] = array('IN', array('60', '120')); //60预报状态, 120是退费状态

        $perPageNum = C('PER_PAGE_NUM');
        $perPageNumLimit = ',' . $perPageNum;
        $limit = (($re_page - 1) * $perPageNum) . $perPageNumLimit; //分页显示,每页显示30条
        $signingUsers = D('Fee')->getAllSigningUsers($where, $limit);

        if ($signingUsers['count'] > 0) {
            foreach ($signingUsers['data'] as $k => $v) {

                //找出最新的那条记录
                $where1['user_id'] = $signingUsers['data'] [$k]['user_id'];
                $order = 'receivetime DESC';
                $newestData = D('Fee')->getFeeLog($where1, '', $order);
                //更新为最新的一条数据
                $v['fee_logs_id'] = $newestData['fee_logs_id'];
                $v['pay'] = $newestData['pay'];
                $v['paytype'] = $newestData['paytype'];
                $v['receivetype'] = $newestData['receivetype'];
                $v['receivetime'] = $newestData['receivetime'];
                $v['system_user_id'] = $newestData['system_user_id'];
                $v['feemsg'] = $newestData['feemsg'];
                $v['auditortime'] = $newestData['auditortime'];
                $v['auditor_status'] = $newestData['auditor_status'];
                //////////////////////////////////////////////////////////////////////////

                if (!empty($v['username'])) {
                    $v['username'] = decryptPhone($v['username'], C('PHONE_CODE_KEY'));
                }
                $receivetype = C('USER_RECEIVETYPE');
                if (!empty($v['receivetype'])) {
                    $type = $v['receivetype'];
                    if ($type < 1 || $type > 3) {
                        $type = 1; //付款方式不在现金、刷卡、转账这三种范围内的其它方式，默认为现金方式
                    }
                    $v['receivetypeTx'] = $receivetype[$type]['text'];
                }
                switch ($v['auditor_status']) {
                    case 10:
                        $v['describe_status'] = '等待审核';
                        break;
                    case 20:
                        $v['describe_status'] = '审核失败';
                        break;
                    case 30: //审核通过
                        if (120 == $v['userstatus']) {
                            $v['describe_status'] = '已退款';
                        } else if (60 == $v['userstatus']) {
                            $v['describe_status'] = '审核通过';
                        }
                        break;
                }
                if(empty($v['receivetime'])){
                         $v['receivetime'] = '--';
                }else{
                     $v['receivetime'] = date('Y-m-d H:i:s',$v['receivetime']);
                }
                //如果预报金额数据缺失,就从生成的缴费消息记录获取
                /*if(empty($v['pay'])) {
                    if(!empty($v['feemsg'])){
                       $payData = getNumberFromString($v['feemsg']);
                        $v['pay']  = $payData[0];
                    }
                }*/
                $signingUsers['data'][$k] = $v;
            }
        }
        //加载分页类
        $paging = $this->Paging($re_page, $perPageNum, $signingUsers['count'], $request); //分页,每页15条数据
        $this->assign('paging', $paging);
        $this->assign('request', $request);
        $this->assign('signingUsers', $signingUsers['data']);

        $receiveType = C('USER_RECEIVETYPE'); //获取支付方式
        $this->assign('receiveType', $receiveType);

        $this->display();
    }

    /**生成缴费消息记录
     * @param $feeType  缴费类型  1-是预报缴费    2-缴费   3-退费
     * @param $feeLog 缴费记录
     * @return string  缴费字符串
     * @author cq
     */
    public function  makeFeeMsg($feeType,$feeLog)
    {
        $where['user_id'] = $feeLog['user_id'];
        $userData = D('User')->getUser($where);
        $realName = $userData['realname'];

        if (1 == $feeType) {
            $feeMsg  =  $realName.':'.'已支付预报名费用'.$feeLog['pay'].'.00元整。';

        }elseif (2 == $feeType) {
            $feeMsg  =  $realName.':'.'已缴费用'.$feeLog['pay'].'.00元整。';
        }
        elseif (3 == $feeType) {
            $feeMsg  =  $realName.':'.'已退款金额'.$feeLog['pay'].'.00元整。';
        }
        return $feeMsg;
    }

    /**
     * 确认预报申请
     * @author cq
     */
    public function signingPay()
    {
        $fee_logs_id = I('post.feeLogId');
        if (empty($fee_logs_id)) $this->ajaxReturn(1, '缺省参数,用户记录Id不能为空');
        $where['fee_logs_id'] = $fee_logs_id;
        $feeLog = D('Fee')->getFeeLog($where);

        $data['system_user_id'] = $this->system_user_id; //审核人/收退款人
        $data['auditortime'] = time();
        $userStatus = C('USER_APPLY_STATUS');
        $data['auditor_status'] = $userStatus['30']['num'];//审核通过
        if(empty($feeLog['feemsg'])) {
            $data['feemsg'] = $this->makeFeeMsg(1,$feeLog); //生成缴费消息记录
        }
        D('Fee')->startFeeTrans();
        $result = D('Fee')->updateFeeLog($where, $data); //更新记录信息
        $userdata['reservetype'] = $userStatus['30']['num']; //修改reservetype为审核成功状态
        $userdata['updatetime'] = time(); //更新时间
        $where1['user_id'] = $feeLog['user_id'];
        $userResult = D('Fee')->UpdateUserInfo($where1, $userdata);

        //记录fee表中的预报金额
        $feeData['user_id'] = $feeLog['user_id']; //客户ID
        $feeData['paymemttime'] = time(); //实际缴纳预报费的时间
        $feeData['paycount'] = $feeLog['pay']; //实际预报金额

        $result2 = D('Fee')->addFeeRecord($feeData);
        if (!$result || !$result2 || !$userResult) {
            D('Fee')->rollbackFeeTrans();
            $this->ajaxReturn(1, '确认收款失败');
        } else {
            D('Fee')->commitFeeTrans();
            $this->ajaxReturn(0, '确认收款成功', U('System/Fee/signing'));
        }

    }

    /**
     * 退回预报申请
     * @author cq
     */
    public function signingFailure()
    {
        $fee_logs_id = I('post.feeLogId');
        if (empty($fee_logs_id)) $this->ajaxReturn(1, '缺省参数,缴费记录ID不能为空');
        $where['fee_logs_id'] = $fee_logs_id;
        $feeLog = D('Fee')->getFeeLog($where);

        $data['system_user_id'] = $this->system_user_id; //审核人/收退款人
        $data['auditortime'] = time();
        $userStatus = C('USER_APPLY_STATUS');
        $data['auditor_status'] = $userStatus['20']['num'];//审核失败

        D('Fee')->startFeeTrans();
        $result = D('Fee')->updateFeeLog($where, $data); //更新记录信息

        $where1['user_id'] = $feeLog['user_id'];
        $userdata['reservetype'] = $userStatus['20']['num']; //修改reservetype为审核失败状态
        $userdata['status'] = 30;//退回待跟进状态
        $userdata['updatetime'] = time(); //更新时间
        $userResult = D('Fee')->UpdateUserInfo($where1, $userdata);

        if (!$result || !$userResult) {
            D('Fee')->rollbackFeeTrans();
            $this->ajaxReturn(1, '退回申请失败');
        } else {
            D('Fee')->commitFeeTrans();
            $this->ajaxReturn(0, '退回申请成功', U('System/Fee/signing'));
        }
    }

    /**
     * 预报退款
     * @author cq
     */
    public function signingRefund()
    {
        $fee_logs_id = I('post.feeLogId');
        $pay = I('post.pay'); //退款金额
        $receivetype = I('post.receivetype'); //退款方式

        if (empty($fee_logs_id)) $this->ajaxReturn(1, '缺省参数,记录ID不能为空');
        if (empty($receivetype)) $this->ajaxReturn(1, '缺省参数,请选择用户退款方式');
        if (empty($pay)) $this->ajaxReturn(1, '缺省参数,用户退款金额不能为空');

        $where['fee_logs_id'] = $fee_logs_id;
        $feeData = D('Fee')->getFeeLog($where);
        $forecastPay = $feeData['pay'];
        if ($pay > $forecastPay) {
            $this->ajaxReturn(1, '出错了，退款金额不能大于预报金额');
        }
        $user_apply_status = C('USER_APPLY_STATUS');
        $feeLog['user_id'] = $feeData['user_id'];
        $feeLog['pay'] = $pay; //退费金额
        $feeLog['paytype'] = 3; //退费
        $feeLog['receivetype'] = $receivetype;
        $feeLog['receivetime'] = time();
        $feeLog['system_user_id'] = $this->system_user_id; //退款人
        $feeLog['auditortime'] = time();
        $feeLog['auditor_status'] = $user_apply_status['30']['num']; //审核通过
        $feeLog['feemsg'] = $this->makeFeeMsg(3,$feeData); //生成缴费消息记录

        D('Fee')->startFeeTrans();
//        $result1 = D('Fee')->updateFeeLog($where,$feeLog); //更新预报记录为退费
        $result1 = D('Fee')->addFeeLog($feeLog); //新增退费记录

        $userStatus = C('USER_STATUS');
        $userdata['status'] = $userStatus['120']['num']; //退费状态
        $userdata['updatetime'] = time(); //更新时间
        $where1['user_id'] = $feeLog['user_id'];
        $userResult = D('Fee')->UpdateUserInfo($where1, $userdata);

        $data['paycount'] = 0; //预报金额清0
        $data['pay_status'] = 3; //修改为已退费状态
        $result2 = D('Fee')->UpdateFee($where1, $data);

        if (!$result1 || !$userResult || !$result2) {
            D('Fee')->rollbackFeeTrans();
            $this->ajaxReturn(1, '预报退款失败');
        } else {
            D('Fee')->commitFeeTrans();
            $this->ajaxReturn(0, '预报退款成功', U('System/Fee/signing'));
        }
    }

    /***
     * 缴费管理-客户列表
     * @author cq
     */
    public function pay()
    {
        $request = I('get.');
        $re_page = isset($request['page']) ? $request['page'] : 1;
        unset($request['page']);
        if (!empty($request)) {
            $searchtype = trim($request['key_name']);
            $keyValue = trim($request['key_value']);
            $checkform = new \Org\Form\Checkform();
            if (!empty($keyValue)) {
                switch ($searchtype) {
                    case 'qq':
                        $where['zl_user.qq'] = array('like', '%' . $keyValue . '%');
                        break;
                    case 'username':
                        $where['zl_user.username'] = encryptPhone($keyValue, C('PHONE_CODE_KEY'));
                        break;
                    case 'tel':
                        $where['zl_user.tel'] = array('like', '%' . $keyValue . '%');
                        break;
                    case 'realname':
                        $where['zl_user.realname'] = array('like', '%' . $keyValue . '%');
                        break;
                }
            } else {
                if (!empty($request['receivetype'])) {
                    $where['receivetype'] = $request['receivetype'];
                }
                if (!empty($request['pay_status'])) {
                    $where['pay_status'] = $request['pay_status'];
                }
            }
        }
        $perPageNum = C('PER_PAGE_NUM');
        $where['zl_user.status'] = array('IN', array('80', '120'));  //80是缴费状态, 120是退费状态
        $where['zl_fee_logs.paytype'] = array('IN', array('1', '2', '3')); //1是预报并且提交资料后 2是缴费状态, 3是退费状态
        $where['zl_fee_logs.auditor_status'] = 30;  //30是审核通过状态, 处于缴费中的全部是审核通过状态
        $where['zl_fee.pay_status'] = array('IN', array('1', '2', '3'));//在缴费状态下, 1-欠费中, 2-缴费完成, 3-已经退费

        $perPageNum = C('PER_PAGE_NUM');
        $perPageNumLimit = ',' . $perPageNum;
        $limit = (($re_page - 1) * $perPageNum) . $perPageNumLimit; //分页显示,每页显示30条

        $payUsers = D('Fee')->getAllPayUsers($where, $limit);
        if ($payUsers['count'] > 0) {
            foreach ($payUsers['data'] as $k => $v) {
                if (!empty($payUsers['data'][$k]['username'])) {
                    $payUsers['data'][$k]['username'] = decryptPhone($v['username'], C('PHONE_CODE_KEY')); //解码电话号码
                }
                if ($payUsers['data'][$k]['loan_institutions_id'] != 0) {
                    $payUsers['data'][$k]['is_loan'] = '是';

                    switch ($payUsers['data'][$k]['loan_institutions_id']) {
                        case 1:
                            $payUsers['data'][$k]['loan_org'] = '宜信';
                            break;
                        case 20:
                            $payUsers['data'][$k]['loan_org'] = '玖富';
                            break;
                        default:
                            $payUsers['data'][$k]['loan_org'] = '其它';
                            break;
                    }
                } else {
                    $payUsers['data'][$k]['is_loan'] = '否';
                    if ($payUsers['data'][$k]['delay'] != 0) {
                        $payUsers['data'][$k]['delayTx'] = $payUsers['data'][$k]['delay'] . '个月';
                    } else {
                        $payUsers['data'][$k]['delayTx'] = '不延期';
                    }
                    if ($payUsers['data'][$k]['instalments'] != 0) {
                        $payUsers['data'][$k]['instalmentsTx'] = '分' . $payUsers['data'][$k]['instalments'] . '期';
                    } else {
                        $payUsers['data'][$k]['instalmentsTx'] = '不分期';
                    }
                }

                if (empty($payUsers['data'][$k]['arrearage'])) {
                    $payUsers['data'][$k]['arrearage'] = ''; //设置为空串
                }
                if (!empty($payUsers['data'][$k]['pay_status'])) {

                    switch ($payUsers['data'][$k]['pay_status']) {
                        case 1:
                            $payUsers['data'][$k]['pay_status'] = '欠费中';
                            break;
                        case 2:
                            $payUsers['data'][$k]['pay_status'] = '缴费完成';
                            break;
                        case 3:
                            $payUsers['data'][$k]['pay_status'] = '已退费';
                            break;
                    }
                }
            }
        }

        //加载分页类
        $paging = $this->Paging($re_page, $perPageNum, $payUsers['count'], $request); //分页,每页15条数据
        $this->assign('request', $request);
        $this->assign('paging', $paging);
        $this->assign('payUsers', $payUsers['data']);
        $receiveType = C('USER_RECEIVETYPE'); //获取支付方式
        $this->assign('receiveType', $receiveType);

        $this->display();
    }

    /**
     * 缴费管理--确认收款
     * @author cq
     */
    public function paySure()
    {
        $fee_id = I('post.fee_id');
        $pay = I('post.pay'); //退款金额
        $receivetype = I('post.receivetype'); //退款方式

        if (empty($fee_id)) $this->ajaxReturn(1, '缺省参数,缴费记录ID不能为空');
        if (empty($receivetype)) $this->ajaxReturn(1, '缺省参数,请选择用户付款方式');
        if (empty($pay)) $this->ajaxReturn(1, '缺省参数,用户缴费金额不能为空');

        $where['fee_id'] = $fee_id;
        $feeData = D('Fee')->getFee($where);

        if ($pay > $feeData['arrearage']) {  //缴费金额大于欠费金额,不合理

            $this->ajaxReturn(1, '缴费金额不正确,不能大于欠费金额.');
        }
        $user_status = C('USER_APPLY_STATUS');
        $feeLog['user_id'] = $feeData['user_id'];
        $feeLog['pay'] = $pay;
        $feeLog['paytype'] = 2; //2是缴费
        $feeLog['receivetype'] = $receivetype; //收款方式
        $feeLog['receivetime'] = time();
        $feeLog['system_user_id'] = $this->system_user_id;
        $feeLog['feemsg'] = $this->makeFeeMsg(2,$feeLog);
        $feeLog['auditortime'] = time();
        $feeLog['auditor_status'] = $user_status['30']['num'];
        D('Fee')->startFeeTrans();
        $result1 = D('Fee')->addFeeLog($feeLog); //新增缴费记录

        $feeData['paycount'] = $feeData['paycount'] + $feeLog['pay']; //实际缴费金额
        $feeData['arrearage'] = $feeData['arrearage'] - $feeLog['pay']; //欠费总额
        if ($feeData['arrearage'] < 0) {
            $this->ajaxReturn(1, '缴费金额不正确,不能大于余额,请重新确认');
        }
        if ($feeData['arrearage'] == 0) {
            $data['pay_status'] = 2; //缴费完成
        }else{
            $data['pay_status'] = 1; //欠费中
        }
        $where['fee_id'] = $feeData['fee_id'];
        $data['paycount'] = $feeData['paycount'];
        $data['arrearage'] = $feeData['arrearage'];
        $result2 = D('Fee')->UpdateFee($where, $data);

        if (!$result1 || !$result2) {
            D('Fee')->rollbackFeeTrans();
            $this->ajaxReturn(1, '确认缴费失败');

        } else {
            D('Fee')->commitFeeTrans();
            $this->ajaxReturn(0, '确认缴费成功', U('System/Fee/pay'));
        }
    }

    /*
     * 缴费管理--退款
     * @author cq
     */
    public function  payRefund()
    {

        $fee_id = I('post.fee_id');
        $pay = I('post.pay'); //退款金额
        $receivetype = I('post.receivetype'); //退款方式

        if (empty($fee_id)) $this->ajaxReturn(1, '缺省参数,缴费记录ID不能为空');
        if (empty($receivetype)) $this->ajaxReturn(1, '缺省参数,请选择用户退款方式');
        if (empty($pay)) $this->ajaxReturn(1, '缺省参数,用户退款金额不能为空');

        $where['fee_id'] = $fee_id;
        $feeData = D('Fee')->getFee($where);

        if ($pay > $feeData['paycount']) {
            $this->ajaxReturn(1, '退款金额错误,不能大于实际缴费金额');
        }

        $user_apply_status = C('USER_APPLY_STATUS');
        $feeLog['user_id'] = $feeData['user_id'];
        $feeLog['pay'] = $pay;
        $feeLog['paytype'] = 3; //退费
        $feeLog['receivetype'] = $receivetype;
        $feeLog['receivetime'] = time();
        $feeLog['system_user_id'] = $this->system_user_id;
        $feeLog['auditortime'] = time();
        $feeLog['auditor_status'] = $user_apply_status['30']['num']; //审核通过

        D('Fee')->startFeeTrans();
        $result1 = D('Fee')->addFeeLog($feeLog); //添加退费记录

        $userStatus = C('USER_STATUS');
        $userdata['status'] = $userStatus['120']['num']; //退费状态120
        $userdata['updatetime'] = time(); //更新时间

        $where1['user_id'] = $feeData['user_id'];
        $userResult = D('Fee')->UpdateUserInfo($where1, $userdata);

        //退款后结算金额处理
        $feeData['paycount'] = $feeData['paycount'] - $feeLog['pay']; //退费后的剩余金额
        $feeData['arrearage'] = 0; //欠费总额清空

        $data['paycount'] = $feeData['paycount'];
        $data['arrearage'] = $feeData['arrearage'];
        $data['pay_status'] = 3; //修改为已退费状态

        $result2 = D('Fee')->UpdateFee($where, $data);

        if (!$result1 || !$result2 || !$userResult) {
            D('Fee')->rollbackFeeTrans();
            $this->ajaxReturn(1, '退款失败');
        } else {
            D('Fee')->commitFeeTrans();
            $this->ajaxReturn(0, '退款成功', U('System/Fee/pay'));
        }
    }

}
