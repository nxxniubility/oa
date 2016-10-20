<?php
namespace Common\Model;
use Common\Model\BaseModel;
class PagesModel extends BaseModel
{
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('subject', 'checkSpecialCharacter', array('code'=>'301','msg'=>'主题不能含有特殊字符！'), 0, 'callback'),
        array('subject', '0,15', array('code'=>'302','msg'=>'主题不能大于15字符！'), 0, 'length'),
    );

    /*
     * 获取模板列表
     * @author nxx
     * @return array
     */
    public function getList($where=null, $field='*', $order=null, $limit=null, $join=null)
    {
        if ($where['pagestype_id'] == 0) {
            return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
        }else{
            return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
        }
    }

    /*
     * 根据指定条件获取模板
     * @author nxx
     */
     public function getFind($where=null, $field='*', $join=null)
     {
         return $this->field($field)->where($where)->join($join)->find();
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
             $re_flag = $this->where(array('pages_id'=>$id))->save($data);
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
         return $this->where(array($this->_id=>$id))->delete();
     }


}
