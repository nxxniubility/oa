<?php
/*
|--------------------------------------------------------------------------
| 推广账号
|--------------------------------------------------------------------------
| createtime：2016-04-21
| updatetime：
| updatename：
*/
namespace Common\Model;
use Common\Model\SystemModel;
class ProidModel extends SystemModel{


	/**
     * 获取推广账号
     * @author Nixx
    */
	public function getProList($pro = null, $limit='0,10')
	{
		$order = 'createtime desc';
		$getProList['data'] =  $this->proidDb->where($pro)->order($order)->limit($limit)->select();
		$getProList['count'] =  $this->proidDb->where($pro)->count();
		foreach ($getProList['data'] as $key => $pro) {
			$channe = $this->channelDb->where("channel_id = $pro[channel_id]")->find();
			$getProList['data'][$key]['channelname'] = $channe['channelname'];
			if ($pro['pcservice_id']) {
				$pcservice = $this->servicecodeDb->where("servicecode_id = $pro[pcservice_id]")->find();
				$getProList['data'][$key]['pcservice'] = $pcservice['title'];
			}
			if ($pro['mservice_id']) {
				$mservice = $this->servicecodeDb->where("servicecode_id = $pro[mservice_id]")->find();
				$getProList['data'][$key]['mservice'] = $mservice['title'];
			}
		}	
		return $getProList;

	}
	

    /**
     * 添加账号
     * @author Nixx
    */
	public function createProid($proid)
	{	
		$proidDb = D("Proid");
		$proid['status'] = 1;
		$pro = $proidDb->where($proid)->find();
		if ($pro) {
			return false;
		}
		$proid['createtime'] = time();
		$proid_id = $proidDb->data($proid)->add();
		if (!$proid_id) {
			return false;
		}
		return $proid_id;

	}

	/**
     * 修改账号
     * @author Nixx
    */
	public function editProid($proid)
	{
		$proidDb = D("Proid");
		$proid['status'] = 1;
		$pro['proid_id'] = $proid['proid_id'];
		$pro['status'] = 1;
		$backInfo = $proidDb->where($pro)->save($proid);
		if ($backInfo === false) {
			return false;
		}
		return $proid['proid_id'];

	}

	/**
     * 获取账号详情
     * @author Nixx
    */
	public function getProInfo($proid_id)
	{
		$promote['status'] = 1;
		$proInfo = $this->proidDb->where("proid_id = $proid_id and status = 1")->find();
		if (!$proInfo) {
			return false;
		}
		$channelInfo = $this->channelDb->where("channel_id = $proInfo[channel_id]")->find();
		$proInfo['channelName'] = $channelInfo['channelname'];
		if ($proInfo['pcservice_id']) {
			$pcService = $this->servicecodeDb->where("servicecode_id = $proInfo[pcservice_id] and status =1")->find();
		}
		if ($proInfo['mservice_id']) {
			$mService = $this->servicecodeDb->where("servicecode_id = $proInfo[mservice_id] and status = 1")->find();
		}
		$proInfo['pcservice'] = $pcService['title'];
		$proInfo['mservice'] = $mService['title'];
	
		return $proInfo;
	}

	/**
     * 获取客服代码列表
     * @author Nixx
    */
	public function getserviceList($terminal_id)
	{
		$serviceList = $this->servicecodeDb->where("terminal_id = $terminal_id")->select();
		if ($serviceList) {
			return $serviceList;
		}
		return false;
	}				




	/**
	 * 删除推广账号
	 * @author Nxx
	 */
	public function deleteProid($proid_id)
	{
		$proid['status'] = 0;
		$updateproid = $this->proidDb->where("proid_id = $proid_id and status = 1")->save($proid);
		if ($updateproid === false) {
			return false;
		}
		return true;
	}

	/**
	 * 删除推广计划
	 * @author Nxx
	 */
	public function deletePro($pro)
	{	
		$promoteInfo = $this->getPromoteInfo($pro);
		$promote['status'] = 0;
		$updatepromote = $this->promoteDb->where("promote_id = $pro[promote_id] and status = 1")->save($promote);
		if ($promoteInfo['pro_lev_id'] != 0) {
			$prolevInfo = $this->prolevDb->where("pro_lev_id = $promoteInfo[pro_lev_id] and status = 1")->find();
			$updateprolevplan = $this->prolevDb->where("pro_lev_id = $prolevInfo[pid] and status = 1")->save($promote);
			$updateprolevplanunit = $this->prolevDb->where("pro_lev_id = $prolevInfo[pro_lev_id] and status = 1")->save($promote);
			if ($updateprolev === false) {
				return false;
			}
		}
		if ($updatepromote === false) {
			return false;
		}
		return true;
	}

