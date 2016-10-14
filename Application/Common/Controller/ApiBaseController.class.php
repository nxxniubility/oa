<?php

/**
 * 接口基类
 * @author luoyu
 */

namespace Common\Controller;
use Think\Controller;

class ApiBaseController extends BaseController {

    public function _initialize() {
        //分页处理
        $var_page = C('VAR_PAGE');
        if ($_GET[$var_page]) {
            $_GET[$var_page] = intval($_GET[$var_page]) + 1;
        } else {
            $_GET[$var_page] = 1;
        }
        $this->assign($var_page, $_GET[$var_page]);

    }
	protected function out($code=0,$msg="",$data=array())
	{
		$out = array(
			'code' => $code,
			'msg'  => $msg,
			'data' => $data ? : array(),
			'token'=> session_id()
			);
		exit(json_encode($out));
	}
	protected function checkSystemLogin(){
        if(!$this->isLogin("system_user_id"))
		{
			$this->out(400,"您还没有登录！",U('System/Admin/login',array(),true,true));
		}
    }
}
