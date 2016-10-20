<?php

namespace Common\Model;
use Common\Model\BaseModel;

class NodeModel extends BaseModel
{
    protected $_id='id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('name', 'getStrMonogram', array('code'=>'201','msg'=>'节点方法名只能是字母组合！'), 0, 'callback'),
        array('title', 'checkSpecialCharacter', array('code'=>'202','msg'=>'节点名称不能含有特殊字符！'), 0, 'callback'),
        array('sort', 'checkInt', array('code'=>'203','msg'=>'排序只能为正整数！'), 0, 'callback'),
        array('pid', 'checkInt', array('code'=>'204','msg'=>'父级ID只能为正整数！'), 0, 'callback'),
        array('level', 'checkInt', array('code'=>'205','msg'=>'级别ID只能为正整数！'), 0, 'callback'),
        array('name', '0,20', array('code'=>'211','msg'=>'方法名称不能大于20字符！'), 0, 'length'),
        array('title', '0,15', array('code'=>'212','msg'=>'名称不能大于15字符！'), 0, 'length'),
        array('remark', '0,250', array('code'=>'213','msg'=>'方法名称不能大于250字符！'), 0, 'length'),
        array('sort', '0,10', array('code'=>'214','msg'=>'排序只能为大于10位数字！'), 0, 'length'),
        array('pid', '0,10', array('code'=>'215','msg'=>'父级ID只能为大于10位数字！'), 0, 'length'),
        array('level', '0,10', array('code'=>'216','msg'=>'级别ID只能为大于10位数字！'), 0, 'length'),
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