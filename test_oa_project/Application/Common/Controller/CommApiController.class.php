<?php

/**
 * 接口基类
 * @author luoyu
 */

namespace Common\Controller;
use Common\Controller\ApiBaseController;

class CommApiController extends ApiBaseController {
   
    public function _initialize() {
		parent::_initialize();
     		
    }
	protected function checkUserLogin(){
        if(!$this->isLogin("zp_user_id"))
		{
			$this->out(400,"您还没有登录！",U('Home/User/login',array(),true,true));
		}
    }
    
  
	
}
