<?php
namespace System\Controller;

use Api\Controller\DataController;
use Common\Controller\SystemController;
use Common\Service\DataService;
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
        //实例化
        $DataService = new DataService();
        $request = I('get.');
        if(empty($request['startime'])){
            $request['startime'] = date('Y/m/d', strtotime('-7 day'));
        }
        if(empty($request['endtime'])){
            $request['endtime'] = date('Y/m/d');
        }
        $where['daytime'] = $request['startime'].'-'.$request['endtime'];
        $data['request'] = $where;
        //获取接口数据
        $data_market = $DataService->getDataMarket($where);
        $data['dataMarket'] = $data_market['data'];
        //获取职位及部门
        $DepartmentService = new DepartmentService();
        $department_list = $DepartmentService->getList();
        $data['departmentList'] = $department_list['data'];
        $RoleService = new RoleService();
        $role_list = $RoleService->getAllRole();
        $data['roleList'] = $role_list['data'];
//        $ZoneService = new ZoneService();
//        $zone_list = $ZoneService->getZoneList(1);
//        $data['zoneList'] = $zone_list['data'];
        $this->assign('data', $data);
        $this->display();
    }
}