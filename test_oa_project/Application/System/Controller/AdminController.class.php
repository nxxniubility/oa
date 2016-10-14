<?php
/*
|--------------------------------------------------------------------------
| 后台登录控制器
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：2016-04-13
| updatename：zgt
*/
namespace System\Controller;

use Common\Controller\BaseController;
use Common\Controller\SystemUserController as SystemUserMain;
use Think\Verify;
use Org\Util\Rbac;

class AdminController extends BaseController {

    public function _initialize(){
        parent::_initialize();

        //是否滴答URL
        $get_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $is_dida = 0;
        if(strstr($get_url,'crm.didazp')){
            $is_dida = 1;
        }
        $this->assign('is_dida', $is_dida);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工登录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function index()
    {
        if(IS_POST){
            //获取参数 验证
            $request = I('post.');
            if(empty($request['username'])) $this->ajaxReturn(1, '用户名不能为空', '', 'username');
            if(empty($request['password'])) $this->ajaxReturn(1, '密码不能为空', '', 'password');
            $result = D('SystemUser', 'Service')->login($request);
            if($result['code']==0){
                $this->ajaxReturn(0, '登录成功', U('System/Index/index'));
            }
            $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
        }
        $this->assign('verify', U('System/Admin/verify', array('type' => 'login')));
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 激活账号
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function  activation()
    {
        if (IS_POST) {
            //获取参数
            $request = I('post.');
            //参数验证
            $checkform = new \Org\Form\Checkform();
            $username = trim($request['username']);
            if (empty($username)) $this->ajaxReturn('1', '手机号不能为空', '', 'username');
            if(!$checkform->checkMobile($username)) $this->ajaxReturn('1', '手机号码格式有误', '', 'username');
            if (empty($request['phoneverify'])) $this->ajaxReturn('1', '验证码不能为空,请输入6位验证码', '', 'phoneverify');
            if (empty($request['password'])) $this->ajaxReturn('1', '密码不能为空,请输入密码', '', 'password');
            if (empty($request['confirmPassword'])) $this->ajaxReturn('1', '确认密码不能为空,请输入确认密码', '', 'confirmPassword');
            //激活帐号
            $systemUserMain = new SystemUserMain();
            $result = $systemUserMain->systemActivation($request);
            if($result['code']!=0)$this->ajaxReturn($result['code'], $result['msg'], '', (empty($result['sign'])?'':$result['sign']));
            //保存session
            $session_data = array(
                'zone_id'=>$result['data']['userInfo']['zone_id'],
                'system_user_id'=>$result['data']['userInfo']['system_user_id'],
                'realname'=>$result['data']['userInfo']['realname'],
                'username'=>$result['data']['userInfo']['username'],
                'face'=>$result['data']['userInfo']['face'],
                'email'=>$result['data']['userInfo']['email'],
                'sex'=>$result['data']['userInfo']['sex'],
                'usertype'=>$result['data']['userInfo']['usertype'],
                'isuserinfo'=>$result['data']['userInfo']['isuserinfo'],
                'logintime'=>$result['data']['userInfo']['logintime']
            );
            session('system_user_id',$result['data']['userInfo']['system_user_id']);
            session('system_user',$session_data);
            session('system_user_role',$result['data']['userRole']);
            //激活成功
            Rbac::saveAccessList();
            $this->success('激活成功', 0, U('System/Index/index'));
        }else{
            $this->assign('version',C('SYSTEM_VERSION'));
            $this->assign('url_login', U('System/Admin/index'));
            $this->assign('ajax_randVerifyCode', U('System/Admin/randVerifyCode'));
            $this->display();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 异常登录验证
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function loginAlarm()
    {
        if(IS_AJAX){
            $request = I('post.');
            if(empty($request['verifyCode'])) $this->ajaxReturn('1','请输入验证码！');
            $verifyCode = session('smsVerifyCode_alarm');
            if($request['verifyCode']==$verifyCode){
                $system_user = session('system_user');
                $system_user_id = session('system_user_id');
                D('SystemUserLogs')->where(array('system_user_id'=>$system_user_id,'logintime'=>$system_user['logintime']))->save(array('status'=>0));
                session('login_alarm', null);
                $this->ajaxReturn('0','验证成功！',U('/System/Index/index'));
            }else{
                $this->ajaxReturn('1','验证码错误！');
            }
        }

        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 退出登录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function logout(){
        session(null);
        cookie(null);
        session_unset();
        session_destroy();
        $this->redirect('System/Admin/index');
    }

    /**
     * 验证码生成
     * @author Sunles
     * @return mixed
     */
    public function verify(){
        $type = I('get.type','login');
        $verify = new \Think\Verify();
        $verify->length = 4;
        $verify->imageW = 150;
        $verify->imageH = 45;
        $verify->fontSize = 22;
        $verify->useCurve = false;
        $verify->entry($type);
    }


    /**
     * 生成和发送6位随机的短信验证码
     * @author cq
     */
    public function  randVerifyCode()
    {
        $systemUserModel = D('SystemUser');
        $smsType = I('post.smsType');
        //生成随机的6位验证码
        $num = rand(100000, 999999);
        $strNum = strval($num);
        session('smsVerifyCode_'.$smsType, null);

        //参数验证
        $username =  I('post.username');
        if(empty($username)){
            $this->ajaxReturn(1, '手机号不能为空', '', 'username');
        }
        //数据验证
        $userInfo = D('SystemUser')->getFind(array('username'=>encryptPhone($username,C('PHONE_CODE_KEY'))));
        if (empty($userInfo)){
            $this->ajaxReturn(2, '该OA账号未创建,请先找人事创建账号！', '', 'username');
        }
        if($smsType=='activate'){
            if (!empty($userInfo['password'])){
                $this->ajaxReturn(3, '该OA账号已激活！');
            }
        }
        $smsData = array(
            'code' => $strNum,
            'product' => '泽林信息'
        );
        //阿里大鱼短信模板
        $smspages = C('ALDAYUSMSPAGES');
        //发送短信验证码
        $result = $this->sms($username, $username, $smsData, $smspages[$smsType], '身份验证');
        if($result->code==0){
            session('smsVerifyCode_'.$smsType, $strNum);
            $this->ajaxReturn(0, '已经发送验证码,请查收','','smsverify');
        }else{
            $this->ajaxReturn(4, '发送验证码失败');
        }
    }
    
    /**
     * 无权限提示页面
     * @author Sunles
     * @return null
     */
    public function accessError(){
        echo '您暂无权限';
    }

}