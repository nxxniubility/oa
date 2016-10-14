<?php
/*
|--------------------------------------------------------------------------
| 职位管理表（原用户权限组role）
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：2016-04-18
| updatename：zgt
*/
namespace Common\Model;

class FeeModel extends SystemModel
{

    protected $feeDb;  //收费表, 针对每个学员的费用
    protected $feelogDb; //收费记录表, 记录每次学员的缴费记录
    protected $userDb; //客户表
    protected $tranDb; // 事务

    public function _initialize()
    {
        $this->feeDb = M('fee');
        $this->feelogDb = M('fee_logs');
        $this->userDb = M('User');
        $this->tranDb = M();
    }

    /**
     * 启动事务
     * @author cq
     */
    public function  startFeeTrans()
    {

        $this->tranDb->startTrans();
    }

    /**
     * 提交事务
     * @author cq
     */
    public function  commitFeeTrans()
    {

        $this->tranDb->commit();
    }

    /**
     * 回滚事务
     * @author cq
     */
    public function  rollbackFeeTrans()
    {

        $this->tranDb->rollback();
    }


    /**
     * 获取单个缴费记录
     * @param string $where
     * @param string $field
     * @author cq
     * @return \Think\mixed
     */
    public function getFeeLog($where = "", $field = "*",$order)
    {
        return $this->feelogDb->field($field)->where($where)->order($order)->find();
    }

    /**
     * 获取指定人的所有缴费记录
     * @param string $where
     * @param string $field
     * @param string $order
     * @author cq
     * @return \Think\mixed
     */
    public function getAllFeeLog($where = "", $field = "*", $order = 'receivetime DESC')
    {
        return $this->feelogDb->field($field)->where($where)->order($order)->select();
    }


    /* 更新缴费记录
     * @author cq
     * @param string $where
     * @param $data
     */

    public function  updateFeeLog($where, $data)
    {
        return $this->tranDb->table('zl_fee_logs')->where($where)->save($data);

    }

    /* 新增缴费记录
      * @author cq
      * @param string $where
      * @param $data
      */
    public function  addFeeLog($data)
    {
        return $this->tranDb->table('zl_fee_logs')->data($data)->add();
    }

    /* 新增缴费
    * @author cq
    * @param string $where
    * @param $data
    */
    public function  addFeeRecord($feeData)
    {
        $where['user_id'] = $feeData['user_id'];
        $result= $this->tranDb->table('zl_fee')->where($where)->find();

        if(!empty($result)){ //更新记录
            $newData['paymemttime'] = $feeData['paymemttime'];
            $newData['paycount'] = $feeData['paycount'];
            $this->tranDb->table('zl_fee')->where($where)->save($newData);
        }else{ //创建一条记录
            $result =  $this->tranDb->table('zl_fee')->data($feeData)->add();
        }
        return $result;
    }

    /**
     * 获取客户的费用档案
     * @param string $where
     * @param string $field
     * @author cq
     * @return \Think\mixed
     */
    public function getFee($where = "", $field = "*")
    {
        return $this->feeDb->field($field)->where($where)->find();
    }

    /**
     * 获取所有客户的费用档案--列表
     * @param string $where
     * @param string $field
     * @author cq
     * @return \Think\mixed
     */
    public function getAllFee($where = "", $field = "*")
    {
        return $this->feeDb->field($field)->where($where)->order('fee_id DESC')->select();
    }

    /**更新缴费档案
     * @param string $where
     * @param $data
     * @author cq
     * @return mixed
     */
    public function UpdateFee($where = "", $data)
    {
        return $this->tranDb->table('zl_fee')->where($where)->save($data);
    }

    /**
     * 更新相应的客户信息
     * @param $where
     * @param $data
     * @return mixed
     * @author cq
     */
    public function  UpdateUserInfo($where, $data)
    {
        return $this->tranDb->table('zl_user')->where($where)->save($data);
    }