	/*
	* 拼接推广计划id字符串查询条件
    * @author Nixx
	*/
	public function getPromoteidString($proList)
	{

		foreach($proList as $pros){
			$includedString = $includedString . ",$pros[promote_id]";
		}
		return $includedString;
	}

	/**
     * 批量修改计划
     * @author Nixx
    */ 
	public function editPromoteInfo($promote)
	{
		if ($promote['pro_lev_id']) {
			if ($promote['mark'] == 1) {  //计划
				$prolevList = M("pro_lev")->where("pid = $promote[pro_lev_id] and status=1")->select();
				foreach ($prolevList as $key => $plv) {
					if (!$pro_lev_ids) {
						$pro_lev_ids = $plv['pro_lev_id'];
					}else{
						$pro_lev_ids = $pro_lev_ids.",$plv[pro_lev_id]";
					}
				}
				$where['pro_lev_id'] = array("IN", $pro_lev_ids);
				$where['status'] = 1;
				$proList = $this->promoteDb->where($where)->select();
			}elseif($promote['mark'] == 2){  //单元
				$proList = $this->promoteDb->where("pro_lev_id = $promote[pro_lev_id] and status=1")->select();
			}
			$includedString = $this->getPromoteidString($proList);		
			if (!$includedString) {
				return false;
			}
			$promotes['promote_id'] = array("IN", $includedString);
			$promotes['status'] = 1;
			unset($promote['pro_lev_id']);
			unset($promote['mark']);
			$updatepromote = $this->promoteDb->where($promotes)->save($promote);
			if ($updatepromote === false) {
				return false;
			}
		}else{
			$proid_id = $promote['proid_id'];
			$proid['pc_pages_id'] = $promote['pc_pages_id'];
			$proid['m_pages_id'] = $promote['m_pages_id'];
			$updatepromote = $this->promoteDb->where("proid_id = $proid_id")->save($proid);
			if ($updatepromote === false) {
				return false;
			}
		}
		return true;
	}


	/**
     * 单个修改计划
     * @author Nixx
    */
	public function editPromote($promote)
	{
		
		$updatepromote = $this->promoteDb->where("promote_id = $promote[promote_id] and status=1")->save($promote);
		if ($updatepromote === false) {
			return false;
		}
		return true;
	}

	/**
     * 获取批量修改计划的搜索条件
     * @author Nixx
    */
	public function getProLevPlanList($proid_id)
	{
		$proLevPlanList = $this->prolevDb->where("proid_id = $proid_id and pid = 0 and pro_lev_id!=0 and status=1")->select();

		if (!$proLevPlanList) {
			return false;
		}		
		return $proLevPlanList;
	}

	/**
     * 获取批量修改计划的搜索条件
     * @author Nixx
    */
	public function getProLevPlanunitList($pro_lev_id)
	{
		$proLevPlanunitList = $this->prolevDb->where("pid = $pro_lev_id and status=1")->select();
		if (!$proLevPlanunitList) {
			return false;
		}
	
		return $proLevPlanunitList;
	}

