<?php
/*
|--------------------------------------------------------------------------
| 推广相关接口
|--------------------------------------------------------------------------
| @author nxx
*/
namespace SystemApi\Controller;
use Common\Controller\SystemApiController;
use Common\Service\SystemProidService;

class ProidController extends SystemApiController
{

	/**
     * ****************************************************************
     * 客服代码列表
     * ****************************************************************
     * @author nxx
     */
    public function servJsList()
    {
        
        //获取数据
        $result = D('Proid', 'Service')->getOwnServicecode($own);
        //返回参数
        if($result['code']==0){
            $this->ajaxReturn(0,'获取成功',$result['data']);
        }
        $this->ajaxReturn($result['code'],$result['msg']);
    }

    /**
     * 添加客服代码
     * @author nxx
     */
    public function addServ()
    {
        $param['remark'] = I('param.remark', null);
        $param['servicecode'] = I('param.servicecode', null);
        $param['terminal_id'] = I('param.terminal_id', null);
        $param['title'] = I('param.title', null);
        $param['url'] = I('param.url', null);
        //去除数组空值
        $param = array_filter($param);

        $result = D('Proid', 'Service')->addServicecode($param);
        if($result['code'] != 0){
            $this->ajaxReturn($result['code'], $result['msg']);
        }else{
            $this->ajaxReturn(0, '添加成功', U('System/Proid/servJsList'));
        }

    }

    /**
     * 修改客服代码详情
     * @author nxx
     */
    public function editServ()
    {
        $servicecode_id = I('param.serv_id', null);
        $param['remark'] = I('param.remark', null);
        $param['servicecode'] = I('param.servicecode', null);
        $param['terminal_id'] = I('param.terminal_id', null);
        $param['title'] = I('param.title', null);
        $param['url'] = I('param.url', null);
        //去除数组空值
        $param = array_filter($param);
        
        $result = D('Proid', 'Service')->editServicecode($request,$servicecode_id);
        if($result['code'] != 0){
            $this->ajaxReturn($result['code'], $result['msg']);
        }else{
            $this->ajaxReturn(0, '添加成功', U('System/Proid/servJsList'));
        }
    }

    /**
     * 查看客服代码详情
     * @author nxx
     */
    public function detailServ()
    {
        $servicecode_id = I('param.serv_id', null);
        $res = D('Proid', 'Service')->detailServicecode(array('servicecode_id'=>$servicecode_id));
        $this->ajaxReturn($res['code'], $res['msg'], $res['data']);
    }

    /**
     * 删除客服代码
     * @author nxx
     */
    public function delServ()
    {
        $servicecode_id = I('param.serv_id', null);
        $requery['sign'] = 'del';
        $result = D('Proid', 'Service')->editServicecode($requery,$servicecode_id);
        $this->ajaxReturn($result['code'], $result['msg']);
    }


    /**
     * ****************************************************************
     * 专题页列表
     * ****************************************************************
     * @author nxx
     */
    public function pages()
    {
        //获取参数-过滤
        $request['page'] = I("param.page", null);
        $param['status'] = 1;
        //模板列表
        $pages = D('Proid', 'Service')->getAllPages('createtime desc',$re_page.',12',$param);
        $data['pagesAll'] = $pages['data'];
        //加载分页类
        $data['paging'] = $this->Paging($re_page,12,$data['pagesAll']['count'],$request);

    }

    /**
     * 添加专题页
     * @author nxx
     */
    public function addPages()
    {	
        $param['course_id'] = I('param.course_id', null);
        $param['image'] = I('param.image', null);
        $param['pagestype_id'] = I('param.pagestype_id', null);
        $param['subject'] = I('param.subject', null);
        $param['terminal_id'] = I('param.terminal_id', null);
        //去除数组空值
        $param = array_filter($param);
        
        $result = D('Proid', 'Service')->addPages($param);
        if($result['code'] != 0){
            $this->ajaxReturn($result['code'], $result['msg']);
        }else{
            $this->ajaxReturn(0,'专题页添加成功，可以添加导航，或者返回专题页列表',U('System/Proid/addPagesNav?pages_id='.$result['data']));
        }
    }

