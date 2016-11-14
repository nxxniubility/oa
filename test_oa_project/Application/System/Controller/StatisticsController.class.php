<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Service\DataService;
use Common\Service\SystemUserService;
use Common\Service\DepartmentService;
use Common\Service\RoleService;
use Common\Service\ZoneService;

class StatisticsController extends SystemController
{

    /*
    |--------------------------------------------------------------------------
    | 营销统计
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function market()
    {
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 我的营销统计
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function mymarket()
    {
        $request = I('get.');
        if (empty($request['system_user_id'])) {
            $request['system_user_id'] = $this->system_user_id;
        }
        $where['system_user_id'] = $request['system_user_id'];
        $result = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$where['system_user_id']));
        $systemUserInfo = $result['data'];
        if(empty($request['role_id'])) {
            redirect(__SELF__.'&role_id='.$systemUserInfo['roles'][0]['id']);
        }
        $this->assign('request', $request);
        $this->assign('systemUserInfo', $systemUserInfo);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 营销统计-设置合格标准列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function marketStandard()
    {
        //实例化
        $DataService = new DataService();
        $result = $DataService->getStandard();
        $data['list'] = $result['data'];
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 营销统计-添加合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addStandard()
    {
        //获取职位及部门
        $DepartmentService = new DepartmentService();
        $department_list = $DepartmentService->getList();
        $data['departmentList'] = $department_list['data'];
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 营销统计-修改合格标准
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editStandard()
    {
        $standard_id = I('get.standard_id');
        //获取职位及部门
        $DepartmentService = new DepartmentService();
        $department_list = $DepartmentService->getList();
        $data['departmentList'] = $department_list['data'];
        $DataService = new DataService();
        $reInfo = $DataService->getStandardInfo(array('standard_id'=>$standard_id));
        $data['info'] = $reInfo['data'];
        $data['standard_id'] = $standard_id;
        $this->assign('data', $data);
        $this->display();
    }
}