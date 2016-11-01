<?php

/**
 * 接口基类
 * @author luoyu
 */

namespace Common\Controller;
use Common\Controller\ApiBaseController;

class SystemApiController extends ApiBaseController {

    public function _initialize()
    {
        parent::_initialize();
        if(!session(C('USER_AUTH_KEY'))){
            $this->ajaxReturn(501, '请重新登录');
        }
    }


}
