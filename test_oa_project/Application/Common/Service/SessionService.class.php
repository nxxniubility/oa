<?php
/*
* Session服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class SessionService extends BaseService {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	
    //初始化
    public function _initialize() {
        parent::_initialize();
    }
	//初始化session设置
	protected function init($config)
	{
		if($config)
		{
			session($config);
		}
	}
	//添加session项
	public function set($key,$value)
	{
		if($key)session($key,$value);		
	}
    //读取session值
	public function get($key)
	{
		if($key)return session($key);
	}
	//删除session值
	public function delete($key)
	{
		if($key)session($key,NULL);
	}
	//清空session值
	public function clear($key)
	{
		session(NULL);
	}
	//判断是否赋值
	public function ifset($key)
	{
		if($key)
		{
			return session("?".$key);
		}else{
			return false;
		}
	}
	//获取所有值
	public function getAll()
	{
	    return session();	
	}
    /*
	* 登陆session设置 
	* @author luoyu
	* @param $info  更新项-值组成的数组，可以是以下这些项：
	  /*用户共有的值
	  	  
		'zp_user_id' => $info['zp_user_id'],
		'zp_username' => $info['zp_username'],
		'zp_realname' => $info['zp_realname'],
		'zp_intent' => $info['zp_intent'],           
		'zp_face' => $info['zp_face'],
		'zp_sex' => $info['zp_sex'],
		'city_id' => $info['city_id']               
	  
	  /*招人
  
		$zp_userinfo['zp_company_id'] = $info['zp_company_id'];
		$zp_userinfo['zp_company_email'] = $info['zp_company_email'];
		$zp_userinfo['zp_company_mobile'] = $info['zp_company_mobile'];
		$zp_userinfo['zp_company_status'] = $info['zp_company_status'];
		$zp_userinfo['zp_company_realname'] = $info['zp_real_name'];
		$zp_userinfo['zp_company_face'] = $info['zp_real_face'];
		$zp_userinfo['zp_user_post'] = $info['zp_user_post'];
       
	*/

    public function setUserSession($info) {
		
		$zp_userinfo=$this->get("zp_user");
		$zp_userinfo=empty($zp_userinfo)?array():$zp_userinfo;
		if($info['zp_real_name'])
		{
			$info['zp_company_face'] = $info['zp_real_face'];
			unset($info['zp_real_face']);
		}
		if($info['zp_real_name']){
			$info['zp_company_realname'] = $info['zp_real_name'];
			unset($info['zp_real_name']);
		}
        
		$zp_userinfo=array_merge($zp_userinfo,$info);		
        $this->set("zp_user", $zp_userinfo);
		if(!empty($info['zp_user_id']))
		{
           $this->set("zp_user_id", $info['zp_user_id']);
		}
    }
	
}