    /**
     * 添加模板 导航
     * @author nxx
     */
    public function addPagesNav()
    {
        $pages_id = I('param.pages_id', null);
        $param = I('param.navs', null);//去除数组空值
        $param = array_filter($param);
        
        $result = D('Proid', 'Service')->createPagesNav($param['navs'],$pages_id);
        if($result['code'] != 0){
            $this->ajaxReturn($result['code'], $result['msg']);
        }else{
            $this->ajaxReturn(0,'专题页导航添加成功', U('System/Proid/pages'));
        }
        
    }

    /**
     * 修改模板
     * @author nxx
     */
    public function editPages()
    {
        $pages_id = I('param.id', null);
        $param['course_id'] = I('param.course_id', null);
        $param['image'] = I('param.image', null);
        $param['pagestype_id'] = I('param.pagestype_id', null);
        $param['subject'] = I('param.subject', null);
        $param['terminal_id'] = I('param.terminal_id', null);
        //获取参数 验证
        $result = D('Proid', 'Service')->editPages($param,$pages_id);
        if($result['code'] != 0) {
            $this->ajaxReturn($result['code'], $result['msg']);
        }else{
            $this->ajaxReturn(0, '专题页修改成功，可以修改导航，或者返回专题页列表', U('System/Proid/addPagesNav?pages_id='.$pages_id));
        }
        
    }

    /**
     * 模板详情
     * @author nxx
     */
    public function detailPage()
    {
        $pages_id = I('param.id', null);
        $channel_id = I('param.channel_id', null);
        $re_page = I('param.page', 1);
        if(!empty($channel_id)){
            $where[C('DB_PREFIX').'channel.channel_id'] = $channel_id;
        }
        //模板详情
        $pageInfo = D('Proid', 'Service')->getPagesInfo(array('pages_id'=>$pages_id));
        $this->ajaxReturn($pageInfo['code'], $pageInfo['msg'], $pageInfo['data']);
    }

    /**
     * 模板 修改专题页备注
     * @author nxx
     */
    public function editPagesRemark(){
        $pages_id = I('param.pages_id', null);
        // $type = I('param.type',null);
        $remark = I('param.remark',null);
        $result = D('Proid', 'Service')->addPagesRemark($remark,$pages_id,$this->system_user_id);
        $this->ajaxReturn($result['code'], $result['msg']);
    }

    /**
     * 模板 删除
     * @author nxx
     */
    public function delPages(){
        $pages_id = I('param.pages_id',null);
        $type = I('param.type',null);
        // if( isset($type) && $type=='del' ){
        $request['status'] = 0;
        $result = D('Proid', 'Service')->editPages($request,$pages_id);
        $this->ajaxReturn($result['code'], $result['msg']);
        // }
    }

