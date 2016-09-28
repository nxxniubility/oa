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
        //实例化
        $DataService = new DataService();
        $request = I('get.');
        //默认时间
        if(empty($request['startime'])){
            $request['startime'] = date('Y/m/d', strtotime('-7 day'));
        }
        if(empty($request['endtime'])){
            $request['endtime'] = date('Y/m/d');
        }
        $where['daytime'] = $request['startime'].','.$request['endtime'];
        $where['zone_id'] = $request['zone_id'];
        $where['role_id'] = $request['role_id'];
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
        $ZoneService = new ZoneService();
        $zone_list = $ZoneService->getZoneList(1);
        $data['zoneList'] = $zone_list['data'];
        $request['daytime'] = $request['startime'].','.$request['endtime'];
        $data['request'] = $request;
        $standardList = $DataService->getStandard();
        foreach ($standardList['data'] as $key => $standard) {
            $infos = $DataService->getStandardInfos(array('standard_id'=>$standard['standard_id']));
            $standard['role_id'] = explode(',', $standard['role_id']);
            $infos['data']['role_ids'] = $standard['role_id'];
            $standardInfos[$key] = $infos['data'];
        }
        foreach ($data['dataMarket']['systemuser'] as $k1 => $v1) {
            foreach ($standardInfos as $k2 => $v2) {
                if (in_array($v1['role_id'], $v2['role_ids'])) {
                    foreach ($v2 as $k3 => $v3) {
                        if ($v3['option_name'] == 'addcount') {
                            if (($v1['addcount']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redaddcount'] = 1;
                                
                            }elseif (($v1['addcount']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redaddcount'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'restartcount') {
                            if (($v1['restartcount']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redrestartcount'] = 1;
                                
                            }elseif (($v1['restartcount']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redrestartcount'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'visitcount') {
                            if (($v1['visitcount']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redvisitcount'] = 1;
                            }elseif (($v1['visitcount']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redvisitcount'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'ordercount') {
                            if (($v1['ordercount']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redordercount'] = 1;
                            }elseif (($v1['ordercount']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redordercount'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'refundcount') {
                            if (($v1['refundcount']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redrefundcount'] = 1;
                            }elseif (($v1['refundcount']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redrefundcount'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'visitratio') {
                            if (($v1['visitratio']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redvisitratio'] = 1;
                            }elseif (($v1['visitratio']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redvisitratio'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'conversionratio') {
                            if (($v1['conversionratio']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redconversionratio'] = 1;
                            }elseif (($v1['conversionratio']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redconversionratio'] = 1;
                            }
                        }
                        if ($v3['option_name'] == 'totalratio') {
                            if (($v1['totalratio']>$v3['option_num']) && ($v3['option_warn']==2)) {
                                $v1['redtotalratio'] = 1;
                            }elseif (($v1['totalratio']<$v3['option_num']) && ($v3['option_warn']==1)) {
                                $v1['redtotalratio'] = 1;
                            }
                        }
                        $data['dataMarket']['systemuser'][$k1] = $v1;
                    }
                }
            }
        }
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
        $SystemUser = new SystemUserService();
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
        $result = $SystemUser->getListCache(array('system_user_id'=>$where['system_user_id']));
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