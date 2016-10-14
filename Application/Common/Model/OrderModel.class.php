<?php
namespace Common\Model;
use Common\Model\BaseModel;
class OrderModel extends BaseModel
{
    public function _initialize(){
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取订单列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getList($where=null, $field='*', $order='createime DESC', $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }

    /*
     * 订单详情
     * @author nxx
     */
     public function getFind($where=null, $field='*', $join=null)
     {
         return $this->field($field)->where($where)->join($join)->find();
     }

    /*
    |--------------------------------------------------------------------------
    | 获取订单总数
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getCount($where=null,$join=null)
    {
        return $this->where($where)->join($join)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addData($data)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_id = $this->add($data);
            return array('code'=>0,'data'=>$re_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function editData($data,$id)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_flag = $this->where(array('order_id'=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
        }
    }

    /*
    * 预报审核通过
    * subscription createtime
    * @author nxx
    */
    // public function updataOrder($data)
    // {
    //     $save['status'] = 3;
    //     $save['cost'] = $data['subscription'];
    //     $save['auditoruser_id'] = $data['system_user_id'];
    //     //启动事务
    //     $this->startTrans();
    //     $flag_save = M('order')->where(array('order_id'=>$data['order_id']))->save($save);
    //     $addLog['order_id'] = $data['order_id'];
    //     $addLog['status'] = 1;
    //     $addLog['paytype'] = 1;
    //     $addLog['cost'] = $save['cost'];
    //     $addLog['createtime'] = $data['createtime'];
    //     $flag_add = M('order_logs')->data($addLog)->add();
    //     if($flag_save!==false && $flag_add!==false)
    //     {
    //         $this->commit();
    //         return array('code'=>0,'msg'=>'预报审核成功');
    //     }
    //     $this->rollback();
    //     return array('code'=>1,'msg'=>'预报审核通过操作失败！');
    // }



}
