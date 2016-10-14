<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 15:15
 */

namespace Common\Model;


use Common\Model\SystemModel;
class SystemUpdateModel extends SystemModel
{

    /**
     *获取所有的系统更新信息
     * @author cq
     * @return array  系统更新的所有信息
     */
    public  function  getSystemUpdateInfo($where='',$limit=null, $order='createtime DESC'){

        /* $sysUpdateData['data'] = $this->systemUpdateDb->where($where)->limit($limit)->select();
         $sysUpdateData['count'] = $this->systemUpdateDb->where($where)->count();
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

        $result = $this->systemUpdateDb->data($data)->add();
        return $result;
    }

    /*
     *删除指定条件的更新信息
     * @author cq
     * @parameter $where 删除条件
     */
    public  function  delUpdateInfo($where){

        $result = $this->systemUpdateDb->where($where)->delete();
        return $result;

    }

    /** 更新数据
     * @author cq
     * @param $where 修改条件
     * @param $data  修改数据
     * @return mixed
     */
    public  function  modifyUpdateInfo($where,$data){
        $result = $this->systemUpdateDb->where($where)->save($data);
        return $result;
    }


}