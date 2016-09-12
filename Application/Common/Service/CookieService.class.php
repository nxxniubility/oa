<?php
/*
* Cookie服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Model\BaseService;

class CookieService extends BaseService {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	
    //初始化
    public function _initialize() {
        parent::_initialize();
    }
	//初始化cookie设置
	protected function init($config)
	{
		if($config)
		{
			cookie($config);
		}
	}
	//添加cookie项
	public function set($key,$value,$time)
	{
		if($time===0||(!empty($time)&&is_numeric($time)))
		{
		   if($key)cookie($key,$value,$time);	
		}else{
		   if($key)cookie($key,$value);	
		}
		
	}
    //读取cookie值
	public function get($key)
	{
		if($key)return cookie($key);
	}
	//删除cookie值
	public function delete($key)
	{
		if($key)cookie($key,NULL);
	}
	//清空cookie值
	public function clear($key)
	{
		cookie(NULL);
	}
	//判断是否赋值
	public function ifset($key)
	{
		if($key)
		{
			return cookie("?".$key);
		}else{
			return false;
		}
	}
	//获取所有值
	public function getAll()
	{
	    return cookie();	
	}
  
}