    /**
     * 获取指定类型模板
     * @author nxx
     */
    public function pagesList()
    {
        $re_type = I("post.type");
        if($re_type=='getlist'){
            $order = 'createtime desc';
            $re_page = I("post.page",1);
            $request['pagestype_id'] = I('post.pagesType_id');
            $request['terminal_id'] = I('post.terminal_id');
            $request['status'] = 1;
            if(!empty($request['key_name']) && !empty($request['key_val'])){
                $request[$request['key_name']] = '%%'.$request['key_val'];
            }
            unset($request['page']);
            unset($request['key_name']);
            unset($request['key_val']);

            $result= D('Proid', 'Service')->getAllPages($order,$re_page.',8',$request);
            $pagesList = $result['data'];
            if ($pagesList['code'] != 0) {
                $this->ajaxReturn($pagesList['code'], $pagesList['msg']);
            }
            $pagesList['paging'] = $this->Paging($re_page,8,$pagesList['count'],'','javascript:pagesList(',1);
            $this->ajaxReturn(0, '数据获取成功', $pagesList);
        }else{
            $request['pagestype_id'] = I('post.pagesType_id');
            $request['terminal_id'] = I('post.terminal_id');
            $request['status'] = 1;
            if(!empty($request['key_name']) && !empty($request['key_val'])){
                $request[$request['key_name']] = '%%'.$request['key_val'];
            }
            unset($request['page']);
            unset($request['key_name']);
            unset($request['key_val']);
            $result= D('Proid', 'Service')->getAllPages(null, null, $request);
            if (empty($result['data'])) {
                $this->ajaxReturn(201, '模板获取失败');
            }
            $pagesList = $result['data'];
            foreach ($pagesList['data'] as $key => $value) {
                $where['pages_id'] = $value['pages_id'];
                $remark = D('Proid', 'Service')->getRemark($where);
                if ($remark['data']['remark']) {
                    $pagesList['data'][$key]['remark'] = $remark['data']['remark'];
                }else{
                    $pagesList['data'][$key]['remark'] = '空';
                }
            }
            $this->ajaxReturn(0, '数据获取成功', $pagesList);
        }

    }

    /**
     * 终端 快捷操作(删除/修改)-ajax
     * @author nxx
     */
    public function disposTermin()
    {
        if(IS_POST) {
            $terminal_id = I('post.terminal_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $result = D('Proid', 'Service')->delTerminal($terminal_id);
                $this->ajaxReturn($result['code'], $result['msg']);
            }else if( isset($type) && $type=='addEdit' ){
                $request['addTerminal'] = I('post.addTerminal');
                $request['editTerminal'] = I('post.editTerminal');
                $result = D('Proid', 'Service')->operateTerminal($request);
                $this->ajaxReturn($result['code'], $result['msg']);           
            }
        }
    }

    /**
     * 模板分类  快捷操作(删除/修改)-ajax
     * @author nxx
     */
    public function dispostPagesType()
    {
        if(IS_POST) {
            $pagestype_id = I('post.pagestype_id');
            $type = I('post.type',null);
            if( $type=='del' ){
                $request['status'] = 0;
                $result = D('Proid', 'Service')->editPagesType($request,$pagestype_id);
                $this->ajaxReturn($result['code'], $result['msg']);
            }else if( $type=='addEdit' ){
                $request['addPagesType'] = I('post.addPagesType');
                $request['editPagesType'] = I('post.editPagesType');
                $result = D('Proid', 'Service')->operatePagesType($request);
                $this->ajaxReturn($result['code'], $result['msg']);

                
            }
        }
    }

    /**
     * 推广账号管理
     * @author nxx
     */
    public function id()
    {
        $result = D('Proid', 'Service')->proidList($pro,'0,100');
        $this->ajaxReturn($result['code'], $result['msg'], $result['data']);
    }

