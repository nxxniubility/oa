<?php

namespace Common\Model;
use Common\Model\BaseModel;

class CourseProductModel extends BaseModel
{
    protected $_id='course_product_id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('productname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'名称已经存在,请重新输入！'), 0, 'unique'),
        array('productname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'名称不能含有特殊字符！'), 0, 'callback'),
        array('price', 'checkInt', array('code'=>'202','msg'=>'价格只能为数字！'), 0, 'callback'),
        array('productname', '0,20', array('code'=>'211','msg'=>'名称不能大于20字符！'), 0, 'length'),
        array('price', '0,10', array('code'=>'212','msg'=>'价格不能大于10位！'), 0, 'length'),
        array('description', '0,350', array('code'=>'213','msg'=>'内容不能大于350字符！'), 0, 'length'),
    );

    /*
    |--------------------------------------------------------------------------
    | 获取单条记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
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
            $re_id =  $this->add($data);
            return array('code'=>0,'data'=>$re_id);
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
            $re_flag =  $this->where(array($this->_id=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
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
        return $this->where(array($this->_id=>$id))->delete();
    }

}