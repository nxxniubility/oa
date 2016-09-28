<?php
namespace Common\Model;

class MarketStandardInfoModel extends SystemModel
{
    /*
   |--------------------------------------------------------------------------
   | 获取单条记录
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getFind($where=null, $field='*', $order=null, $join=null)
    {
        if (!$this->create($where)){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            return $this->getError();
        }else{
            return $this->field($field)->where($where)->join($join)->order($order)->find();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit='0,30', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null)
    {
        return $this->where($where)->join($join)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($id)
    {
        return $this->where(array('standard_id'=>$id))->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addData($data)
    {
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            return $this->add($data);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editData($data,$id)
    {
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            return $this->where(array('standard_id'=>$id))->save($data);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 删除
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delData($id)
    {
        return $this->where(array('standard_id'=>$id))->delete();
    }

}