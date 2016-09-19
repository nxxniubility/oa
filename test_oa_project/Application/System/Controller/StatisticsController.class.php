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
        //实例化
        $DataController = new DataController();
        $where['daytime'] = '20160918-20160919';
        if(IS_POST){
            $request = I('post.');
            $where['type'] = $request['type'];
            //获取接口数据
            $dataMarketInfo = $DataController->getDataMarketInfo($where);
            dump($dataMarketInfo);
        }
        //获取接口数据
        $dataMarket = $DataController->getDataMarket($where);
        $data['dataMarket'] = $dataMarket['data'];
        $this->assign('data', $data);
        $this->display();
    }
}