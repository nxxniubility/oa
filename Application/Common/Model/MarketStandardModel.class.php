<?php
namespace Common\Model;

class MarketStandardModel extends SystemModel
{
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('standard_name', '0,10', array('code'=>'203','msg'=>'名称不能大于10位字符！'), self::EXISTS_VALIDATE, 'length'),
        array('standard_remark', '0,250', array('code'=>'203','msg'=>'备注不能大于250位字符！'), self::EXISTS_VALIDATE, 'length'),
    );

    /*
   |--------------------------------------------------------------------------
   | 获取单条记录
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getFind($where=null, $field='*', $order=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->find();
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
        // 如果创建失败 表示验证没有通过 输出错误提示信息
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $result =  $this->add($data);
            return array('code'=>0, 'data'=>$result);
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
            $result =  $this->where(array('standard_id'=>$id))->save($data);
            return array('code'=>0, 'data'=>$result);
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