<?php

namespace Common\Model;
use Common\Model\BaseModel;

class UserAbandonModel extends BaseModel
{
    protected $_id='user_abandon_id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('abandonname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'名称不能含有特殊字符！'), 0, 'callback'),
        array('callbacknum', 'checkInt', array('code'=>'201','msg'=>'要求回访次数只能为正整数！'), 0, 'callback'),
        array('attaindays', 'checkInt', array('code'=>'201','msg'=>'达到要求保护天数只能为正整数！'), 0, 'callback'),
        array('unsatisfieddays', 'checkInt', array('code'=>'201','msg'=>'未达到要求保护天数只能为正整数！'), 0, 'callback'),
        array('abandonname', '0,10', array('code'=>'202','msg'=>'姓名不能大于十位字符！'), 0, 'length'),
        array('callbacknum', '0,10', array('code'=>'203','msg'=>'要求回访次数不能大于十位数字！'), 0, 'length'),
        array('attaindays', '0,10', array('code'=>'202','msg'=>'达到要求保护天数不能大于十位数字！'), 0, 'length'),
        array('unsatisfieddays', '0,10', array('code'=>'203','msg'=>'未达到要求保护天数不能大于十位数字！'), 0, 'length'),
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

    public function getAbandonList($where="",$fields=""){
        $where['status'] = 1;
        return $this->field($fields)->where($where)->select();
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