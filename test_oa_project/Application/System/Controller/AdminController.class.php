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
   | 框架跳出登录界面
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function reLogin()
    {
        $this->display();
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
            $result = D('SystemUser', 'Service')->activation($request);
            if($result['code']==0){
                $this->ajaxReturn(0, '激活成功', U('System/Index/index'));
            }
            $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
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
            $result = D('SystemUser', 'Service')->loginAlarm($request);
            if($result['code']==0){
                $this->ajaxReturn(0, '验证成功', U('System/Index/index'));
            }
            $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
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
     * @author zgt
     */
    public function  randVerifyCode()
    {
        //参数验证
        $request =  I('post.');
        $result = D('SystemUser', 'Service')->activationSms($request);
        if($result['code']==0){
            $this->ajaxReturn(0, '短信发送成功');
        }
        $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
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