	/**
     * 获取计划列表
     * @author Nixx
    */
	public function getPromoteList($promote, $limit="0,100000")
	{
		$getPromoteListAll['count'] =  $this->promoteDb->where($promote)->count();
		$proid = $this->proidDb->where("proid_id = $promote[proid_id] and status=1")->find();
		$promotes =  $this->promoteDb->where($promote)->limit($limit)->select();
		if (!$promotes) {
			return false;
		}
		foreach ($promotes as $key => $promote) {
			if ($promote['pcservice_id']) {
				$pcservice = $this->servicecodeDb->where("servicecode_id = $promote[pcservice_id]")->find();
				$promote['pcservice'] = $pcservice['url'];
			}else{
				$pcservice = $this->servicecodeDb->where("servicecode_id = $proid[pcservice_id]")->find();
				$promote['pcservice'] = $pcservice['url'];
			}
			if ($promote['mservice_id']) {
				$mservice = $this->servicecodeDb->where("servicecode_id = $promote[mservice_id]")->find();
				$promote['mservice'] = $mservice['url'];
			}else{
				$mservice = $this->servicecodeDb->where("servicecode_id = $proid[mservice_id]")->find();
				$promote['mservice'] = $mservice['url'];
			}
			$promote['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=2";
			$promote['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=1";
			$promotes[$key] = $promote;	
		}	
		$getPromoteListAll['promoteList'] = $promotes;
		return $getPromoteListAll;
	}

	/**
     * 获取指定计划
     * @author Nixx
    */
	public function getPromoteInfo($promote)
	{
		$promote['status'] = 1;
		$proInfo = $this->promoteDb->where($promote)->find();
		if (!$proInfo) {
			return false;
		}
		$proid = $this->proidDb->where("proid_id = $proInfo[proid_id] and status=1")->find();
		if (!$proInfo['pcservice_id']) {
			$proInfo['pcservice_id'] = $proid['pcservice_id'];
		}
		if (!$proInfo['mservice_id']) {
			$proInfo['mservice_id'] = $proid['mservice_id'];
		}
		$pcservice = $this->servicecodeDb->where("servicecode_id = $proInfo[pcservice_id]")->find();
		$proInfo['pcservice'] = $pcservice['url'];
		$mservice = $this->servicecodeDb->where("servicecode_id = $proInfo[mservice_id]")->find();
		$proInfo['mservice'] = $mservice['url'];
		$proInfo['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=2";
		$proInfo['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=1";
		return $proInfo;
	}


	/**
     * 获取计划详情
     * @author Nixx
    */
	public function getPromInfo($promote_id)
	{
		$proInfo = $this->promoteDb->where("promote_id = $promote_id and status=1")->find();
		if (!$proInfo) {
			return false;
		}
		return $proInfo;
	}

	/*
	拼接pro_lev_id符串
	 */
	public function getIdString($promote)
	{
		foreach($promote as $pro){
			$includedString = $includedString . ",$pro[pro_lev_id]";
		}
		return $includedString;
	}

	/**
     * 根据pro_lev_id获取所有计划
     * @author Nixx
    */
	public function getPromoteInfoByProlevid($pro_lev_id)
	{
		$prolev['status'] = 1;
		$prolev['pid'] = $pro_lev_id;
		$result = $this->prolevDb->where($prolev)->select();	
		if ($result) {
			foreach ($result as $key => $res) {
                unset($res['proid_id']);
                unset($res['promote_id']);
                $rresult[] = $res;
            }
			return $rresult;
		}
		$prolev['pro_lev_id'] = $pro_lev_id;
		unset($prolev['pid']);		
		$result = $this->prolevDb->where($prolev)->find();
		unset($result['proid_id']);
        unset($result['promote_id']); 
        $rresult[] = $result;     
		return $rresult;
	}

     

	/**
     * 获取模板列表
     * @author Nixx
    */
	public function getPagesList($pages = null, $limit='0,7')
	{
		$getPagesListAll['data'] =  $this->pagesDb->where($pages)->limit($limit)->select();
		$getPagesListAll['count'] =  $this->pagesDb->where($pages)->count();
		return $getPagesListAll;
	}

	/**
     * 根据关键字模糊搜索账号下所有计划
     * @author   Nxx
     */
    public function searchName($keyword, $proid_id)
    {
        $promote["status"] = array("eq",1);
        $promote["proid_id"] = array("eq",$proid_id);
        $promote["keyword"] = array("like","%$keyword%");
        $promoteList = $this->promoteDb->where($promote)->select(); 
        if (!$promoteList) {
        	return false;
        }
        return $promoteList;
    }

    /**
     * 查找设置模板
     * @author   Nxx
     */
    public function getSetPages($setPages)
    {
    	$setPages['status'] = 1;
    	return $this->setPagesDb->where($setPages)->select();
    }

    /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfo($setpages_id)
    {
    	return $this->setPageInfoDb->where("setpages_id = $setpages_id")->select();
    }

    /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfos($setpages_id)
    {
    	return $this->setPageInfoDb->where("setpages_id = $setpages_id")->order('pagehead')->select();
    }

	/**
     * 添加设置模板
     * @author   Nxx
     */ 
    public function	createSetPages($setPages)
    {
    	$set['system_user_id'] = $setPages['system_user_id'];
    	$set['pagesname'] = $setPages['pagesname'];
    	$set['status'] = 1;
    	$result = $this->setPagesDb->where($set)->find(); 	
    	if ($result) {
    		$error['code'] = 1;
    		$error['msg'] = '模板名已存在';
    		return $error;
    	}   	
    	$set['type'] = $setPages['type'];
    	if ($setPages['channel_id']) {
    		$result = $this->setPagesDb->where("system_user_id = $set[system_user_id] and channel_id = $setPages[channel_id] and status=1 and type=$setPages[type]")->find(); 	
	    	if ($result) {
	    		$error['code'] = 2;
	    		$error['msg'] = '该渠道已存在模板';
	    		return $error;
	    	}
	    	$set['channel_id'] = $setPages['channel_id'];
    	}else{
    		$set['channel_id'] = 0;
    	}
    	$set['createtime'] = time();   	
    	$setpages_id = $this->setPagesDb->data($set)->add();
    	if (!$setpages_id) {
    		$error['code'] = 2;
    		$error['msg'] = '模板添加失败';
    		return $error;
    	}   
    	foreach ($setPages['sign'] as $key => $pages) {
            $arr[] = $pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
        	$del = $this->setPagesDb->where("setpages_id = $setpages_id")->delete();
            $error['code'] = 3;
            $error['msg'] = '请不要重复选择表头';
            return $error;
        }	
    	foreach ($setPages['sign'] as $key => $pages) {
    		$pageInfo['pagehead'] = strtoupper($pages[0]);
    		$pageInfo['headname'] = $pages[1];
    		$pageInfo['setpages_id'] = $setpages_id;
    		$result = M("setpageinfo")->data($pageInfo)->add();
    		if (!$result) {
    			$updat = $this->setPagesDb->where("setpages_id = $setpages_id")->delete();
    			$error['code'] = 4;
	    		$error['msg'] = '模板表头设置失败';
	    		return $error;
    		}
    	}
    	$error['code'] = 0;
		$error['msg'] = $setpages_id;
		return $error;


    }

    /**
     * 修改设置模板
     * @author   Nxx
     */
    public function	editSetPages($setPages)
    {
    	foreach ($setPages['sign'] as $key => $pages) {
    		$page =strtoupper($pages[0]);
            $arr[] = $page;
        }
        if (count($arr)>count(array_unique($arr))) {
            $error['code'] = 1;
            $error['msg'] = '请不要重复选择表头';
            return $error;
        }
        D("Setpageinfo")->startTrans();
        M("setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
    	foreach ($setPages['sign'] as $key => $pages) {
    		$pageInfo['pagehead'] = strtoupper($pages[0]);
    		$pageInfo['headname'] = $pages[1];
    		$pageInfo['setpages_id'] = $setPages['setpages_id'];
    		$result = M("setpageinfo")->data($pageInfo)->add();
    		if (!$result) {
    			D("Setpageinfo")->rollback();
    			$updat = $this->setPagesDb->where("setpages_id = $setPages[setpages_id]")->delete();
    			$error['code'] = 4;
	    		$error['msg'] = '模板表头设置失败';
	    		return $error;
    		}
    	}
    	D("Setpageinfo")->commit();    	
    	$set['pagesname'] = $setPages['pagesname'];
    	if ($setPages['channel_id']) {
    		$set['channel_id'] = $setPages['channel_id'];
    		$upda = M("setpages")->where("setpages_id = $setPages[setpages_id] and status=1")->save($set);
	    	if ($upda === false) {
	    		$delInfo = M("setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
	    		$error['code'] = 3;
	    		$error['msg'] = '模板修改失败';
	    		return $error;
	    	}
    	}
    	$error['code'] = 0;
		$error['msg'] = '模板修改成功';
		return $error;
    }
    /**
     * 删除设置模板
     * @author   Nxx
     */
    public function	delSetPages($setPages)
    {
    	$set['status'] = 0;
    	$delInfo = M("setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
    	if ($delInfo === false) {
    		return false;
    	}
    	$updateSetPages = $this->setPagesDb->where("setpages_id = $setPages[setpages_id] and status=1")->save($set);
    	if ($updateSetPages === false) {
    		return false;
    	}
    	return true;
    }

    /**
     * 添加推广计划
     * @author Nixx
    */
	public function createPromote($promote)
	{
		unset($promote['pcservice']);
		unset($promote['mservice']);
		unset($promote['pc_pages']);
		unset($promote['m_pages']);
		unset($promote['createtime']);
		$promote['status'] = 1;
		//若无则执行添加操作
		$promoteInfo = $this->promoteDb->where($promote)->find();
		if ($promoteInfo) {
			return $promoteInfo['promote_id'];
		}else{
			$promote['createtime'] = time();
			$promote_id = $this->promoteDb->data($promote)->add();
			if (!$promote_id) {
				$status['status'] = 0;
				$proLevInfo = M("pro_lev")->where("pro_lev_id = $promote[pro_lev_id] and status=1")->find();
                $dels = M("pro_lev")->where("pro_lev_id = $proLevInfo[pid] and status=1")->save($status);
                $delProLev = M("pro_lev")->where("pro_lev_id == $promote[pro_lev_id] and status=1")->save($status);
				return false;
			}
			return $promote_id;
		}
	}

	/**
     * 获取客服代码
     * @author Nixx
    */
	public function getServicecode($servicecode)
	{
		return $this->servicecodeDb->where($servicecode)->find();
	}


	/**
     * 获取pro_lev
     * @author Nixx
    */
	public function getProLevInfo($prolev)
	{
		$prolev['status'] = 1;
		return $this->prolevDb->where($prolev)->find();
	}


	/**
     * 创建pro_lev
     * @author Nixx
    */
	public function createProLev($prolev)
	{
		$pro_lev_id = $this->prolevDb->data($prolev)->add();
		if (!$pro_lev_id) {
			return false;
		}
		return $pro_lev_id;
	}


}
