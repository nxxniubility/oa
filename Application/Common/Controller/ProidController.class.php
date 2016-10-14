<?php
namespace Common\Controller;
use Common\Controller\BaseController;
class ProidController extends BaseController
{

    /**
     * 获取客服代码
     */
    public function getOwnServicecode($where)
    {
        $servicecodeList = D("Servicecode")->where($where)->select();
        if ($servicecodeList) {
            foreach ($servicecodeList as $key => $servicecode) {
            $terminal = D("Terminal")->where("terminal_id = $servicecode[terminal_id]")->find();
            $servicecode['terminalname'] = $terminal['terminalname'];
            $servicecodeList[$key] = $servicecode;
            }
            return array('code'=>'0', 'msg'=>$servicecodeList);
        }
        return array('code'=>'1', 'msg'=>"么有客服代码哎");

    }

    /**
     * 添加客服代码
     * @author zgt                           
     * @return array
     */
    public function addServicecode($data)
    {
        $servicecode['createtime'] = time();
        $servicecode = array_merge_recursive($servicecode,$data);
        $servicecode_id = D("Servicecode")->data($servicecode)->add();
        if ($servicecode_id!==false){
            return array('code'=>'0', 'msg'=>$servicecode_id);
        }
        return array('code'=>'1', 'msg'=>"添加客服代码失败");
    }

    /**
     * 修改客服代码
     * @author zgt
     * @return array
     */
    public function editServicecode($data,$servicecode_id)
    {
        $result = D("Servicecode")->where("servicecode_id={$servicecode_id}")->save($data);
        if ($result!==false) {
            return array('code'=>0, 'msg'=>"操作成功");
        }
        return array('code'=>1, 'msg'=>"操作失败");
    }

    /**
     * 客服代码详情
     * @author zgt
     * @return array
     */
    public function detailServicecode($servicecode_id){
        $servicecodeInfo = D("Servicecode")->where("servicecode_id=$servicecode_id")->find();
        return array('code'=>'0', 'msg'=>$servicecodeInfo);
    }

    /*
     * 获取所有终端表-缓存
     * @author zgt
     * @return array
     */
    public function getAllTerminal()
    {
        if (F('Cache/Promote/terminal')) {
            $terminalAll = F('Cache/Promote/terminal');
        } else {
            $terminalAll = D("Terminal")->where(array('status'=>1))->select();
            F('Cache/Promote/terminal', $terminalAll);
        }
        return array('code'=>'0', 'msg'=>$terminalAll);
    }

    /*
     * 获取所有模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getPagesList($request)
    {
        if ($request['pagestype_id'] == 0) {
            $pagesList['data'] = D('Pages')->where("terminal_id = $request[terminal_id] and status=1")->select();
        }else{
            $pagesList['data'] = D('Pages')->where($request)->select();
        }
        return array('code'=>0, 'msg'=>$pagesList);
    }

    /*
     * 获取所有模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getAllPages($order='createtime desc',$page=null,$where=array('status'=>1))
    {
        $DB_PREFIX = C('DB_PREFIX');
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
        } else {
            $pagesAll['data'] = D("Pages")
                ->where($where)
                ->order($DB_PREFIX.'pages.createtime DESC')
                ->select();
            $pagesAll['count'] = D("Pages")->count();
            F('Cache/Promote/pages',$pagesAll);
        }

        $pagesAll = $this->disposeArray($pagesAll, $order, $page, $where);
        return array('code'=>'0', 'msg'=>$pagesAll);
    }

    /*
     * 获取所有模板分类
     * @author nxx
     * @return array
     */
    public function getPagesType($data)
    {
        $data['status'] = 1;
        $datas = D("PagesType")->where($data)->select();
        return array('code'=>0,'msg'=>$datas);
    }

