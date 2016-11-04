<?php

/**
 * 接口基类
 * @author luoyu
 */

namespace Common\Controller;
use Common\Controller\ApiBaseController;

class SystemApiController extends ApiBaseController {

    protected $system_user_id;
    protected $system_user;
    public function _initialize()
    {
        parent::_initialize();
        if(!session(C('USER_AUTH_KEY'))){
            $this->ajaxReturn(501, '请重新登录');
        }
        $this->system_user_id = session('system_user_id');
        $this->system_user = session('system_user');
    }


}
