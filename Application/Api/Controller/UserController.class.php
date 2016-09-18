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
    | 添加用户
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addUser($data=null)
    {
        //外部调用？
        if($data===null){
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
            $data_add = $data;
        }
        //去除数组空值
        $data_add = array_filter($data_add);
        //必要参数？ infoquality channel_id course_id remark
        if( empty($data_add['system_user_id']) || empty($data_add['infoquality']) || empty($data_add['channel_id']) || empty($data_add['course_id']) || empty($data_add['remark']) ){
            $this->ajaxReturn(1, '缺少必要参数');
        }
        $systemInfo = D('SystemUser')->field('zone_id,realname')->where(array('system_user_id'=>$data_add['system_user_id']))->find();
        if(!empty($systemInfo)){
            $data_add['zone_id'] = $systemInfo['zone_id'];
        }
        //验证唯一字段 数据处理
        $checkData = $this->checkField($data_add);
        if($checkData['code']!=0) return array('code'=>$checkData['code'], 'msg'=>$checkData['msg'], 'sign'=>!empty($checkData['sign'])?$checkData['sign']:null);
        $data_add = $checkData['data'];
        //是否获取新渠道
        $newChannelData = $this->isNewChannel($data_add);
        if($newChannelData['code']!=0) return array('code'=>$newChannelData['code'], 'msg'=>$newChannelData['msg'], 'sign'=>!empty($newChannelData['sign'])?$newChannelData['sign']:null);
        $data_add = $newChannelData['data'];
        //获取接口服务层
        $UserService = new UserService();
        $result = $UserService->createUser($data_add);
        //返回参数
        if($result!==false){
            $this->ajaxReturn(0, '添加成功', $result);
        }
        $this->ajaxReturn(2, '添加失败', $result);
    }


    /*
    |--------------------------------------------------------------------------
    | 添加回访记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addCallback($data=null)
    {
        //外部调用？
        if($data===null){
            $data_add['system_user_id'] = I('param.system_user_id',null);
            $data_add['user_id'] = I('param.user_id',null);
            $data_add['nextvisit'] = I('param.nextvisit',null);
            $data_add['waytype'] = I('param.waytype',null);
            $data_add['attitude_id'] = I('param.attitude_id',null);
            $data_add['remark'] = I('param.remark',null);
        }else{
            $data_add = $data;
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
        if($data_add['nexttime']>strtotime('+15 day')) $this->ajaxReturn(1, '回访时间设置不能大于十五天', '', 'nextvisit');
        if($data_add['nexttime']<time()) $this->ajaxReturn(1, '回访时间设置不能小于当前时间', '', 'nextvisit');
        //获取接口服务层
        $UserService = new UserService();
        $reflag = $UserService->addCallback($data_add,1);
        //返回参数
        if($reflag!==false){
            $this->ajaxReturn(0, '添加成功');
        }
        $this->ajaxReturn(2, '添加失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 客户放弃
    |--------------------------------------------------------------------------
    | user_id:客户 system_user_id：操作人  attitude_id：放弃 remark：放弃原因 $rank：操作等级（1：普通员工，2：主管）
    | @author zgt
    */
    public function abandonUser($data=null)
    {
        //外部调用？
        if($data===null){
            $data_save['system_user_id'] = I('param.system_user_id',null);
            $data_save['user_id'] = I('param.user_id',null);
            $data_save['nextvisit'] = I('param.nextvisit',null);
            $data_save['waytype'] = I('param.waytype',null);
            $data_save['attitude_id'] = I('param.attitude_id',null);
            $data_save['remark'] = I('param.remark',null);
        }else{
            $data_save = $data;
        }
        //必要参数
        if(empty($data_save['user_id']) || empty($data_save['system_user_id'])) return array('code'=>1,'msg'=>'参数异常');
        //获取客户信息
        $userList = D('User')->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$data['user_id'])))->select();
        if(empty($userList)) return array('code'=>3,'msg'=>'查找不到客户信息');
        //客户验证
        foreach($userList as $k=>$v) {
            //是否交易中
            if ($v['status'] == '70') return array('code' => 3, 'msg' => '客户' . $v['realname'] . '状态不予许放弃');
            //普通员工判断归属人
            if ($data_save['system_user_id'] != $v['system_user_id']) return array('code' => 3, 'msg' => '只有归属人才能分配该客户信息');
        }
    }



    /*
    * 参数处理 QQ username tel introducermobile interviewurl
    * @author zgt
    * @return false
    */
    protected function checkField($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(!empty($data['user_id'])){
            $user = D('User')->where(array('user_id'=>$data['user_id']))->find();
        }
        //验证手机号码是否有修改
        if(!empty($data['username'])){
            if( !empty($user) && $user['username']==encryptPhone($data['username'], C('PHONE_CODE_KEY')) ){
                unset($data['username']);
            }else{
                $data['username'] = trim($data['username']);
                $username = $data['username'];
                if(!$checkform->checkMobile($data['username'])) return array('code'=>11,'msg'=>'手机号码格式有误','sign'=>'username');
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                $isusername = D('User')->where(array('username'=>array(array('eq',$data['username']),array('eq',$username0),'OR')))->find();
                if(!empty($isusername)) return array('code'=>11,'msg'=>'手机号码已存在');
                $reApi = phoneVest($username);
                if(!empty($reApi)) {
                    $data['phonevest'] = $reApi['city'];
                }else{
                    $data['phonevest'] = '';
                }
            }
        }
        //验证固定电话是否有修改
        if(!empty($data['tel'])){
            if( !empty($user) && $user['tel']==$data['tel'] ) {
                unset($data['tel']);
            }else{
                $data['tel'] = trim($data['tel']);
                if (!$checkform->checkTel($data['tel'])) return array('code' => 11, 'msg' => '固定号码格式有误', 'sign' => 'tel');
                $istel = D('User')->where(array('tel' => $data['tel']))->find();
                if (!empty($istel)) return array('code' => 11, 'msg' => '固定电话已存在');
            }
        }
        //验证QQ号码是否有修改
        if(!empty($data['qq'])){
            if( !empty($user) && $user['qq']==$data['qq'] ) {
                unset($data['qq']);
            }else{
                $data['qq'] = trim($data['qq']);
                if (!$checkform->checkInt($data['qq'])) return array('code' => 11, 'msg' => 'qq格式有误', 'sign' => 'qq');
                $isqq = D('User')->where(array('qq' => $data['qq']))->find();
                if (!empty($isqq)) return array('code' => 11, 'msg' => 'qq号码已存在');
                if (empty($data['email']) && !empty($user['email'])) $data['email'] = $data['qq'] . '@qq.com';
            }
        }

        return array('code'=>0,'data'=>$data);
    }

    /**
     * 是否获取新渠道
     * @author zgt
     */
    public function isNewChannel($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        //转介绍人获取渠道
        if(!empty($data['introducermobile'])) {
            if( !empty($user) && $user['introducermobile']==$data['introducermobile'] ) {
                unset($data['introducermobile']);
            }else{
                if($checkform->checkMobile($data['introducermobile'])!==false) $data['introducermobile'] = encryptPhone($data['introducermobile'], C('PHONE_CODE_KEY'));
                else  return array('code'=>12,'msg'=>'转介绍人手机号码格式有误','sign'=>'introducermobile');
                $introducer = D('User')->where(array('username'=>$data['introducermobile']))->find();
                if(!empty($introducer['channel_id'])) $data['channel_id'] = $introducer['channel_id'];
            }
        }
        //通过咨询地址获取 渠道与推广ID
        if(!empty($data['interviewurl'])){
            if( !empty($user) && $user['interviewurl']==$data['interviewurl'] ) {
                unset($data['interviewurl']);
            }else{
                $valueUrl = $data['interviewurl'];
                preg_match("/promote[=|\/]([0-9]*)/", $valueUrl, $promote);
                if(!empty($promote[1])){
                    $promoteInfo = D('Promote')
                        ->field('channel_id')
                        ->where(array('promote_id'=>$promote[1]))
                        ->join("__PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
                        ->find();
                    if(!empty($promoteInfo['channel_id'])){
                        $data['channel_id'] = $promoteInfo['channel_id'];
                        $data['promote_id'] = $promote[1];
                    }
                }
            }
        }
        return array('code'=>0,'data'=>$data);
    }
}