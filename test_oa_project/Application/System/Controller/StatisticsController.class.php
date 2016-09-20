<?php
namespace System\Controller;

use Api\Controller\DataController;
use Common\Controller\SystemController;
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
        $DataController = new DataController();
        $where['daytime'] = '20160919-20160920';
        $data['request'] = $where;
        //获取接口数据
        $data_market = $DataController->getDataMarket($where);
        $ZoneService = new ZoneService();
        $zone_list = $ZoneService->getZoneList(1);
        $data['dataMarket'] = $data_market['data'];
        $data['zoneList'] = $zone_list['data'];
        $this->assign('data', $data);
        $this->display();
    }
}