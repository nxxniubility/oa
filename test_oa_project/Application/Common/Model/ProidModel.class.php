<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ProidModel extends BaseModel
{
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('accountname', 'checkSpecialCharacter', array('code'=>'201','msg'=>'推广账号名称不能含有特殊字符！'), 0, 'callback'),
        array('accountname', '0,15', array('code'=>'202','msg'=>'推广账号名称不能大于15字符！'), 0, 'length'),
        array('totalcode', '0,500', array('code'=>'203','msg'=>'统计代码不能大于500字符！'), 0, 'length'),
        array('moffcode', '0,500', array('code'=>'204','msg'=>'离线宝移动代码不能大于500字符！'), 0, 'length'),
        array('pcoffcode', '0,500', array('code'=>'205','msg'=>'离线宝PC代码不能大于500字符！'), 0, 'length'),
        array('remark', '0,1000', array('code'=>'206','msg'=>'备注不能大于1000字符！'), 0, 'length'),
    );
    /*
    |--------------------------------------------------------------------------
    | 获取单条记录
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取列表
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取总数
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
            $re_id =  $this->add($data);
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
            $re_flag =  $this->where(array('proid_id'=>$id))->save($data);
            return array('code'=>0,'data'=>$re_flag);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 删除
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function delData($id)
    {
        return $this->where(array('proid_id'=>$id))->delete();
    }

}