    /**
     * 获取所有申请预报的客户信息
     * @param $where
     * @param $limit
     * @author cq
     */
    public function  getAllSigningUsers($where, $limit)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.reservetype",
            "{$DB_PREFIX}user.status as userstatus",
            "{$DB_PREFIX}fee_logs.fee_logs_id",
            "{$DB_PREFIX}fee_logs.pay",
            "{$DB_PREFIX}fee_logs.fee_logs_id",
            "{$DB_PREFIX}fee_logs.receivetype",
            "{$DB_PREFIX}fee_logs.receivetime",
            "{$DB_PREFIX}fee_logs.auditor_status",
            "{$DB_PREFIX}fee_logs.paytype",
            "{$DB_PREFIX}fee_logs.auditortime",
            "{$DB_PREFIX}fee_logs.feemsg",
            "{$DB_PREFIX}fee.pay_status",
            "{$DB_PREFIX}system_user.system_user_id as apply_system_user_id",
            "{$DB_PREFIX}system_user.realname as apply_realname",
        );
        $signingUsers['data'] = $this->feelogDb->field($field)
            ->join('LEFT JOIN  __USER__ ON  __USER__.user_id = __FEE_LOGS__.user_id')
            ->join('LEFT JOIN  __FEE__ ON  __FEE__.user_id = __FEE_LOGS__.user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->where($where)
            ->group("{$DB_PREFIX}user.user_id")
            ->limit($limit)
            ->order('receivetime DESC,auditor_status ASC')
            ->select();
        $data  = $this->feelogDb
            ->join('LEFT JOIN  __USER__ ON  __USER__.user_id = __FEE_LOGS__.user_id')
            ->join('LEFT JOIN  __FEE__ ON  __FEE__.user_id = __FEE_LOGS__.user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->where($where)
            ->group("{$DB_PREFIX}user.user_id")->select();
            $signingUsers['count'] = count($data);

        return $signingUsers;
    }


    /**
     * 获取所有缴费客户信息
     * @author cq
     */
    public function  getAllPayUsers($where, $limit)
    {

        $DB_PREFIX = C('DB_PREFIX');
        $field = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.status as userstatus",
            "{$DB_PREFIX}fee_logs.paytype",  //2是缴费状态, 3是退费状态
            "{$DB_PREFIX}fee_logs.receivetype",
            "{$DB_PREFIX}fee.fee_id",
            "{$DB_PREFIX}fee.coursecount", //学费总额
            "{$DB_PREFIX}fee.discount_cost",//优惠金额
            "{$DB_PREFIX}fee.paycount",//实际缴费总额
            "{$DB_PREFIX}fee.arrearage",//欠款总额
            "{$DB_PREFIX}fee.loan_institutions_id",//'贷款机构，0-不贷款    1-宜信  2-玖富'
            "{$DB_PREFIX}fee.delay",//'延期：0-不延期 1/2/3/4/...月'
            "{$DB_PREFIX}fee.instalments",//'是否分期:0-不分期  其他数字为分期期数',
            "{$DB_PREFIX}fee.pay_status", //缴费状态:  1.欠费中 2.缴费完成  3.已退费,
            "{$DB_PREFIX}system_user.system_user_id as apply_system_user_id",
            "{$DB_PREFIX}system_user.realname as apply_realname",
        );
        $payUsers['data'] = $this->userDb->field($field)
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __FEE_LOGS__ ON  __FEE_LOGS__.user_id = __USER__.user_id')
            ->join('LEFT JOIN  __FEE__ ON  __FEE__.user_id = __USER__.user_id')
            ->where($where)
            ->group("{$DB_PREFIX}user.user_id")
            ->limit($limit)
            ->order('zl_user.updatetime DESC')
            ->select();
        $data = $this->userDb->field($field)
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __FEE_LOGS__ ON  __FEE_LOGS__.user_id = __USER__.user_id')
            ->join('LEFT JOIN  __FEE__ ON  __FEE__.user_id = __USER__.user_id')
            ->where($where)
            ->group("{$DB_PREFIX}user.user_id")
            ->select();
             $payUsers['count'] = count($data);
        return $payUsers;

    }


}
