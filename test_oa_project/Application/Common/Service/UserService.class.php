<?php
/*
* 客户服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class UserService extends BaseService
{
    //初始化
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function createUser($data)
    {
        $time = time();
        $data['allocationtime'] = $time;
        $data['updatetime'] = $time;
        $data['lastvisit'] = $time;
        $data['updatetime'] = $time;
        $data['createtime'] = $time;
        $data['createip'] = get_client_ip();
        $data['updateuser_id'] = $data['system_user_id'];
        $data['createuser_id'] = $data['system_user_id'];
        //启动事务
        D()->startTrans();
        $reUserId = D('User')->data($data)->add();
        if(!empty($reUserId)){
            $data_info = $data;
            $data_info['user_id'] = $reUserId;
            $reUserInfo = D('UserInfo')->data($data_info)->add();
        }
        //添加分配记录
//        $logs = $this->allocationLogs($data, $data['system_user_id']);
        if(!empty($reUserId) && !empty($reUserInfo) && !empty($logs)){
            //添加数据统计
            $dataMarket['system_user_id'] = $data['system_user_id'];
            $dataMarket['name'] = 'addnum';
            $dataMarket['user_id'] = $reUserId;
            $DataService = new DataService();
            $DataService->addDataMarket($dataMarket);
            //添加数据记录
            $dataLog['operattype'] = '1';
            $dataLog['user_id'] = $reUserId;
            $dataLog['system_user_id'] = $data['system_user_id'];
            $dataLog['updateuser_id'] = $data['system_user_id'];
            $dataLog['createuser_id'] = $data['system_user_id'];
            $dataLog['operator_user_id'] = $data['system_user_id'];
            $dataLog['zone_id'] = $data['zone_id'];
            $dataLog['channel_id'] = $data['channel_id'];
            $dataLog['infoquality'] = $data['infoquality'];
            $dataLog['logtime'] = $time;
            $DataService = new DataService();
            $DataService->addDataLogs($dataLog);
            D()->commit();
            return $reUserId;
        }else{
            D()->rollback();
            return $reUserId;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 添加回访记录
    |--------------------------------------------------------------------------
    | user_id system_user_id
    | @author zgt
    */
    public function addCallback($data,$rank=1)
    {
        //数据添加
        $user = D('User')->field('user_id,status')->where(array('user_id'=>array('IN', $data['user_id'])))->select();
        if(empty($user)) return false;
        //启动事务
        D()->startTrans();
        foreach($user as $k=>$v){
            $data_user['attitude_id'] = $data['attitude_id'];
            $data_user['nextvisit'] = $data['nexttime'];
            $data_user['callbacktype'] = $data['callbacktype'];
            if($rank==1){
                //更新客户状态
                if($v['status']==20){
                    $data_user['status'] = 30;
                }
                $data['callbacktime'] = time();
                $data_user['callbacknum'] = array('exp','callbacknum+1');
                $data_user['lastvisit'] = $data['callbacktime'];
            }else{
                $data['callbacktime'] = !empty($data['nexttime'])?$data['nexttime']:time();
                $data_user['lastvisit'] = $data['callbacktime'];
            }
            $reflag_save = D('User')->where(array('user_id'=>$v['user_id']))->save($data_user);
            if($reflag_save===false) return false;
            //获取新增数据集合
            $add_callback[$k] = $data;
            $add_callback[$k]['user_id'] = $v['user_id'];
        }
        //批量新增回访
        $reflag = D('UserCallback')->addAll($add_callback);
        if($reflag!==false && $reflag_save!==false){
            D()->commit();
            return true;
        }else{
            D()->rollback();
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 客户放弃/回库
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function abandonUser($data, $rank=1)
    {

        //数据更新
        D()->startTrans();
        $save_user['status'] = 160;
        $where['user_id'] = array('IN',$data['user_id']);
        $result = D('User')->where($where)->save($save_user);
        if($result!==false){
            //添加回访记录
            $data_callback['status'] = 0;
            $data_callback['user_id'] = $data['user_id'];
            $data_callback['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $data_callback['system_user_id'] = $data['system_user_id'];
            $data_callback['nexttime'] = time();
            if($rank==2){
                //批量
                if(count($where['user_id'])>1){
                    $data_callback['remark'] = '批量客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 15;
                }else{
                    $data_callback['remark'] = '客户回库(管理操作):'.$data['remark'];
                    $data_callback['callbacktype'] = 14;
                }
                //添加统计
                $dataMarket['system_user_id'] = $data['system_user_id'];
                $dataMarket['name'] = 'recyclenum';
                $dataMarket['user_id'] = $data['user_id'];
                $dataController = new DataController();
                $dataController->addDataMarket($dataMarket);
            }else{
                $data_callback['remark'] = '客户放弃：'.$data['remark'];
                $data_callback['callbacktype'] = 2;
                //添加统计
                $dataMarket['system_user_id'] = $data['system_user_id'];
                $dataMarket['name'] = 'restartnum';
                $dataMarket['user_id'] = $data['user_id'];
                $dataController = new DataController();
                $dataController->addDataMarket($dataMarket);
            }
            $this->addCallback($data_callback,2);
            D()->commit();
            return array('code'=>0,'msg'=>'操作成功');
        }
        D()->rollback();
        return array('code'=>1,'msg'=>'操作失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 客户转出/批量转出（分配）
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function allocationUser($data, $rank=1)
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 客户出库/批量出库（分配）
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function restartUser($data, $rank=1)
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 赎回客户
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function redeemUser($data)
    {

    }

    /*
    |--------------------------------------------------------------------------
    | 确认到访
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function affirmVisit($data)
    {

    }
}