    /**
     * 添加推广账号
     * @author nxx
     */
    public function addPro()
    {
        $proid['accountname'] = I("param.accountname", null);
        $proid['channel_id'] = I("param.channel_id", null);
        $proid['domain'] = I("param.domain", null);
        $proid['totalcode'] = I("param.totalcode", null);
        $proid['pcservice_id'] = I("param.pcservice_id", null);
        $proid['mservice_id'] = I("param.mservice_id", null);
        $proid['pcoffcode'] = I("param.pcoffcode", null);
        $proid['moffcode'] = I("param.moffcode", null);
        $proid['remark'] = I("param.remark", null);
        $result = D('Proid', 'Service')->createProid($proid);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'],$result['msg']);
        }
        $this->ajaxReturn(0, '创建成功', U('System/Proid/id'));

    }

    /**
     *修改推广账号
     * @author nxx
     */
    public function editPro()
    {
        $proid['proid_id'] = I("param.proid_id", null);
        $proid['channel_id'] = I("param.channel_id", null);
        $proid['accountname'] = I("param.accountname", null);
        $proid['domain'] = I("param.domain", null);        
        $proid['totalcode'] = I("param.totalcode", null);
        $proid['pcservice_id'] = I("param.pcservice_id", null);
        $proid['mservice_id'] = I("param.mservice_id", null);
        $proid['pcoffcode'] = I("param.pcoffcode", null);
        $proid['moffcode'] = I("param.moffcode", null);
        $proid['remark'] = I("param.remark", null);
        $backInfo = D('Proid', 'Service')->editProid($proid);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
        }
        $this->ajaxReturn(0, '修改成功', U('System/Proid/id'));
    }

    /**
     * 推广账号详情
     * @author nxx
     */
    public function proidInfo()
    {   
        $proid_id = I("param.proid_id", null);
        $proInfo = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid_id));
       	$this->ajaxReturn($proInfo['code'], $proInfo['msg'], $proInfo['data']);
    }

    /**
     * 删除推广账号
     * @author nxx
     */
    public function delProid()
    {
        $proid_id = I('param.proid_id', null);
        $backInfo = D('Proid', 'Service')->deleteProid($proid_id);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
        }
        $this->ajaxReturn(0, '删除成功', U('System/Proid/id'));
    }

    /**
     * 计划列表
     * @author nxx
     */
    public function index()
    {
        $proid['proid_id'] = I("param.proid_id");    
        $re_page = I("param.page",1);
        $promote['proid_id'] = $proid['proid_id'];
        $promote['pro_lev_id'] = I("param.pro_lev_id");
        $promote['key_name'] = I("param.key_name");
        $promote['key_val'] = I("param.key_val");
        // foreach ($pro as $key => $value) {
        //     if (empty($value)) {
        //         unset($pro[$key]);
        //     }
        //     if ($pro['pro_lev_id']) {
        //         $prolevList = D('Proid', 'Service')->getProLevPlanunitList(array('pro_lev_id'=>$pro['pro_lev_id']));
        //         $result = D('Proid', 'Service')->getIdString($prolevList['data']);
        //         $idString = $result['data'];
        //         $promote['pro_lev_id'] = array("IN", $idString);
        //     }
        //     if (!$pro['key_name'] && $pro['key_val']) {
        //         $this->ajaxReturn(301, '请选择搜索类型');
        //     }
        //     if ($pro['key_name'] == 'promote_id') {
        //         $promote['promote_id'] = $pro['key_val'];
        //     }elseif ($pro['key_name'] == 'keyword') {
        //         $promote['keyword'] = array('like', "%{$pro['key_val']}%" );
        //     }
        // }
        $promList = D('Proid', 'Service')->getPromoteList($promote,(($re_page-1)*15).',15');
        $data['promList'] = $promList['data'];
        //加载分页类
        $data['paging'] = $this->Paging($re_page,15,$promList['data']['count'],$proid);
        $this->ajaxReturn(0, '', $data);

    }

    /**
     * 添加推广计划
     * @author nxx
     */
    public function addPromote()
    {
        $promote['proid_id'] = I("param.proid_id", null);
        $promote['plan'] = I("param.plan", null);
        $promote['planunit'] = I("param.planunit", null);
        $promote['keyword'] = I("param.keyword", null);
        $promote['pcservice_id'] = I("param.pcservice_id", null);
        $promote['mservice_id'] = I("param.mservice_id", null);
        $promote['pc_pages_id'] = I("param.pcPageid", null);
        $promote['m_pages_id'] = I("param.mPageid", null);
        
        $reback = D('Proid', 'Service')->createPromote($promote);
        if($reback['code'] != 0) {
            $this->ajaxReturn($reback['code'], $reback['msg']);
        }
        $this->ajaxReturn(0, '添加计划成功', U('System/Proid/index', array('proid_id'=>$promote['proid_id'])));

    }

    /**
     * 修改计划
     * @author nxx
     */
    public function editPromote()
    {
        $promote['promote_id'] = I("param.promote_id", null);
        $promote['pc_pages_id'] = I("param.pc_pages_id", null);
        $promote['m_pages_id'] = I("param.m_pages_id", null);
        $promote['pcservice_id'] = I("param.pcservice_id", null);
        $promote['mservice_id'] = I("param.mservice_id", null);
        $backInfo = D('Proid', 'Service')->editPromote($promote);
        if($backInfo['code'] != 0){
            $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
        }
        $this->ajaxReturn(0, '修改成功', U('System/Proid/index', array('proid_id'=>$promoteInfo['proid_id'])));
        
        
    }


    /**
     * 批量修改计划
     * @author nxx
     */
    public function editPromoteList()
    {
        $promote['proid_id'] = I("param.proid_id", null);
        $promote['pro_lev_id'] = I("param.pro_lev_id", null);
        $promote['pc_pages_id'] = I("param.pc_pages_id", null);
        $promote['m_pages_id'] = I("param.m_pages_id", null);
        $promote['mark'] = I("param.mark");//2-单元  1-计划
        
        $backInfo = D('Proid', 'Service')->editPromoteInfo($promote);
        if($backInfo['code'] != 0){
            $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
        }
        $this->ajaxReturn(0, '修改成功', U('System/Proid/index',array('proid_id'=>$promote['proid_id'])));
       
    }

    /**
     * 删除推广计划
     * @author nxx
     */
    public function delPro()
    {
        $prom['promote_id'] = I('param.promote_id', null);
        $proid_id = I('param.proid_id', null);
        $backInfo = D('Proid', 'Service')->deletePro($prom);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'], '删除失败');
        }
        $this->ajaxReturn(0, '删除成功', U('System/Proid/index',array('proid_id'=>$proid_id)));
    }

    /**
     * 获取推广计划id
     * @author nxx
     */
    public function prolevPlanList()
    {
        $proid_id = I('param.proid_id', null);
        $res = D('Proid', 'Service')->getProLevPlanList($proid_id);
        if ($res['code']!=0) {
            $this->ajaxReturn($res['code'], '该账号下尚未添加计划');
        } 
        $this->ajaxReturn(0, '数据获取成功', $res['data']);

    }

    /**
     * 获取推广计划单元id
     * @author nxx
     */
    public function prolevPlanunitList()
    {
        $pro_lev_id = I('param.pro_lev_id', null);
        $res = D('Proid', 'Service')->getProLevPlanunitList(array('pid'=>$pro_lev_id));
        if ($res['code']!=0) {
            $this->ajaxReturn($res['code'], '获取失败');
        }  
        $this->ajaxReturn(0, '数据获取成功', $res['data']);

    }


    /**
     * 推广计划导入模板列表页
     * @author 
     */
    public function setPages()
    {   
        $setPages['type'] = 1;
        $res = D('Proid', 'Service')->getSetPages($setPages);
        $this->ajaxReturn($res['code'], $res['msg'], $res['data']);

    }

    /**
     * 添加模板
     * @author 
     * 
     */
    public function addSetTemplate()
    {   	
	
        $proid_id = I("param.proid_id", null);
        $param['pagesname'] = I("param.pagesname", null);
        $param['sign'] = I("param.sign", null);
        $param['type'] = 1;
        $result = D('Proid', 'Service')->createSetPages($param);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'], $result['msg']);
        }
        $this->ajaxReturn(0, '设置模板成功', U('System/Proid/setPages', array('proid_id' => $proid_id))); 
       

    }

    /**
     * 删除模板
     * @author 
     * 
     */
    public function delSetPages()
    {   
        $proid_id = I("param.proid_id", null);
        $setpages['setpages_id'] = I("param.setpages_id", null);
        $backInfo = D('Proid', 'Service')->delSetPages($setpages);
        if ($backInfo['code'] == 0) {
            $this->ajaxReturn(0, '删除成功', U('System/Proid/setPages', array('proid_id' => $proid_id)));
        }
        $this->ajaxReturn($backInfo['code'],$backInfo['msg']);

    }

    /**
     * 修改模板
     * @author 
     * 
     */
    public function editSetTemplate()
    {   
        $proid_id = I("param.proid_id", null);
        $setpages_id = I("param.setpages_id", null);
        $param['pagesname'] = I("param.pagesname", null);
    	$param['sign'] = I("param.sign", null);
        $setpages['setpages_id'] = $setpages_id;
        
        $result = D('Proid', 'Service')->editSetPages($setpages);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result['code'], $result['msg']);
        }
        $this->ajaxReturn(0, '修改模板成功', U('System/Proid/setPages', array('proid_id' => $proid_id))); 
    }

    /**
     * 导入计划
     * @author nxx
     */
    public function inputPlan()
    {
        $proid_id = I("param.proid_id", null); 
        $request['setpages_id'] = I('param.setpages_id', null);
        $request['pcPagesType_id'] = I('param.pcPagesType_id', null);
        $request['mPagesType_id'] = I('param.mPagesType_id', null);
        if (!empty($_FILES['file'])) {
            $exts = array('xls','xlsx');
            $rootPath = './Public/';
            $savePath = 'promote/';
            $uploadFile = $this->uploadFile($exts,$rootPath,$savePath);
            $filename = $rootPath.$uploadFile['file']['savepath'].$uploadFile['file']['savename'];
        }
        $datas = importExecl($filename);  
        unlink($filename);
        $setPagesInfo = D('Proid', 'Service')->getSetPagesInfo($request['setpages_id']);
        unset($request['setpages_id']);
        $letters = $setPagesInfo['data'];           
        foreach ($letters as $k1 => $letter) {
            $k1 = $k1+1;
            $pro[$k1][] = $letter['pagehead'];
            $pro[$k1][] = $letter['headname'];
            $errorIfm[] = $letter['headname'];
        }
        if (in_array('plan', $errorIfm) && !in_array('planunit', $errorIfm) || !in_array('plan', $errorIfm) && in_array('planunit', $errorIfm)) {
            $this->error('模板设置表头时,计划和单元必须同时存在');
        }
        $result = D('Proid', 'Service')->inputPlan($datas, $pro, $request, $proid_id);
        $this->redirect('/System/Proid/inputClient', array('proid_id'=>$proid_id));
        
    }

    // /**
    //  * [inputClient description]
    //  * @return [type] [description]
    //  */
    // public function inputClient()
    // {
    //     $faile = session('faile_input');
    //     $success = session('success_input');        
    //     $newPromoteList = session('newPromoteList');      
    //     $letters = session('letters'); 
    //     $proid_id = I("get.proid_id");
    //     $excel_key_value = array(
    //         'plan'=>'推广计划名称',
    //         'planunit'=>'推广单元名称',
    //         'keyword'=>'关键词名称',
    //         'pc_pages'=>'PC模板',
    //         'm_pages'=>'移动模板',
    //     );        
    //     $letters = array_keys($letters);
    //     $filename = "proPlan";
    //     outExecl($filename,array_values($excel_key_value),$newPromoteList,$letter);
    //     $successCount = count($success);
    //     $faileCount = count($faile);
    //     $this->assign('faile', $faile);
    //     $this->assign('success', $success);
    //     $this->assign('successCount', $successCount);
    //     $this->assign('faileCount', $faileCount);
    //     $this->assign('proid_id', $proid_id);
    //     $this->display();
    // }

    /**
     * 导出计划
     * @author nxx
     */
    public function outPlan()
    {
        $proid_id = I('param.proid_id', null);
        $request['keyword'] = I('param.keyword', null);
        $request['pro_lev_id'] = I('param.pro_lev_id', null);
        $request['pro_lev_ids'] = I('param.pro_lev_ids', null);
        //去除数组空值
        $param = array_filter($param);

        $result = D('Proid', 'Service')->outputPlan($request, $proid_id);
        if($result['code']==0){
            return $result['data'];
        }else{
            $this->ajaxReturn($result['code'], $result['msg']);
        }
    }


}