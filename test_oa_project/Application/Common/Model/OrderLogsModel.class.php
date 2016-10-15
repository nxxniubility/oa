<?php

namespace Common\Model;
use Common\Model\BaseModel;

class OrderLogsModel extends BaseModel
{
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('cost', 'checkFloatInt', array('code'=>'205','msg'=>'金额格式有误，只能为正整数！'), 0, 'callback'),
    );

    /*
    |--------------------------------------------------------------------------
    | 获取记录列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getList($where=null, $field='*', $order='createime DESC', $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取记录总数
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getCount($where=null, $join=null)
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



}
