<?php

namespace Common\Model;
use Common\Model\BaseModel;

class SystemUpdateModel extends BaseModel
{
    protected $_id='system_update_id';
    public function _initialize(){
        parent::_initialize();
    }

    //自动验证
    protected $_validate = array(
        array('uptitle', '0,10', array('code'=>'201','msg'=>'标题不能大于10位字符！'), 0, 'length'),
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

    /**
     *获取所有的系统更新信息
     * @author cq
     * @return array  系统更新的所有信息
     */
    public  function  getSystemUpdateInfo($where='',$limit=null, $order='createtime DESC'){

        /* $sysUpdateData['data'] = $this->where($where)->limit($limit)->select();
         $sysUpdateData['count'] = $this->where($where)->count();
         return  $sysUpdateData;*/
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}system_update.system_update_id",
            "{$DB_PREFIX}system_update.system_user_id",
            "{$DB_PREFIX}system_update.uptitle",
            "{$DB_PREFIX}system_update.upbody",
            "{$DB_PREFIX}system_update.createtime",
            "{$DB_PREFIX}system_user.realname"
        );
        $john = 'LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_UPDATE__.system_user_id =  __SYSTEM_USER__.system_user_id';
        $sysUpdateData['data'] =  $this->field($field)->where($where)->join($john)->order($order)->limit($limit)->select();
        $sysUpdateData['count'] =  $this->field($field)->where($where)->join($john)->count();
        return  $sysUpdateData;

    }




    /**
     * 新增加一项更新信息
     * @author  cq
     * @ result  添加的结果
     */
    public  function  addNewUpdateInfo($data){

        $result = $this->data($data)->add();
        return $result;
    }

    /*
     *删除指定条件的更新信息
     * @author cq
     * @parameter $where 删除条件
     */
    public  function  delUpdateInfo($where){

        $result = $this->where($where)->delete();
        return $result;

    }

    /** 更新数据
     * @author cq
     * @param $where 修改条件
     * @param $data  修改数据
     * @return mixed
     */
    public  function  modifyUpdateInfo($where,$data){
        $result = $this->where($where)->save($data);
        return $result;
    }
}