    /*
     * 获取模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getPagesInfo($pages_id)
    {
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            foreach($pagesAll['data'] as $k=>$v){
                if($v['pages_id']==$pages_id){
                    $pagesInfo = $v;
                }
            }
        } else {
            $pagesInfo = D("Pages")->where(array('pages_id'=>$pages_id))->find();
        }
        return array('code'=>0, 'msg'=>$pagesInfo);
    }

    /*
     * 获取模板相关联计划列表
     * @author zgt
     * @return array
     */
    public function getPagesPromote($pages_id,$where=null,$limit='0,10')
    {
        $DB_PREFIX = C('DB_PREFIX');
        $where["{$DB_PREFIX}promote.status"] = 1;
        $where["{$DB_PREFIX}promote.pc_pages_id|{$DB_PREFIX}promote.m_pages_id"] = $pages_id;
        $promoteList['data'] = D('Promote')
            ->field(
                array(
                    "{$DB_PREFIX}promote.plan",
                    "{$DB_PREFIX}promote.keyword",
                    "{$DB_PREFIX}promote.createtime",
                    "{$DB_PREFIX}channel.channelname",
                    "{$DB_PREFIX}system_user.realname"
                )
            )
            ->join("LEFT JOIN __PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
            ->join("LEFT JOIN __CHANNEL__ on __CHANNEL__.channel_id=__PROID__.channel_id")
            ->join("LEFT JOIN __SYSTEM_USER__ on __SYSTEM_USER__.system_user_id=__PROID__.system_user_id")
            ->where($where)
            ->order("{$DB_PREFIX}promote.createtime DESC")
            ->limit($limit)
            ->select();

        $promoteList['count'] = D('Promote')
            ->join("LEFT JOIN __PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
            ->join("LEFT JOIN __CHANNEL__ on __CHANNEL__.channel_id=__PROID__.channel_id")
            ->join("LEFT JOIN __SYSTEM_USER__ on __SYSTEM_USER__.system_user_id=__PROID__.system_user_id")
            ->where($where)
            ->count();

        return array('code'=>0,'msg'=>$promoteList);
    }

    /*
     * 根据terminal_id获取模板内容-缓存
     * @author zgt
     * @return array
     */
    public function getPages($terminal_id)
    {
        if (F('Cache/Promote/pages')) {
            $pagesAll = F('Cache/Promote/pages');
            foreach($pagesAll['data'] as $k=>$v){
                if($v['terminal_id']==$terminal_id){
                    $pagesInfo = $v;
                }
            }
        } else {
            $pagesInfo = D("Pages")->where(array('terminal_id'=>$terminal_id))->find();
        }
        return $pagesInfo;
    }

    /**
     * 添加模板
     * @author zgt
     */
    public function addPages($data)
    {
        $data['status'] = 1;
        $data['createtime'] = time();
        $result = D("Pages")->data($data)->add();
        if ($result!==false){
            $data['pages_id'] = $result;
            if (F('Cache/Promote/pages')) {
                $pagesAll = F('Cache/Promote/pages');
                $pagesAll['data'][] = $data;
                $pagesAll['count'] = ($pagesAll['count']+1);
                F('Cache/Promote/pages', $pagesAll);
            }
            return array('code'=>0, 'msg'=>$result);
        }
        return array('code'=>1, 'msg'=>'添加模板失败');
    }

    /**
     * 修改模板
     * @author zgt
     */
    public function editPages($data, $pages_id)
    {
        $result = D("Pages")->where(array('pages_id'=>$pages_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/pages')) {
                $newInfo = D("Pages")->where(array('pages_id'=>$pages_id))->find();
                $pagesAll = F('Cache/Promote/pages');
                foreach($pagesAll['data'] as $k=>$v){
                    if($v['pages_id'] == $pages_id){
                        $pagesAll['data'][$k] = $newInfo;
                    }
                }
                F('Cache/Promote/pages', $pagesAll);
            }
            return array('code'=>0, 'msg'=>'修改成功');
        }
        return array('code'=>1, 'msg'=>'修改失败');
    }
    /**
     * 获取模板 导航
     * @author zgt
     */
    public function getPagesNav($pages_id)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $inf = D('Pagesnav')
            ->where(array($DB_PREFIX.'pagesnav.pages_id'=>$pages_id))
            ->join('__PAGES__ ON __PAGES__.pages_id=__PAGESNAV__.pages_nav_id')
            ->order($DB_PREFIX.'pagesnav.sort asc')
            ->select();
        return array('code'=>0, 'msg'=>$inf);
    }
    /**
     * 添加模板 导航
     * @author zgt
     */
    public function addPagesNav($data,$pages_id)
    {
        $add['pages_id'] = $pages_id;
        D('Pagesnav')->where(array('pages_id'=>$pages_id))->delete();
        if(!empty($data)){
            $data = explode(',', $data);
            foreach($data as $k=>$v){
                $v = explode('@@', $v);
                $add['pages_nav_id'] = $v[0];
                $add['nav_name'] = $v[1];
                $add['sort'] = ($k+1);
                $result = D('Pagesnav')->data($add)->add();
                if(!$result) {
                    return array('code'=>1, 'msg'=>"添加导航失败");
                }
            }
        }
        return array('code'=>0, 'msg'=>"添加导航成功");
    }

    /*
     * 获取模板分类-缓存
     * @author zgt
     * @return array
     */
    public function getAllPagesType()
    {
        if (F('Cache/Promote/pagesType')) {
            $pagesTypeAll = F('Cache/Promote/pagesType');
        } else {
            $pagesTypeAll = D('PagesType')->where(array('status'=>1))->select();
            F('Cache/Promote/pagesType',$pagesTypeAll);
        }

        return array("code"=>0,'msg'=>$pagesTypeAll);
    }

    /**
     * 添加模板分类
     * @author zgt
     */
    public function addPagesType($data)
    {
        $data['status'] = 1;
        $result = D('PagesType')->data($data)->add();
        if ($result!==false){
            $data['pagestype_id'] = $result;
            if (F('Cache/Promote/pagesType')) {
                $cacheAll = F('Cache/Promote/pagesType');
                $cacheAll[] = $data;
                F('Cache/Promote/pagesType', $cacheAll);
            }
            return array("code"=>0,'msg'=>$result);
        }
        return array("code"=>1,'msg'=>'添加模板分类失败');
    }

    /**
     * 修改模板分类
     * @author zgt
     */
    public function editPagesType($data,$pagestype_id)
    {
        $result = D('PagesType')->where(array('pagestype_id'=>$pagestype_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/pagesType')) {
                $newInfo = D('PagesType')->where(array('pagestype_id'=>$pagestype_id))->find();
                $cacheAll = F('Cache/Promote/pagesType');
                foreach($cacheAll as $k=>$v){
                    if($v['pagestype_id'] == $pagestype_id){
                        if(!empty($data['satus']) && $data['satus']==0){
                            unset($cacheAll[$k]);
                        }else{
                            $cacheAll[$k] = $newInfo;
                        }
                    }
                }
                F('Cache/Promote/pagesType', $cacheAll);
            }
            return array('code'=>0,'msg'=>'修改模板分类成功');
        }
        return array('code'=>1,'msg'=>'修改模板分类失败');
    }
    /**
     * 查看模板备注
     * @author zgt
     */
    public function getPagesRemark($pages_id,$system_user_id)
    {
        $remarklt = D('PagesRemark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
        return array('code'=>0, 'msg'=>$remarklt);
    }

    /**
     * 添加模板备注
     * @author zgt
     */
    public function addPagesRemark($remark,$pages_id,$system_user_id)
    {
        $data['remark'] = $remark;
        $remarklt = D('PagesRemark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->find();
        if (empty($remarklt)){
            $data['pages_id'] = $pages_id;
            $data['system_user_id'] = $system_user_id;
            $result = D('PagesRemark')->data($data)->add();
        }else{
            $result = D('PagesRemark')->where(array('pages_id'=>$pages_id,'system_user_id'=>$system_user_id))->save($data);
        }
        if($result!==false) {
            array('code'=>0,'msg'=>'备注添加成功');
        }else{
            return array('code'=>1,'msg'=>'备注添加失败');
        }
    }
        

    public function getAllPagesnav($where,$field){
        return D('Pagesnav')->where($where)->field($field)->select();
    }


    /**
     * 修改终端表
     * @author zgt
     */
    public function editTerminal($data, $terminal_id)
    {
        $terminalDb = D('Terminal');
        $result = $terminalDb->where(array('terminal_id'=>$terminal_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/terminal')) {
                $newInfo = $terminalDb->where(array('terminal_id'=>$terminal_id))->find();
                $cacheAll = F('Cache/Promote/terminal');
                foreach($cacheAll['data'] as $k=>$v){
                    if($v['terminal_id'] == $terminal_id){
                        if(!empty($data['satus']) && $data['satus']==0){
                            unset($cacheAll['data'][$k]);
                        }else{
                            $cacheAll['data'][$k] = $newInfo;
                        }
                    }
                }
                F('Cache/Promote/terminal', $cacheAll);
            }
            return array('code'=>0,'msg'=>'成功');
        }
        return array('code'=>1,'msg'=>'失败');
    }


    /**
     * 添加终端表
     * @author zgt
     */
    public function addTerminal($data)
    {
        $data['status'] = 1;
        $result = D('Terminal')->data($data)->add();
        if ($result!==false){
            $data['terminal_id'] = $result;
            if (F('Cache/Promote/terminal')) {
                $cacheAll = F('Cache/Promote/terminal');
                $cacheAll[] = $data;
                F('Cache/Promote/terminal', $cacheAll);
            }
            return array('code'=>0,'msg'=>$result);
        }
        return array('code'=>1,'msg'=>'添加失败');;
    }

    /*
    |--------------------------------------------------------------------------
    | 推广账号管理
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function proidList($pro, $limit)
    {
        $order = 'createtime desc';
        $proidDb = D("Proid");
        $servicecodeDb = D('Servicecode');
        $getProList['data'] = $proidDb->where($pro)->order($order)->limit($limit)->select();
        $getProList['count'] = $proidDb->where($pro)->count();
        foreach ($getProList['data'] as $key => $pro) {
            $channe = D('Channel')->where("channel_id = $pro[channel_id]")->find();
            $getProList['data'][$key]['channelname'] = $channe['channelname'];
            if ($pro['pcservice_id']) {
                $pcservice = $servicecodeDb->where("servicecode_id = $pro[pcservice_id]")->find();
                $getProList['data'][$key]['pcservice'] = $pcservice['title'];
            }
            if ($pro['mservice_id']) {
                $mservice = $servicecodeDb->where("servicecode_id = $pro[mservice_id]")->find();
                $getProList['data'][$key]['mservice'] = $mservice['title'];
            }
        }
        return array('code'=>0,'msg'=>$getProList);
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
            return array('code'=>'1', 'msg'=>'已存在相同的推广账号');
        }
        $proid['createtime'] = time();
        $proid_id = $proidDb->data($proid)->add();
        if (!$proid_id) {
            return array('code'=>'2', 'msg'=>'创建推广账号失败');
        }
        return array('code'=>'0', 'msg'=>'创建推广账号成功');

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
            return array('code'=>'1', 'msg'=>'修改推广账号失败');
        }
        return array('code'=>'0', 'msg'=>'修改推广账号成功');

    }

    /**
     * 获取账号详情
     * @author Nixx
    */
    public function getProInfo($proid_id)
    {
        $promote['status'] = 1;
        $proInfo = D("Proid")->where("proid_id = $proid_id and status = 1")->find();
        if (!$proInfo) {
            return array('code'=>1,'msg'=>'没有信息');
        }
        $channelInfo = D("Channel")->where("channel_id = $proInfo[channel_id]")->find();
        $proInfo['channelName'] = $channelInfo['channelname'];
        if ($proInfo['pcservice_id']) {
            $pcService = D("Servicecode")->where("servicecode_id = $proInfo[pcservice_id] and status =1")->find();
        }
        if ($proInfo['mservice_id']) {
            $mService = D("Servicecode")->where("servicecode_id = $proInfo[mservice_id] and status = 1")->find();
        }
        $proInfo['pcservice'] = $pcService['title'];
        $proInfo['mservice'] = $mService['title'];
    
        return array('code'=>0,'msg'=>$proInfo);
    }

    /**
     * 删除推广账号
     * @author Nxx
     */
    public function deleteProid($proid_id)
    {
        $proid['status'] = 0;
        $updateproid = D("Proid")->where("proid_id = $proid_id and status = 1")->save($proid);
        if ($updateproid === false) {
            return array("code"=>1,'msg'=>'删除推广账号失败');
        }
        return array("code"=>0,'msg'=>'删除推广账号成功');
    }

    /**
     * 删除推广计划
     * @author Nxx
     */
    public function deletePro($pro)
    {   
        $promoteInfo = $this->getPromoteInfo($pro);
        $promote['status'] = 0;
        $updatepromote = D("Promote")->where("promote_id = $pro[promote_id] and status = 1")->save($promote);
        if ($updatepromote === false) {
            return array("code"=>1,'msg'=>'删除推广计划失败');
        }
        return array("code"=>0,'msg'=>'删除推广计划成功');
    }

    /**
     * 获取pro_lev
     * @author Nixx
    */
    public function getProLevInfo($prolev)
    {
        $prolev['status'] = 1;
        $prolevInfo = D("ProLev")->where($prolev)->find();
        if (!$prolevInfo) {
            return array('code'=>1, 'msg'=>'获取失败');
        }
        return array('code'=>0, 'msg'=>$prolevInfo);
    }


    /**
     * 创建pro_lev
     * @author Nixx
    */
    public function createProLev($prolev)
    {
        $pro_lev_id = D("ProLev")->data($prolev)->add();
        if (!$pro_lev_id) {
            return array("code"=>1,'msg'=>'创建pro_lev失败');
        }
        return array('code'=>0, 'msg'=>$pro_lev_id);
    }


    /**
     * 添加推广计划
     * @author Nixx
    */
    public function createPromote($promote)
    {
        $promoteDb = D("Promote");
        $proLevDb = D("ProLev");
        unset($promote['pcservice']);
        unset($promote['mservice']);
        unset($promote['pc_pages']);
        unset($promote['m_pages']);
        unset($promote['createtime']);
        $promote['status'] = 1;
        //若无则执行添加操作
        $promoteInfo = $promoteDb->where($promote)->find();
        if ($promoteInfo) {
            return array("code"=>0,'msg'=>$promoteInfo['promote_id']);
        }else{
            $promote['createtime'] = time();
            $promote_id = $promoteDb->data($promote)->add();
            if (!$promote_id) {
                $status['status'] = 0;
                $proLevInfo = $proLevDb->where("pro_lev_id = $promote[pro_lev_id] and status=1")->find();
                $dels = $proLevDb->where("pro_lev_id = $proLevInfo[pid] and status=1")->save($status);
                $delProLev = $proLevDb->where("pro_lev_id == $promote[pro_lev_id] and status=1")->save($status);
                return array("code"=>1,'msg'=>'添加推广计划失败');
            }
            return array("code"=>0,'msg'=>$promote_id);
        }
    }


    /**
     * 获取计划列表
     * @author Nixx
    */
    public function getPromoteList($promote, $limit="0,100000")
    {
        $promoteDb = D("Promote");
        $servicecodeDb = D("Servicecode");
        $getPromoteListAll['count'] = $promoteDb->where($promote)->count();
        $proid = D("Proid")->where("proid_id = $promote[proid_id] and status=1")->find();
        $promotes =  $promoteDb->where($promote)->limit($limit)->select();
        if (!$promotes) {
            return array('code'=>1, 'msg'=>'暂无数据');
        }
        foreach ($promotes as $key => $promote) {
            if ($promote['pcservice_id']) {
                $pcservice = $servicecodeDb->where("servicecode_id = $promote[pcservice_id]")->find();
                $promote['pcservice'] = $pcservice['url'];
            }else{
                $pcservice = $servicecodeDb->where("servicecode_id = $proid[pcservice_id]")->find();
                $promote['pcservice'] = $pcservice['url'];
            }
            if ($promote['mservice_id']) {
                $mservice = $servicecodeDb->where("servicecode_id = $promote[mservice_id]")->find();
                $promote['mservice'] = $mservice['url'];
            }else{
                $mservice = $servicecodeDb->where("servicecode_id = $proid[mservice_id]")->find();
                $promote['mservice'] = $mservice['url'];
            }
            $promote['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=2";
            $promote['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$promote['promote_id']}&dev=1";
            $promotes[$key] = $promote; 
        }   
        $getPromoteListAll['promoteList'] = $promotes;
        return array('code'=>0, 'msg'=>$getPromoteListAll);
    }

    /**
     * 获取批量修改计划的搜索条件
     * @author Nixx
    */
    public function getProLevPlanunitList($pro_lev_id)
    {
        $proLevPlanunitList = D("ProLev")->where("pid = $pro_lev_id and status=1")->select();
        if (!$proLevPlanunitList) {
            return array('code'=>1,'msg'=>"暂无数据");
        }
    
        return array('code'=>1,'msg'=>$proLevPlanunitList);
    }

    /*
    拼接pro_lev_id符串
     */
    public function getIdString($promote)
    {
        foreach($promote as $pro){
            $includedString = $includedString . ",$pro[pro_lev_id]";
        }
        return array('code'=>0, 'msg'=>$includedString);
    }

    /**
     * 获取计划
     * @author Nixx
    */
    public function getProLevPlanList($proid_id)
    {
        $proLevPlanList = D("ProLev")->where("proid_id = $proid_id and pid = 0 and pro_lev_id!=0 and status=1")->select();

        if (!$proLevPlanList) {
            return array('code'=>1, 'msg'=>'获取条件失败');
        }       
        return array('code'=>0, 'msg'=>$proLevPlanList);
    }

    /**
     * 查找设置模板
     * @author   Nxx
     */
    public function getSetPages($setPages)
    {
        $setPages['status'] = 1;
        $pages = D("Setpages")->where($setPages)->select();
        return array('code'=>0,'msg'=>$pages);
    }


    /**
     * 添加设置模板
     * @author   Nxx
     */ 
    public function createSetPages($setPages)
    {
        $set['system_user_id'] = $setPages['system_user_id'];
        $set['pagesname'] = $setPages['pagesname'];
        $set['status'] = 1;
        $result = D("Setpages")->where($set)->find();   
        if ($result) {
            $error['code'] = 1;
            $error['msg'] = '模板名已存在';
            return $error;
        }       
        $set['type'] = $setPages['type'];
        if ($setPages['channel_id']) {
            $result = D("Setpages")->where("system_user_id = $set[system_user_id] and channel_id = $setPages[channel_id] and status=1 and type=$setPages[type]")->find();   
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
        $setpages_id = D("Setpages")->data($set)->add();
        if (!$setpages_id) {
            $error['code'] = 2;
            $error['msg'] = '模板添加失败';
            return $error;
        }   
        foreach ($setPages['sign'] as $key => $pages) {
            $arr[] = $pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
            $del = D("Setpages")->where("setpages_id = $setpages_id")->delete();
            $error['code'] = 3;
            $error['msg'] = '请不要重复选择表头';
            return $error;
        }   
        foreach ($setPages['sign'] as $key => $pages) {
            $pageInfo['pagehead'] = strtoupper($pages[0]);
            $pageInfo['headname'] = $pages[1];
            $pageInfo['setpages_id'] = $setpages_id;
            $result = D("Setpageinfo")->data($pageInfo)->add();
            if (!$result) {
                $updat = D("Setpages")->where("setpages_id = $setpages_id")->delete();
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
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfos($setpages_id)
    {
        $setpagesInfos = D("Setpageinfo")->where("setpages_id = $setpages_id")->order('pagehead')->select();
        return array('code'=>0,'msg'=>$setpagesInfos);
    }

    /**
     * 修改设置模板
     * @author   Nxx
     */
    public function editSetPages($setPages)
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
        D("Setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
        foreach ($setPages['sign'] as $key => $pages) {
            $pageInfo['pagehead'] = strtoupper($pages[0]);
            $pageInfo['headname'] = $pages[1];
            $pageInfo['setpages_id'] = $setPages['setpages_id'];
            $result = D("Setpageinfo")->data($pageInfo)->add();
            if (!$result) {
                D("Setpageinfo")->rollback();
                $updat = D("Setpages")->where("setpages_id = $setPages[setpages_id]")->delete();
                $error['code'] = 4;
                $error['msg'] = '模板表头设置失败';
                return $error;
            }
        }
        D("Setpageinfo")->commit();     
        $set['pagesname'] = $setPages['pagesname'];
        if ($setPages['channel_id']) {
            $set['channel_id'] = $setPages['channel_id'];
            $upda = D("Setpages")->where("setpages_id = $setPages[setpages_id] and status=1")->save($set);
            if ($upda === false) {
                $delInfo = D("Setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
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
    public function delSetPages($setPages)
    {
        $set['status'] = 0;
        $delInfo = D("Setpageinfo")->where("setpages_id = $setPages[setpages_id]")->delete();
        if ($delInfo === false) {
            return array('code'=>1,'msg'=>'失败');
        }
        $updateSetPages = D("Setpages")->where("setpages_id = $setPages[setpages_id] and status=1")->save($set);
        if ($updateSetPages === false) {
            return array('code'=>2,'msg'=>'失败');
        }
        return array('code'=>0,'msg'=>'成功');
    }

    /**
     * 查找设置模板详情
     * @author   Nxx
     */
    public function getSetPagesInfo($setpages_id)
    {
        $setPagesInfo = D("Setpageinfo")->where("setpages_id = $setpages_id")->select();
        return array('code'=>0,'msg'=>$setPagesInfo);
    }

    /**
     * 获取客服代码
     * @author Nixx
    */
    public function getServicecode($servicecode)
    {
        $servicecode = D("Servicecode")->where($servicecode)->find();
        return array('code'=>0,'msg'=>$servicecode);
    }

    /**
     * 获取计划详情
     * @author Nixx
    */
    public function getPromInfo($promote_id)
    {
        $proInfo = D("Promote")->where("promote_id = $promote_id and status=1")->find();
        if (!$proInfo) {
            return array('code'=>1,'msg'=>"获取详情失败");
        }
        return array('code'=>1,'msg'=>$proInfo);
    }

    /**
     * 获取指定计划
     * @author Nixx
    */
    public function getPromoteInfo($promote)
    {
        $promote['status'] = 1;
        $proInfo = D("Promote")->where($promote)->find();
        if (!$proInfo) {
            return array('code'=>1,'msg'=>'没有数据');
        }
        $proid = D("Proid")->where("proid_id = $proInfo[proid_id] and status=1")->find();
        if (!$proInfo['pcservice_id']) {
            $proInfo['pcservice_id'] = $proid['pcservice_id'];
        }
        if (!$proInfo['mservice_id']) {
            $proInfo['mservice_id'] = $proid['mservice_id'];
        }
        $pcservice = D("Servicecode")->where("servicecode_id = $proInfo[pcservice_id]")->find();
        $proInfo['pcservice'] = $pcservice['url'];
        $mservice = D("Servicecode")->where("servicecode_id = $proInfo[mservice_id]")->find();
        $proInfo['mservice'] = $mservice['url'];
        $proInfo['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=2";
        $proInfo['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=1";
        return array('code'=>0,'msg'=>$proInfo);
    }

    /**
     * 单个修改计划
     * @author Nixx
    */
    public function editPromote($promote)
    {
        
        $updatepromote = D("Promote")->where("promote_id = $promote[promote_id] and status=1")->save($promote);
        if ($updatepromote === false) {
            return array('code'=>1,'msg'=>'修改失败');
        }
        return array('code'=>0,'msg'=>'修改成功');
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
        $promoteList = D("Promote")->where($promote)->select(); 
        if (!$promoteList) {
            return array('code'=>1,'msg'=>'没有数据');
        }
        return array('code'=>0,'msg'=>$promoteList);
    }

    /**
     * 根据pro_lev_id获取所有计划
     * @author Nixx
    */
    public function getPromoteInfoByProlevid($pro_lev_id)
    {
        $prolev['status'] = 1;
        $prolev['pid'] = $pro_lev_id;
        $result = D("ProLev")->where($prolev)->select();    
        if ($result) {
            foreach ($result as $key => $res) {
                unset($res['proid_id']);
                unset($res['promote_id']);
                $rresult[] = $res;
            }
            return array('code'=>0,'msg'=>$rresult);
        }
        $prolev['pro_lev_id'] = $pro_lev_id;
        unset($prolev['pid']);      
        $result = D("ProLev")->where($prolev)->find();
        unset($result['proid_id']);
        unset($result['promote_id']); 
        $rresult[] = $result;
        if ($rresult) {
            return array('code'=>0,'msg'=>$rresult);
        }    
        return array('code'=>1,'msg'=>'没有数据');
    }

    /**
     * 获取指定计划
     * @author Nixx
    */
    public function getPromoteInfos($promote)
    {
        $promote['status'] = 1;
        $proInfos = D("Promote")->where($promote)->select();
        if (!$proInfos) {
            return array('code'=>0,'msg'=>'没有数据');
        }
        foreach ($proInfos as $key => $proInfo) {
            $proid = D("Proid")->where("proid_id = $proInfo[proid_id] and status=1")->find();
            if (!$proInfo['pcservice_id']) {
                $proInfo['pcservice_id'] = $proid['pcservice_id'];
            }
            if (!$proInfo['mservice_id']) {
                $proInfo['mservice_id'] = $proid['mservice_id'];
            }
            $pcservice = D("Servicecode")->where("servicecode_id = $proInfo[pcservice_id]")->find();
            $proInfo['pcservice'] = $pcservice['url'];
            $mservice = D("Servicecode")->where("servicecode_id = $proInfo[mservice_id]")->find();
            $proInfo['mservice'] = $mservice['url'];
            $proInfo['pc_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=2";
            $proInfo['m_pages'] = "{$proid['domain']}/Home/Propage/index.html?promote={$proInfo['promote_id']}&dev=1";
            $proInfos[$key] = $proInfo;
        }
        return array('code'=>0,'msg'=>$proInfos);
    }

    /**
     * 批量修改计划
     * @author Nixx
    */ 
    public function editPromoteInfo($promote)
    {
        if ($promote['pro_lev_id']) {
            if ($promote['mark'] == 1) {  //计划
                $prolevList = D("ProLev")->where("pid = $promote[pro_lev_id] and status=1")->select();
                foreach ($prolevList as $key => $plv) {
                    if (!$pro_lev_ids) {
                        $pro_lev_ids = $plv['pro_lev_id'];
                    }else{
                        $pro_lev_ids = $pro_lev_ids.",$plv[pro_lev_id]";
                    }
                }
                $where['pro_lev_id'] = array("IN", $pro_lev_ids);
                $where['status'] = 1;
                $proList = D("Promote")->where($where)->select();
            }elseif($promote['mark'] == 2){  //单元
                $proList = D("Promote")->where("pro_lev_id = $promote[pro_lev_id] and status=1")->select();
            }
            $includedString = $this->getPromoteidString($proList);      
            if (!$includedString) {
                return array('code'=>1,'msg'=>'没有数据');
            }
            $promotes['promote_id'] = array("IN", $includedString);
            $promotes['status'] = 1;
            unset($promote['pro_lev_id']);
            unset($promote['mark']);
            unset($promote['proid_id']);

            $updatepromote = D("Promote")->where($promotes)->save($promote);
            if ($updatepromote === false) {
                return array('code'=>2,'msg'=>'修改失败');
            }
        }else{
            $proid_id = $promote['proid_id'];
            $proid['pc_pages_id'] = $promote['pc_pages_id'];
            $proid['m_pages_id'] = $promote['m_pages_id'];
            $updatepromote = D("Promote")->where("proid_id = $proid_id")->save($proid);
            if ($updatepromote === false) {
                return array('code'=>3,'msg'=>'修改失败');
            }
        }
        return array('code'=>0,'msg'=>'修改成功');
    }

    /**
     * 获取推广账号
     * @author Nixx
    */
    public function getProList($pro = null, $limit='0,10')
    {
        $order = 'createtime desc';
        $getProList['data'] =  D("Proid")->where($pro)->order($order)->limit($limit)->select();
        $getProList['count'] =  D("Proid")->where($pro)->count();
        foreach ($getProList['data'] as $key => $pro) {
            $channe = D("Channel")->where("channel_id = $pro[channel_id]")->find();
            $getProList['data'][$key]['channelname'] = $channe['channelname'];
            if ($pro['pcservice_id']) {
                $pcservice = D("Servicecode")->where("servicecode_id = $pro[pcservice_id]")->find();
                $getProList['data'][$key]['pcservice'] = $pcservice['title'];
            }
            if ($pro['mservice_id']) {
                $mservice = D("Servicecode")->where("servicecode_id = $pro[mservice_id]")->find();
                $getProList['data'][$key]['mservice'] = $mservice['title'];
            }
        }   
        return array('code'=>0,'msg'=>$getProList);
    }

    /*
    * 拼接推广计划id字符串查询条件
    * @author Nixx
    */
    public function getPromoteidString($proList)
    {

        foreach($proList as $pros){
            if (!$includedString) {
                $includedString = "$pros[promote_id]";
            }else{
                $includedString = $includedString . ",$pros[promote_id]";
            }
        }
        return $includedString;
    }


}