<?php
namespace Common\Controller;
use Common\Controller\SystemUserController;
use Common\Controller\BaseController;
use Org\Util\Rbac;

class ApiBaseController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
    }
}