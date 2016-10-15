<?php

namespace Common\Model;
use Common\Model\BaseModel;

class SmsTemplateModel extends BaseModel
{
    protected $_id='sms_template_id';
    public function _initialize(){
        parent::_initialize();
    }

    //�Զ���֤
    protected $_validate = array(
        array('templatename', 'checkSpecialCharacter', array('code'=>'201','msg'=>'���Ʋ��ܺ��������ַ���'), 0, 'callback')
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