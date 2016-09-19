<?php
namespace System\Controller;

use Api\Controller\DataController;
use Common\Controller\SystemController;
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
        $DataController = new DataController();
        $where = array(
            'daytime'=>'20160918-20160919'
        );
        $dataMarket = $DataController->getDataMarket($where);
        $data['dataMarket'] = $dataMarket['data'];
        print_r($data['dataMarket']);
        $this->assign('data', $data);
        $this->display();
    }
}