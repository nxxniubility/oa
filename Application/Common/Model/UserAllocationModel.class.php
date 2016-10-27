<?php

namespace Common\Model;
use Common\Model\BaseModel;

class UserAllocationModel extends BaseModel
{
    protected $_id='user_allocation_id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('allocationname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'名称不能含有特殊字符！'), 0, 'callback'),
        array('startnum', 'checkInt', array('code'=>'201','msg'=>'开始天数只能为正整数！'), 0, 'callback'),
        array('intervalnum', 'checkInt', array('code'=>'201','msg'=>'间隔天数只能为正整数！'), 0, 'callback'),
        array('sort', 'checkInt', array('code'=>'201','msg'=>'排序只能为正整数！'), 0, 'callback'),
        array('allocationname', '0,10', array('code'=>'202','msg'=>'姓名不能大于十位字符！'), 0, 'length'),
        array('startnum', '0,10', array('code'=>'202','msg'=>'开始天数不能大于十位数字！'), 0, 'length'),
        array('intervalnum', '0,10', array('code'=>'203','msg'=>'间隔天数不能大于十位数字！'), 0, 'length'),
        array('sort', '0,10', array('code'=>'202','msg'=>'排序数量不能大于十位数字！'), 0, 'length'),
    );

    /*
    |--------------------------------------------------------------------------
    | ��ȡ������¼
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | ��ȡ�б�
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | ��ȡ����
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null)
    {
        return $this->where($where)->join($join)->count();
    }


    /*
    |--------------------------------------------------------------------------
    | ���
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addData($data)
    {
        // �������ʧ�� ��ʾ��֤û��ͨ�� ���������ʾ��Ϣ
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_id =  $this->add($data);
            return array('code'=>0,'data'=>$re_id);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | �޸�
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editData($data,$id)
    {
        // �������ʧ�� ��ʾ��֤û��ͨ�� ���������ʾ��Ϣ
        if (!$this->create($data)){
            return $this->getError();
        }else{
            $re_flag =  $this->where(array($this->_id=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ɾ��
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delData($id)
    {
        return $this->where(array($this->_id=>$id))->delete();
    }

}