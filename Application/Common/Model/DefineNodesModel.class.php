<?php

namespace Common\Model;

class DefineNodesModel extends   SystemModel{

    protected   $defineNodeDb;

   public function  _initialize(){
   }

    /**删除用户的所有默认节点
     * @author cq
     * @param $system_user_id
     * @return mixed
     */
    public function delDefineNodes($system_user_id){
        return $this->where(array('system_user_id'=>$system_user_id))->delete();
    }

    /**
     * 添加新定义的节点
     * @author  cq
     * @param  $system_user_id
     * @param  $data
     * @return mixed
     */
    public  function addDefineNode($system_user_id, $data){

        return  $this->where(array('system_user_id'=>$system_user_id))->data($data)->add();
    }
    
    /**
     * 获取用户默认的节点\
     * @author cq
     * @param $user_id 用户id
     * @param $role_id 角色id
     */
    public  function  getUserDefaultNodes($user_id, $role_id){
         $userDefaultNodes = D('DefineNodes')->where(array('system_user_id' => $user_id, 'role_id' => $role_id))
                ->join('LEFT JOIN zl_node on zl_node.id = zl_define_nodes.node_id')->order('zl_define_nodes.sort ASC')->select();

        return $userDefaultNodes;
    }








}
