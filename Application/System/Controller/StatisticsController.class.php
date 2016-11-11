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
    | 营销统计入口页
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function marketIndex()
    {
        // $request = I('get.');
        // //默认时间
        // if(empty($request['startime'])){
        //     $request['startime'] = date('Y/m/d', strtotime('-7 day'));
        // }
        // if(empty($request['endtime'])){
        //     $request['endtime'] = date('Y/m/d');
        // }
        // $where['daytime'] = $request['startime'].','.$request['endtime'];
        // $where['zone_id'] = $request['zone_id']?$request['zone_id']:$this->system_user['zone_id'];
        // $where['role_id'] = $request['role_id'];
        // $data['my_zone_id'] = $this->system_user['zone_id'];

        // $this->assign('data', $data);
        // $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 营销统计
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function market()
    {
        $request = I('get.');
        //默认时间
        if(empty($request['startime'])){
            $request['startime'] = date('Y/m/d', strtotime('-7 day'));
        }
        if(empty($request['endtime'])){
            $request['endtime'] = date('Y/m/d');
        }
        $where['daytime'] = $request['startime'].','.$request['endtime'];
        $where['zone_id'] = $request['zone_id']?$request['zone_id']:$this->system_user['zone_id'];
        $where['role_id'] = $request['role_id'];
        $data['my_zone_id'] = $this->system_user['zone_id'];

        $this->assign('data', $data);
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
        //实例化
        $DataService = new DataService();
        $request = I('get.');
        if (empty($request['system_user_id'])) {
            $request['system_user_id'] = $this->system_user_id;
        }
        //默认时间
        if(empty($request['startime'])){
            $request['startime'] = date('Y/m/d', strtotime('-7 day'));
        }
        if(empty($request['endtime'])){
            $request['endtime'] = date('Y/m/d');
        }
        $where['system_user_id'] = $request['system_user_id'];
        $where['daytime'] = $request['startime'].','.$request['endtime'];
        $result = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$where['system_user_id']));
        $systemUserInfo = $result['data'];
        $result = $DataService->getDataMarket($where);
        $dataMarket = $result['data'];
        $dataMarket['daytime'] = $where['daytime'];
        $request['daytime'] = $request['startime'].','.$request['endtime'];
        $data['request'] = $request;
        $this->assign('dataMarket', $dataMarket);
        $this->assign('systemUserInfo', $systemUserInfo);
        $this->assign('data', $data);
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