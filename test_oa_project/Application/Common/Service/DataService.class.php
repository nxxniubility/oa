<?php
/*
* 数据服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class DataService extends BaseService
{
    //初始化
    public function _initialize() {
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取数据记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataLogs($where)
    {
        return D('DataLogs')->where($where)->select();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataLogs($data)
    {
        if(empty($data['logtime']))  $data['logtime'] = time();
        return D('DataLogs')->add($data);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getDataMarket($where)
    {
        return D('DataMarket')->where($where)->select();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加营销数据
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addDataMarket($where)
    {
        $data_where['daytime'] = strtotime(date('Y-m-d'));
        $data_where['system_user_id'] = $where['system_user_id'];
        $systemdata = D('DataMarket')->where($data_where)->find();
        if(empty($systemdata)){
            D('DataMarket')->add($data_where);
        }
        $dataMarket_arr = explode(',',$where['user_id']);
        $field = $where['name'];
        $exp = !empty($where['exp'])?$where['exp']:'+';   // + -
        $num = count($dataMarket_arr);
        $data_save[$field] = array('exp', $field.$exp.$num);
        $flag_save = D('DataMarket')->where($data_where)->save($data_save);
        return $flag_save;
    }
}