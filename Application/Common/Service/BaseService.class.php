<?php
/*
* Cookie服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Think\Model;

class BaseService extends Model {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	
    //初始化
    public function _initialize() {
        parent::_initialize();
    }
	/**
     * 开启事务
     * @author Echo
     */
    public function startTrans() {
        M()->startTrans();
    }

    /**
     * 提交事务
     * @author Echo
     */
    public function commit() {
        M()->commit();
    }

    /**
     * 回滚事务
     * @author Echo
     */
    public function rollback() {
        M()->rollback();
    }
	
  
}