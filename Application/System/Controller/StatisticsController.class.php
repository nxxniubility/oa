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
            daytime
        );
        $redata = $DataController->getDataMarket();
        dump($redata);
    }
}