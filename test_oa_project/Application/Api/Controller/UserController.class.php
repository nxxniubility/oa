<?php
/*
|--------------------------------------------------------------------------
| 所有数据相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Api\Controller;
use Common\Controller\ApiBaseController;
use Common\Service\DataService;
use Common\Service\UserService;

class UserController extends ApiBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取客户详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getUserInfo($request=null)
    {
        //外部调用？
        if($request===null){
            $data['user_id'] = I('param.user_id',null);
        }else{
            $data = $request;
        }
        //去除数组空值
        $data = array_filter($data);
        if (empty($data['user_id'])) $this->ajaxReturn(11, '客户ID不能为空');
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->getUserInfo($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '获取成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取回访记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getUserCallback($request=null)
    {
        //外部调用？
        if($request===null){
            $data['user_id'] = I('param.user_id',null);
            $data['callbackType'] = I('param.callbackType',null);
        }else{
            $data = $request;
        }
        //去除数组空值
        $data = array_filter($data);
        if (empty($data['user_id'])) $this->ajaxReturn(11, '客户ID不能为空');
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->getUserCallback($data['user_id'],$data['callbackType']);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '获取成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 获取客户短信记录
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getUserSmsLog($request=null)
    {
        //外部调用？
        if($request===null){
            $data['user_id'] = I('param.user_id',null);
        }else{
            $data = $request;
        }
        //去除数组空值
        $data = array_filter($data);
        if (empty($data['user_id'])) $this->ajaxReturn(11, '客户ID不能为空');
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->getUserSmsLog($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '获取成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUser($request=null)
    {
        //外部调用？
        if($request===null){
            $data_add['system_user_id'] = I('param.system_user_id',null);
            $data_add['infoquality'] = I('param.infoquality',null);
            $data_add['username'] = I('param.username',null);
            $data_add['qq'] = I('param.qq',null);
            $data_add['tel'] = I('param.tel',null);
            $data_add['email'] = I('param.email',null);
            $data_add['realname'] = I('param.realname',null);
            $data_add['channel_id'] = I('param.channel_id',null);
            $data_add['searchkey'] = I('param.searchkey',null);
            $data_add['interviewurl'] = I('param.interviewurl',null);
            $data_add['course_id'] = I('param.course_id',null);
            $data_add['introducermobile'] = I('param.introducermobile',null);
            $data_add['remark'] = I('param.remark',null);
        }else{
            $data_add = $request;
        }
        //去除数组空值
        $data_add = array_filter($data_add);
        if (empty($data_add['username']) && empty($data_add['tel']) && empty($data_add['qq'])) $this->ajaxReturn(11, '手机号码 / 固定电话 / QQ 至少填写一项');
        if (empty($data_add['infoquality'])) $this->ajaxReturn(11, '信息质量不能为空');
        if (empty($data_add['channel_id'])) $this->ajaxReturn(11, '所属渠道不能为空');
        if (empty($data_add['course_id']) && $data_add['course_id']!=0 || $data_add['course_id']==null) $this->ajaxReturn(11, '请选择意向课程');
        if (empty($data_add['remark'])) $this->ajaxReturn(11, '备注不能为空');
        //必要参数？ infoquality channel_id course_id remark
        if( empty($data_add['system_user_id']) || empty($data_add['infoquality']) || empty($data_add['channel_id']) || empty($data_add['course_id']) || empty($data_add['remark']) ){
            $this->ajaxReturn(1, '缺少必要参数');
        }
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->createUser($data_add);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '添加成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改用户
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editUser($request=null)
    {
        //外部调用？
        if($request===null){
            $data['user_id'] = I('param.user_id',null);
            $data['system_user_id'] = I('param.system_user_id',null);
            $data['infoquality'] = I('param.infoquality',null);
            $data['username'] = I('param.username',null);
            $data['qq'] = I('param.qq',null);
            $data['tel'] = I('param.tel',null);
            $data['email'] = I('param.email',null);
            $data['realname'] = I('param.realname',null);
            $data['channel_id'] = I('param.channel_id',null);
            $data['searchkey'] = I('param.searchkey',null);
            $data['interviewurl'] = I('param.interviewurl',null);
            $data['course_id'] = I('param.course_id',null);
            $data['introducermobile'] = I('param.introducermobile',null);
        }else{
            $data = $request;
        }
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if (!empty($data['qq']) && !$checkform->checkInt($data['qq']) && strlen($data['qq'])>=5 &&strlen($data['qq'])<=12) $this->ajaxReturn(1, 'QQ号码格式有误');
        if (!empty($data['email']) && !$checkform->isEmail($data['email'])) $this->ajaxReturn(1, '邮箱地址格式有误');
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->editUser($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '修改成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
   |--------------------------------------------------------------------------
   | 修改用户
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function editUserInfo($request=null)
    {
        //外部调用？
        if($request===null){
            $data['user_id'] = I('param.user_id',null);
            $data['system_user_id'] = I('param.system_user_id',null);
            $data['sex'] = I('param.sex',null);
            $data['birthday'] = I('param.birthday',null);
            $data['identification'] = I('param.identification',null);
            $data['homeaddress'] = I('param.homeaddress',null);
            $data['address'] = I('param.address',null);
            $data['urgentname'] = I('param.urgentname',null);
            $data['urgentmobile'] = I('param.urgentmobile',null);
            $data['postcode'] = I('param.postcode',null);
            $data['education_id'] = I('param.education_id',null);
            $data['major'] = I('param.major',null);
            $data['remark'] = I('param.remark',null);
            $data['school'] = I('param.school',null);
            $data['workyear'] = I('param.workyear',null);
            $data['lastposition'] = I('param.lastposition',null);
            $data['lastcompany'] = I('param.lastcompany',null);
            $data['lastsalary'] = I('param.lastsalary',null);
            $data['wantposition'] = I('param.wantposition',null);
            $data['wantsalary'] = I('param.wantsalary',null);
            $data['workstatus'] = I('param.workstatus',null);
            $data['englishstatus'] = I('param.englishstatus',null);
            $data['englishlevel'] = I('param.englishlevel',null);
            $data['computerlevel'] = I('param.computerlevel',null);
            $data['province_id'] = I('param.province_id',null);
            $data['city_id'] = I('param.city_id',null);
            $data['area_id'] = I('param.area_id',null);
        }else{
            $data = $request;
        }
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(!empty($data['identification']) && !$checkform->checkIdcard($data['identification'])) return array('code'=>1,'msg'=>'身份证格式格式错误','sign'=>'identification');
        if(!empty($data['email']) && !$checkform->isEmail($data['email'])) return array('code'=>1,'msg'=>'邮箱格式格式错误','sign'=>'email');
        if(!empty($data['urgentmobile']) && !$checkform->checkMobile($data['urgentmobile'])) return array('code'=>1,'msg'=>'联系人号码格式错误','sign'=>'urgentmobile');
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->editUserInfo($data);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0, '修改成功', $result);
        }
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 添加回访记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addCallback($request=null)
    {
        //外部调用？
        if($request===null){
            $data_add['system_user_id'] = I('param.system_user_id',null);
            $data_add['user_id'] = I('param.user_id',null);
            $data_add['nextvisit'] = I('param.nextvisit',null);
            $data_add['waytype'] = I('param.waytype',null);
            $data_add['attitude_id'] = I('param.attitude_id',null);
            $data_add['remark'] = I('param.remark',null);
        }else{
            $data_add = $request;
        }
        //去除数组空值
        $data_add = array_filter($data_add);
        //必要参数？ infoquality channel_id course_id remark
        if( empty($data_add['system_user_id']) || empty($data_add['user_id']) ){
            $this->ajaxReturn(1, '缺少必要参数');
        }
        //添加回访记录
        if (empty($data_add['nextvisit'])) $this->ajaxReturn(1, '回访时间不能为空', '', 'nextvisit');
        if (empty($data_add['waytype'])) $this->ajaxReturn(1, '请选择回访方式');
        if (empty($data_add['attitude_id'])) $this->ajaxReturn(1, '请选择跟进结果');
        if (empty($data_add['remark'])) $this->ajaxReturn(1, '备注不能为空');
        if (empty($data_add['nexttime'])) $this->ajaxReturn(1, '下次回访时间不能为空');
        if($data_add['nexttime']>strtotime('+15 day')) $this->ajaxReturn(1, '回访时间设置不能大于十五天', '', 'nextvisit');
        if($data_add['nexttime']<time()) $this->ajaxReturn(1, '回访时间设置不能小于当前时间', '', 'nextvisit');
        //获取接口服务层
        $UserService = new UserService();
        $reflag = $UserService->addCallback($data_add,1);
        //返回参数
        if($reflag['code']==0){
            $this->ajaxReturn(0, '添加成功');
        }
        $this->ajaxReturn(2, '添加失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 客户放弃
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因
    | @author zgt
    */
    public function abandonUser($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['nextvisit'] = I('param.nextvisit',null);
            $data_save['waytype'] = I('param.waytype',null);
            $data_save['attitude_id'] = I('param.attitude_id',null);
            $data_save['remark'] = I('param.remark',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id'])) return array('code'=>1,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,realname,infoquality,system_user_id,updateuser_id,createuser_id')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        if(empty($userList)) return array('code'=>3,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v) {
            //是否交易中
            if ($v['status'] == '70') return array('code' => 3, 'msg' => '客户' . $v['realname'] . '状态不予许放弃');
            //普通员工判断归属人
            if ($data_save['system_user_id'] != $v['system_user_id']) return array('code' => 3, 'msg' => '只有归属人才能分配该客户信息');
        }
        $UserService = new UserService();
        $redata = $UserService->abandonUser($data_save,1);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '6';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['system_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
   |--------------------------------------------------------------------------
   | 客户放弃(主管)
   |--------------------------------------------------------------------------
   | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因
   | @author zgt
   */
    public function abandonUserManage($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['nextvisit'] = I('param.nextvisit',null);
            $data_save['waytype'] = I('param.waytype',null);
            $data_save['attitude_id'] = I('param.attitude_id',null);
            $data_save['remark'] = I('param.remark',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id'])) return array('code'=>1,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,realname,infoquality,system_user_id,updateuser_id,createuser_id')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        if(empty($userList)) return array('code'=>3,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v) {
            //是否交易中
            if ($v['status'] == '70') return array('code' => 3, 'msg' => '客户' . $v['realname'] . '状态不予许放弃');
            //普通员工判断归属人
            if ($data_save['system_user_id'] != $v['system_user_id']) return array('code' => 3, 'msg' => '只有归属人才能分配该客户信息');
        }
        $UserService = new UserService();
        $redata = $UserService->abandonUser($data_save,2);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '6';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['system_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 客户转出/批量转出
    |--------------------------------------------------------------------------
    | user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人
    | @author zgt
    */
    public function allocationUser($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['tosystem_user_id'] = I('param.tosystem_user_id',null);
            $data_save['user_id'] = I('param.user_id',null);
        }else{
            $data_save = $request;
        }
        $UserService = new UserService();
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id']) || empty($data_save['tosystem_user_id'])) return array('code'=>1,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,realname,infoquality,system_user_id,updateuser_id,createuser_id')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        $systemInfo = D('SystemUser')->where(array('system_user_id'=>$data_save['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>3,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //普通员工判断归属人
            if($data_save['system_user_id']!=$v['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
            if($data_save['tosystem_user_id']==$v['system_user_id']) return array('code'=>1,'msg'=>'无法将客户转给自己哦');
            //该客户是否在申请转入审核中
            $userApply = $UserService->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        $data_save['zone_id'] = $systemInfo['zone_id'];
        $redata = $UserService->allocationUser($data_save,1);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '5';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['tosystem_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 客户转出/批量转出(主管)
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因
    | @author zgt
    */
    public function allocationUserManage($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['tosystem_user_id'] = I('param.tosystem_user_id',null);
            $data_save['user_id'] = I('param.user_id',null);
        }else{
            $data_save = $request;
        }
        $UserService = new UserService();
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id']) || empty($data_save['tosystem_user_id'])) return array('code'=>1,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,realname,infoquality,system_user_id,updateuser_id,createuser_id')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        $systemInfo = D('SystemUser')->where(array('system_user_id'=>$data_save['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>3,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //该客户是否在申请转入审核中
            $userApply = $UserService->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        $data_save['zone_id'] = $systemInfo['zone_id'];
        $redata = $UserService->allocationUser($data_save,2);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '5';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['tosystem_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 客户出库/批量出库
    |--------------------------------------------------------------------------
    |  user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人
    | @author zgt
    */
    public function restartUser($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['tosystem_user_id'] = I('param.tosystem_user_id',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['tosystem_user_id']) || empty($data_save['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        //获取客户信息与被转出人信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        $systemInfo = D('SystemUser')->where(array('system_user_id'=>$data_save['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //客户验证
        $UserService = new UserService();
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //普通员工判断归属人
            if($data_save['system_user_id']!=$v['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
            if($data_save['tosystem_user_id']==$v['system_user_id']) return array('code'=>1,'msg'=>'无法将客户转给自己哦');
            //该客户是否在申请转入审核中
            $userApply = $UserService->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        $data_save['zone_id'] = $systemInfo['zone_id'];
        $redata = $UserService->restartUser($data_save,1);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '3';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['tosystem_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 客户出库/批量出库（主管）
    |--------------------------------------------------------------------------
    |  user_id:客户 tosystem_user_id：被转员工 system_user_id：操作人
    | @author zgt
    */
    public function restartUserManage($request=null)
    {
        //外部调用？
        if($request===null){
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['tosystem_user_id'] = I('param.tosystem_user_id',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['tosystem_user_id']) || empty($data_save['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        //获取客户信息与被转出人信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data_save['user_id'])))->select();
        $systemInfo = D('SystemUser')->where(array('system_user_id'=>$data_save['tosystem_user_id']))->find();
        if(empty($userList)) return array('code'=>2,'msg'=>'查找不到客户信息');
        //客户验证
        $UserService = new UserService();
        foreach($userList as $k=>$v){
            //是否交易中
            if($v['status']=='70') return array('code'=>1,'msg'=>'客户'.$v['realname'].'状态不予许分配');
            //该客户是否在申请转入审核中
            $userApply = $UserService->isApply($v['user_id']);
            if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
        }
        $data_save['zone_id'] = $systemInfo['zone_id'];
        $redata = $UserService->restartUser($data_save,1);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '3';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['tosystem_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 赎回客户
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function redeemUser($request)
    {
        //外部调用？
        if($request===null){
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['tosystem_user_id'] = I('param.tosystem_user_id',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id'])  || empty($data_save['nexttime']) || empty($data_save['remark'])) return array('code'=>2,'msg'=>'参数异常');
        //该客户是否在申请转入审核中
        $UserService = new UserService();
        $userApply = $UserService->isApply($data_save['user_id']);
        if(!empty($userApply)) return array('code'=>1,'msg'=>'客户 正在审核转入中，无法赎回');
        //获取客户信息与被转出人信息
        $userInfo = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data_save['user_id'])))->find();
        if($data_save['system_user_id']!=$userInfo['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
        if($userInfo['status']!=160)  return array('code'=>1,'msg'=>'客户不属于回库状态,无法赎回');
        $redata = $UserService->restartUser($data_save,1);
        if($redata['code']==0){
            //添加数据记录
            $dataLog['operattype'] = '9';
            $dataLog['operator_user_id'] = $data_save['system_user_id'];
            $dataLog['system_user_id'] = $data_save['system_user_id'];
            $dataLog['user_id'] = $data_save['user_id'];
            $dataLog['logtime'] = time();
            $dataController = new DataController();
            $dataController->addDataLogs($dataLog);
        }
        return $redata;
    }

    /*
    |--------------------------------------------------------------------------
    | 确认到访
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人
    | @author zgt
    */
    public function affirmVisit($request)
    {
        //外部调用？
        if($request===null){
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['system_user_id'] = I('param.system_user_id',null);
        }else{
            $data_save = $request;
        }
        //必要参数
        if(empty($request['user_id']) || empty($request['system_user_id'])) return array('code'=>2,'msg'=>'参数异常');
        $info = D('User')->field('visittime,user_id,status,system_user_id')->where(array('user_id'=>$request['user_id']))->find();
        if($request['system_user_id']!=$info['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
        if(empty($info['visittime']) || $info['visittime']==0){
            $UserService = new UserService();
            $redata = $UserService->affirmVisit($data_save);
            if($redata['code']==0){
                //添加数据记录
                $dataLog['operattype'] = '12';
                $dataLog['operator_user_id'] = $data_save['system_user_id'];
                $dataLog['system_user_id'] = $data_save['system_user_id'];
                $dataLog['user_id'] = $data_save['user_id'];
                $dataLog['logtime'] = time();
                $dataController = new DataController();
                $dataController->addDataLogs($dataLog);
            }
            return $redata;
        }
        return array('code'=>1,'msg'=>'客户不是第一次上门');
    }


}