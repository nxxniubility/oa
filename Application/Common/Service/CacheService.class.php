<?php
/*
* Session服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class  CacheService extends BaseService {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	
	private $_clear_map=array(
	    'System/System/addNode'=>array(
		     array('type'=>'file','name'=>'Cache/Rbac'),//缓存类型，缓存名(缓存名支持两级，用点分隔)
			 //type: file-文件存储  session-session存储, memcache-memcache存储
			 array('type'=>'session','name'=>'rbac_access_list'),
			 array('type'=>'session','name'=>'rbac_menu_list_*')//删除所有key以rbac_menu_list开头的session
			
		 ),
		 'System/System/editNode'=>array(
		     array('type'=>'file','name'=>'Cache/Rbac'),
			 array('type'=>'session','name'=>'rbac_access_list'),
			
		 ),
		 'System/System/delNode'=>array(
		     array('type'=>'file','name'=>'Cache/Rbac'),
			 array('type'=>'session','name'=>'rbac_access_list'),
			
		 ),
		 'Api/Role/setRoleAccess'=>array(
		     array('type'=>'file','name'=>'Cache/Rbac'),
			 array('type'=>'session','name'=>'rbac_access_list')
			
		 )
    );
    //初始化
    public function _initialize() {
  
    }
	//获取类型
   
	public function clear($name='')
	{
		if($name=='')$name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
		
		$clear_items=$this->_clear_map[$name];
		
		if($clear_items&&!empty($clear_items))
		{
		   	foreach($clear_items as $k=>$v)
			{
				$this->clearDo($v);
				
			}
		}
	}
	public function clearAll()
	{
		foreach($this->_clear_map as $name=>$$clear_items)
		{
			if($clear_items&&!empty($clear_items))
			{
				foreach($clear_items as $k=>$v)
				{
					$this->clearDo($v);
				}
			}
		}
	}
	public function clearDo($v)
	{
	    if($v['type']=='session')
		{
			if(strpos($v['name'],'.')!==false)
			{
				$sname=explode('.',$v['name']);
				$val=session($sname[0]);
				unset($val[$sname[1]]);
				session($sname[0],$val);
			}if(strpos($v['name'],'*')!==false)
			{				
				$all_session=session();				
				$fstr=substr($v['name'],0,strlen($v['name'])-1);
				foreach($all_session as $skey=>$sval)
				{					
					if(strpos($skey,$fstr)===0)
					{						
					    session($skey,NULL);
					}
				}
				
			}else{
				session($v['name'],NULL);	
			}
			
		}elseif($v['type']=='memcache')
		{
			$memcache=D('Memcache','Service');
			if(strpos($v['name'],'.')!==false)
			{
				$sname=explode('.',$v['name']);
				$val=$memcache->get($sname[0]);
				unset($val[$sname[1]]);
				$memcache->set($sname[0],$val);
			}else{
				$memcache->delete($v['name']);	
			}
			$memcache->close();
		}else
		{
			if(is_dir(DATA_PATH.$v['name']))//'/'结尾则删除整个目录
			{
				del_dir_file(DATA_PATH.$v['name'],false);
			}else{
				F($v['name'],NULL);	
			}	
		}	
	}
}