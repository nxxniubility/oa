<?php
namespace Common\Model;

use Common\Model\SystemModel;

class OrderModel extends SystemModel
{
    /*
    |--------------------------------------------------------------------------
    | 获取订单列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $order='createime DESC', $limit='0,30', $field='*', $join=null){
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取订单总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null,$join=null){
        return $this->join($join)->where($where)->count();
    }


    /*
    * 预报审核通过
    * subscription createtime
    * @author zgt
    */
    public function updataOrder($data)
    {
        $save['status'] = 3;
        $save['cost'] = $data['subscription'];
        $save['auditoruser_id'] = $data['system_user_id'];
        //启动事务
        $this->startTrans();
        $flag_save = M('order')->where(array('order_id'=>$data['order_id']))->save($save);
        $addLog['order_id'] = $data['order_id'];
        $addLog['status'] = 1;
        $addLog['paytype'] = 1;
        $addLog['cost'] = $save['cost'];
        $addLog['createtime'] = $data['createtime'];
        $flag_add = M('order_logs')->data($addLog)->add();
        if($flag_save!==false && $flag_add!==false)
        {
            $this->commit();
            return array('code'=>0,'msg'=>'预报审核成功');
        }
        $this->rollback();
        return array('code'=>1,'msg'=>'预报审核通过操作失败！');
    }



}
