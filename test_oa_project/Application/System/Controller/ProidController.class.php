<?php
namespace System\Controller;
use Common\Controller\SystemController;
class ProidController extends SystemController

{
    /**
     * ****************************************************************
     * 客服代码列表
     * ****************************************************************
     * @author zgt
     */
    public function servJsList()
    {

        //获取参数
        $request = I('get.');
        //获取排序 分页参数
        $re_page = isset($request['page'])?$request['page']:1;
        unset($request['page']);
        //整理排序参数
        $order = "servicecode_id desc";
        foreach($request as $k=>$v){
            $order = $k.' '.$v;
        }
        //排序URL
        if($request['servicecode_id']=='asc'){
            $data['url_servicecode_id'] = U('System/Proid/servJsList').'?servicecode_id=desc';
        } else {
            $data['url_servicecode_id'] = U('System/Proid/servJsList').'?servicecode_id=asc';
        }
        //获取数据
        $result = D('Proid', 'Service')->getOwnServicecode($own);
        
        $data['servicecodeAll']['data'] = $result['data'];
        $data['url_addServ'] = U('System/Proid/addServ');
        $data['url_editServ'] = U('System/Proid/editServ');
        $data['url_detailServ'] = U('System/Proid/detailServ');
        $data['url_delServ'] = U('System/Proid/delServ');
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加客服代码
     * @author zgt
     */
    public function addServ()
    {
        if(IS_POST){
            $requery = I('post.');
            $result = D('Proid', 'Service')->addServicecode($requery);
            if($result['code'] != 0){
                $this->ajaxReturn($result['code'], $result['msg']);
            }else{
                $this->ajaxReturn(0, '添加成功', U('System/Proid/servJsList'));
            }
        }else{
            //终端列表
            $res = D('Proid', 'Service')->getAllTerminal();
            $data['terminalAll'] = $res['data'];
            $data['url_servJsList'] = U('System/Proid/servJsList');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 修改客服代码详情
     * @author zgt
     */
    public function editServ()
    {
        $servicecode_id = I('get.serv_id',null);
        if(IS_POST){
            $request = I('post.');
            $result = D('Proid', 'Service')->editServicecode($request,$servicecode_id);
            //判断返回数据
            if($result['code'] != 0){
                $this->ajaxReturn($result['code'], $result['msg']);
            }else{
                $this->ajaxReturn(0, '修改成功', U('System/Proid/servJsList'));
            }
        }else{
            //终端列表
            $res = D('Proid', 'Service')->getAllTerminal();
            $data['terminalAll'] = $res['data'];
            $data['url_servJsList'] = U('System/Proid/servJsList');
            $servicecodeInfo = D('Proid', 'Service')->detailServicecode(array('servicecode_id'=>$servicecode_id));
            if ($servicecodeInfo['code'] != 0) {
                $this->ajaxReturn($servicecodeInfo['code'], $servicecodeInfo['msg']);
            }
            $data['Servicecode'] = $servicecodeInfo['data'];
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 查看客服代码详情
     * @author zgt
     */
    public function detailServ()
    {
        $servicecode_id = I('get.serv_id',null);
        $res = D('Proid', 'Service')->detailServicecode(array('servicecode_id'=>$servicecode_id));
        if ($res['code'] != 0) {
            $this->ajaxReturn($res['code'], $res['msg']);
        }
        $data['Servicecode'] = $res['data'];
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 删除客服代码
     * @author zgt
     */
    public function delServ()
    {
        $servicecode_id = I('post.serv_id',null);
        $requery['sign'] = 'del';
        $result = D('Proid', 'Service')->editServicecode($requery,$servicecode_id);
        $this->ajaxReturn($result['code'], $result['msg']);
    }


    /**
     * ****************************************************************
     * 专题页列表
     * ****************************************************************
     * @author zgt
     */
    public function pages()
    {
        //获取参数-过滤
        $request = I("get.");
        $re_page = isset($request['page'])?$request['page']:1;
        if(!empty($request['key_name']) && !empty($request['key_val'])){
            $request[$request['key_name']] = '%%'.$request['key_val'];
        }
        unset($request['page']);
        $data['request'] = $request;
        unset($request['key_name']);
        unset($request['key_val']);
        $request['status'] = 1;
        //模板列表
        $pages = D('Proid', 'Service')->getAllPages('createtime desc',$re_page.',12',$request);
        $data['pagesAll'] = $pages['data'];
        foreach($data['pagesAll']['data'] as $k=>$v){
            $remark = D('Proid', 'Service')->getPagesRemark($v['pages_id'],$this->system_user_id);
            if(!empty($remark['data'])) {
                $data['pagesAll']['data'][$k]['remark'] = $remark['data']['remark'];
            }
        }

        //模板分类列表
        $pagesType = D('Proid', 'Service')->getAllPagesType();
        $data['pagesType'] = $pagesType['data'];
        //课程列表
        $courseAll = D('Course', 'Service')->getCourseList();
        $data['courseAll'] = $courseAll['data']['data'];
        //终端列表
        $terminalAll = D('Proid', 'Service')->getAllTerminal();
        $data['terminalAll'] = $terminalAll['data'];
        //加载分页类
        $data['paging'] = $this->Paging($re_page,12,$data['pagesAll']['count'],$data['request']);
        $data['url_add'] = U('System/Proid/addPages');
        $data['url_edit'] = U('System/Proid/editPages');
        $data['url_del'] = U('System/Proid/delPages');
        $data['url_editRemark'] = U('System/Proid/editPagesRemark');
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加专题页
     * @author zgt
     */
    public function addPages()
    {
        if(IS_POST){
            $request = I('post.');
            $result = D('Proid', 'Service')->addPages($request);
            if($result['code'] != 0){
                $this->ajaxReturn($result['code'], $result['msg']);
            }else{
                $this->ajaxReturn(0,'专题页添加成功，可以添加导航，或者返回专题页列表',U('System/Proid/addPagesNav?pages_id='.$result['data']));
            }
        }else{
            //专题页分类列表
            $pagesType = D('Proid', 'Service')->getAllPagesType();
            $data['pagesType'] = $pagesType['data'];
            //课程列表
            $courseAll = D('Course', 'Service')->getCourseList();
            $data['courseAll'] = $courseAll['data']['data'];
            //终端列表
            $terminalAll = D('Proid', 'Service')->getAllTerminal();
            $data['terminalAll'] = $terminalAll['data'];
            $data['url_pages'] = U('System/Proid/pages');
            $data['url_disposTermin'] = U('System/Proid/disposTermin');
            $data['url_dispostPagesType'] = U('System/Proid/dispostPagesType');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 添加专题页 导航
     * @author zgt
     */
    public function addPagesNav()
    {
        $pages_id = I('get.pages_id');
        if(IS_POST){
            $request = I('post.');
            $result = D('Proid', 'Service')->createPagesNav($request['navs'],$pages_id);
            if($result['code'] != 0){
                $this->ajaxReturn($result['code'], $result['msg']);
            }else{
                $this->ajaxReturn(0,'专题页导航添加成功', U('System/Proid/pages'));
            }
        }else{
            $detail = D('Proid', 'Service')->getPagesNav($pages_id);
            $data['detail'] = $detail['data'];
            //专题页分类列表
            $pagesType = D('Proid', 'Service')->getAllPagesType();
            $data['pagesType'] = $pagesType['data'];
            //课程列表
            $courseAll = D('Course', 'Service')->getCourseList();
            $data['courseAll'] = $courseAll['data']['data'];
            //终端列表
            $terminalAll = D('Proid', 'Service')->getAllTerminal();
            $data['terminalAll'] = $terminalAll['data'];
            $data['url_pages'] = U('System/Proid/pages');
            $data['pages_id'] = $pages_id;
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 修改专题页
     * @author zgt
     */
    public function editPages()
    {
        $pages_id = I('get.id');
        if(IS_POST){
            //获取参数 验证
            $request = I('post.');
            $result = D('Proid', 'Service')->editPages($request,$pages_id);
            if($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }else{
                $this->ajaxReturn(0, '专题页修改成功，可以修改导航，或者返回专题页列表', U('System/Proid/addPagesNav?pages_id='.$pages_id));
            }
        }
        //专题页详情
        $pagesInfo = D('Proid', 'Service')->getPagesInfo(array('pages_id'=>$pages_id));
        $data['pagesInfo'] = $pagesInfo['data'];
        //专题页分类列表
        $pagesType = D('Proid', 'Service')->getAllPagesType();
        $data['pagesType'] = $pagesType['data'];
        //课程列表
        $courseAll = D('Course', 'Service')->getCourseList();
        $data['courseAll'] = $courseAll['data']['data'];
        //终端列表
        $terminalAll = D('Proid', 'Service')->getAllTerminal();
        $data['terminalAll'] = $terminalAll['data'];
        $data['url_pages'] = U('System/Proid/pages');
        $data['url_disposTermin'] = U('System/Proid/disposTermin');
        $data['url_dispostPagesType'] = U('System/Proid/dispostPagesType');
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 专题页详情
     * @author zgt
     */
    public function detailPage()
    {
        $pages_id = I('get.id');
        $channel_id = I('get.channel_id',null);
        $re_page = I('get.page',1);
        if(!empty($channel_id)){
            $where[C('DB_PREFIX').'channel.channel_id'] = $channel_id;
        }
        //专题页详情
        $pageInfo = D('Proid', 'Service')->getPagesInfo(array('pages_id'=>$pages_id));
        $data['pagesInfo'] = $pageInfo['data'];
        //专题页相关计划列表
        $proList = D('Proid', 'Service')->getPagesPromote($pages_id, $where,(($re_page-1)*15).',15');
        $data['promoteList'] = $proList['data'];
        //终端列表
        $terminalAll = D('Proid', 'Service')->getAllTerminal();
        $data['terminalAll'] = $terminalAll['data'];
        //渠道列表
        $channelList = D('Channel', 'Service')->getChannelList();
        $data['channel'] = $channelList['data'];
        //加载分页类
        $data['paging'] = $this->Paging($re_page,15,$data['promoteList']['count'],array('id'=>$pages_id));
        $data['url_pages'] = U('System/Proid/pages');
        $this->assign('data', $data);
        $this->display();

    }

    /**
     * 专题页 修改专题页备注
     * @author zgt
     */
    public function editPagesRemark(){
        if(IS_POST) {
            $pages_id = I('post.pages_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='remark' ){
                $request['remark'] = I('post.remark');
                $result = D('Proid', 'Service')->addPagesRemark($request['remark'],$pages_id,$this->system_user_id);
                $this->ajaxReturn($result['code'], $result['msg']);
            }
        }
    }

    /**
     * 专题页 删除
     * @author zgt
     */
    public function delPages(){
        if(IS_POST) {
            $pages_id = I('post.pages_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $request['status'] = 0;
                $result = D('Proid', 'Service')->editPages($request,$pages_id);
                $this->ajaxReturn($result['code'], $result['msg']);
                
            }
        }
    }

    /**
     * 获取指定类型专题页
     * @author Nixx
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
     * @author zgt
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
     * @author zgt
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
     * @author Nixx
     */
    public function id()
    {
        $request = I("get.");
        unset($request['page']);
        foreach ($request as $key => $req) {
            if ($req) {
                $pro[$key] = $req;
            }
        }
        $pro['status'] = 1;
        $result = D('Proid', 'Service')->proidList($pro,'0,100');
        $data['proList'] = $result['data'];
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加推广账号
     * @author Nixx
     */
    public function addPro()
    {
        $pc['terminal_id'] = 2;
        $pcser = D('Proid', 'Service')->getOwnServicecode($pc);
        if ($pcser['code'] != 0) {
            $this->error('么有PC客服代码,请先添加');
        }else{
            $pcserviceList['data'] = $pcser['data'];
        }
        $m['terminal_id'] = 1;
        $mser = D('Proid', 'Service')->getOwnServicecode($m);
        if ($mser['code'] != 0) {
            $this->error('么有移动客服代码,请先添加');
        }else{
            $mserviceList['data'] = $mser['data'];
        }
        if(IS_POST) {
            $proid['accountname'] = I("post.accountname");
            $proid['channel_id'] = I("post.channel_id");
            $proid['domain'] = I("post.domain");
            $proid['totalcode'] = I("post.totalcode");
            $proid['pcservice_id'] = I("post.pcservice_id");
            $proid['mservice_id'] = I("post.mservice_id");
            $proid['pcoffcode'] = I("post.pcoffcode");
            $proid['moffcode'] = I("post.moffcode");
            $proid['remark'] = I("post.remark");
            $result = D('Proid', 'Service')->createProid($proid);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'],$result['msg']);
            }
            $this->ajaxReturn(0, '创建成功', U('System/Proid/id'));
        }
        $res = D('Channel', 'Service')->getChannelList();
        if (!$res['data']){
            $this->error('没有渠道可供选择,请先添加');
        }
        $result = D('Proid', 'Service')->proidList($pro);
        $proList = $result['data'];
        $proList['channelList'] = $res['data'];
        $this->assign('pcserviceList', $pcserviceList);
        $this->assign('mserviceList', $mserviceList);
        $this->assign('proList', $proList);
        $this->display();


    }

    /**
     *修改推广账号
     * @author Nixx
     */
    public function editPro()
    {
        $proid_id = I("get.proid_id");
        if(IS_POST) {
            $proid['proid_id'] = $proid_id;
            $proid['channel_id'] = I("post.channel_id");
            $proid['accountname'] = I("post.accountname");
            $proid['domain'] = I("post.domain");        
            $proid['totalcode'] = I("post.totalcode");
            $proid['pcservice_id'] = I("post.pcservice_id");
            $proid['mservice_id'] = I("post.mservice_id");
            $proid['pcoffcode'] = I("post.pcoffcode");
            $proid['moffcode'] = I("post.moffcode");
            $proid['remark'] = I("post.remark");
            $backInfo = D('Proid', 'Service')->editProid($proid);
            if ($backInfo['code'] != 0) {
                $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
            }
            $this->ajaxReturn(0, '修改成功', U('System/Proid/id'));
        }
        $channelAll = D('Channel', 'Service')->getChannelList();
        if (empty($channelAll['data'])) {
            $this->ajaxReturn($channelAll['code'], '没有渠道可供选择');
        }
        $result = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid_id));
        $pc['terminal_id'] = 2;
        $m['terminal_id'] = 1;
        $pcser= D('Proid', 'Service')->getOwnServicecode($pc);
        $pcserviceList['data']= $pcser['data'];
        $pcserviceList['pcservice_id'] = $result['data']['pcservice_id'];
        $mser = D('Proid', 'Service')->getOwnServicecode($m);
        $mserviceList['data']= $mser['data'];
        $mserviceList['mservice_id'] = $result['data']['mservice_id'];
        
        $proList['data'] = $result['data'];
        $proList['channelList'] = $channelAll['data'];

        $this->assign('proid_id',$proid_id);
        $this->assign('proList', $proList);
        $this->assign('pcserviceList', $pcserviceList);
        $this->assign('mserviceList', $mserviceList);
        $this->display();
    }

    /**
     * 推广账号详情
     * @author Nixx
     */
    public function proidInfo()
    {   
        $proid_id = I("get.proid_id");
        $proInfo = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid_id));
        if (empty($proInfo['data'])) {
            $this->ajaxReturn(201, '暂无数据');
        }
        $this->assign('proid_id', $proid_id);
        $this->assign('proInfo', $proInfo['data']);
        $this->display();
    }

    /**
     * 删除推广账号
     * @author Nixx
     */
    public function delProid()
    {
        $proid_id = I('get.proid_id');
        $backInfo = D('Proid', 'Service')->deleteProid($proid_id);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
        }
        $this->success('删除成功', 2, U('System/Proid/id'));
    }

    /**
     * 计划列表
     * @author Nixx
     */
    public function index()
    {
        $proid['proid_id'] = I("get.proid_id");    
        $re_page = I("get.page",1);
        $promote['proid_id'] = $proid['proid_id'];
        $promote['status'] = 1;
        if (IS_POST) {
            $pro['pro_lev_id'] = I("post.pro_lev_id");
            $pro['key_name'] = I("post.key_name");
            $pro['key_val'] = I("post.key_val");
            foreach ($pro as $key => $value) {
                if (empty($value)) {
                    unset($pro[$key]);
                }
                if ($pro['pro_lev_id']) {
                    $prolevList = D('Proid', 'Service')->getProLevPlanunitList(array('pro_lev_id'=>$pro['pro_lev_id']));
                    $result = D('Proid', 'Service')->getIdString($prolevList['data']);
                    $idString = $result['data'];
                    $promote['pro_lev_id'] = array("IN", $idString);
                }
                if (!$pro['key_name'] && $pro['key_val']) {
                    $this->ajaxReturn(301, '请选择搜索类型');
                }
                if ($pro['key_name'] == 'promote_id') {
                    $promote['promote_id'] = $pro['key_val'];
                }elseif ($pro['key_name'] == 'keyword') {
                    $promote['keyword'] = array('like', "%{$pro['key_val']}%" );
                }
            }
        }     
        $promList = D('Proid', 'Service')->getPromoteList($promote,(($re_page-1)*15).',15');
        //加载分页类
        $data['paging'] = $this->Paging($re_page,15,$promList['data']['count'],$proid);
        $data['proid_id'] = $proid['proid_id'];
        $proidInfo = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid['proid_id']));
        $this->assign('proid_id', $proid['proid_id']);   
        $this->assign('promoteList', $promList['data']);  
        $this->assign('data', $data);
        $this->assign('proidInfo', $proidInfo['data']);
        $this->assign('urlDelPromote', U("System/Proid/delPro"));
        $this->display();

    }

    /**
     * 添加推广计划
     * @author Nixx
     */
    public function addPromote()
    {
        $promote['proid_id'] = I("get.proid_id");
        if (IS_POST) {
            $promote['plan'] = I("post.plan");
            $promote['planunit'] = I("post.planunit");
            $promote['keyword'] = I("post.keyword");
            $promote['pcservice_id'] = I("post.pcservice_id");
            $promote['mservice_id'] = I("post.mservice_id");
            $promote['pc_pages_id'] = I("post.pcPageid");
            $promote['m_pages_id'] = I("post.mPageid");
            $proidInfo = D('Proid', 'Service')->getProInfo(array('proid_id'=>$promote['proid_id']));
            if (!$promote['pcservice_id']) {
                $promote['pcservice_id'] = $proidInfo['data']['pcservice_id'];
            }
            if (!$promote['mservice_id']) {
                $promote['mservice_id'] = $proidInfo['data']['mservice_id'];
            }
            $prolev['proid_id'] = $promote['proid_id'];
            if ($promote['plan'] && $promote['planunit']) {
                $prolev['name'] = $promote['plan'];
                $proLevInfo = D('Proid', 'Service')->getProLevInfo($prolev);
                if ($proLevInfo['code'] != 0) {
                    $result = D('Proid', 'Service')->createProLev($prolev);
                    $prolev['name'] = $promote['planunit'];
                    $prolev['pid'] = $result['data'];
                    $result = D('Proid', 'Service')->createProLev($prolev);
                    $prolev['pid'] = 0; //重置pid为0
                    $promote['pro_lev_id'] = $result['data'];
                }else{
                    $prolev['name'] = $promote['planunit'];
                    $prolev['pid'] = $proLevInfo['data']['pro_lev_id'];
                    $punitLevInfo = D('Proid', 'Service')->getProLevInfo($prolev);
                    if ($punitLevInfo['code'] == 0) {
                        $promote['pro_lev_id'] = $punitLevInfo['data']['pro_lev_id'];
                    }else{
                        $result = D('Proid', 'Service')->createProLev($prolev);
                        $promote['pro_lev_id'] = $result['data'];
                    }
                }   
            }
            $reback = D('Proid', 'Service')->createPromote($promote);
            if($reback['code'] != 0) {
                $this->ajaxReturn($reback['code'], $reback['msg']);
            }
            $this->ajaxReturn(0, '添加计划成功', U('System/Proid/index', array('proid_id'=>$promote['proid_id'])));
        }

        $proInfo = D('Proid', 'Service')->getProInfo(array('proid_id'=>$promote['proid_id']));
        $pro['proidInfo'] = $proInfo['data'];
        $re_page = isset($request['page'])?$request['page']:1;
        $proInfos = D('Proid', 'Service')->getPromoteList($promote,(($re_page-1)*15).',15');
        $pro['promoteList'] = $proInfos['data'];
        $pc['terminal_id'] = 2;
        $m['terminal_id'] = 1;
        $pcser = D('Proid', 'Service')->getOwnServicecode($pc);
        $pro['pcServicecode']['data'] = $pcser['data'];
        $mser = D('Proid', 'Service')->getOwnServicecode($m);
        $pro['mServicecode']['data'] = $mser['data'];
        $pcPagesTypeList = D('Proid', 'Service')->getPagesType($pc);
        $pro['pcPagesTypeList'] = $pcPagesTypeList['data'];
        $mPagesTypeList = D('Proid', 'Service')->getPagesType($m);
        $pro['mPagesTypeList'] = $mPagesTypeList['data'];
        $this->assign('pro', $pro);
        $this->display();

    }

    /**
     * 修改计划
     * @author Nixx
     */
    public function editPromote()
    {
        $promote['promote_id'] = I("get.promote_id");
        $prom['promote_id'] = $promote['promote_id'];
        $promInfo = D('Proid', 'Service')->getPromoteInfo($prom);
        $promoteInfo = $promInfo['data'];
        if (IS_POST) {
            $promote['pc_pages_id'] = I("post.pc_pages_id");
            $promote['m_pages_id'] = I("post.m_pages_id");
            $promote['pcservice_id'] = I("post.pcservice_id");
            $promote['mservice_id'] = I("post.mservice_id");
            $backInfo = D('Proid', 'Service')->editPromote($promote);
            if($backInfo['code'] != 0){
                $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
            }
            $this->ajaxReturn(0, '修改成功', U('System/Proid/index', array('proid_id'=>$promoteInfo['proid_id'])));
        }
        $res_accountname=D('Proid', 'Service')->getProInfo(array('proid_id'=>$promoteInfo['proid_id'])); 
        if(!$res_accountname['data']){
            $this->ajaxReturn($res_accountname['code'], $res_accountname['data']);  
        }
        $promoteInfo['accountname']=$res_accountname['data']['accountname']; 
        $pc['terminal_id'] = 2;
        $m['terminal_id'] = 1;
        $pcser = D('Proid', 'Service')->getOwnServicecode($pc);
        $promoteInfo['pcServicecode']['data'] = $pcser['data'];
        $mser = D('Proid', 'Service')->getOwnServicecode($m);
        $promoteInfo['mServicecode']['data'] = $mser['data'];
        $pc_pages=D('Proid', 'Service')->getPagesInfo(array('pages_id'=>$promoteInfo['pc_pages_id']));
        $promoteInfo['pc_page'] = $pc_pages['data'];
        $m_pages=D('Proid', 'Service')->getPagesInfo(array('pages_id'=>$promoteInfo['m_pages_id']));
        $promoteInfo['m_page'] = $m_pages['data'];
        $pcPagesTypeList = D('Proid', 'Service')->getPagesType($pc);
        $promoteInfo['pcPagesTypeList'] = $pcPagesTypeList['data'];
        $mPagesTypeList = D('Proid', 'Service')->getPagesType($m);
        $promoteInfo['mPagesTypeList'] = $mPagesTypeList['data'];
        $this->assign('promoteInfo', $promoteInfo);
        $this->display();
        
    }


    /**
     * 批量修改计划
     * @author Nixx
     */
    public function editPromoteList()
    {
        $promote['proid_id'] = I("get.proid_id");
        if (IS_POST) {
            $promote['pro_lev_id'] = I("post.pro_lev_id");
            $promote['pc_pages_id'] = I("post.pc_pages_id");
            $promote['m_pages_id'] = I("post.m_pages_id");
            $promote['mark'] = I("post.mark");//2-单元  1-计划
            if (!$promote['m_pages_id'] || !$promote['pc_pages_id']) {
                $this->ajaxReturn(1, '请选择模板');
            }
            $backInfo = D('Proid', 'Service')->editPromoteInfo($promote);
            if($backInfo['code'] != 0){
                $this->ajaxReturn($backInfo['code'], $backInfo['msg']);
            }
            $this->ajaxReturn(0, '修改成功', U('System/Proid/index',array('proid_id'=>$promote['proid_id'])));
        }else{
            $pro['system_user_id'] = $this->system_user_id;           
            $pro['status'] = 1;
            $res = D('Proid', 'Service')->getProList($pro,'0,100');
            $data['proidList'] = $res['data'];
            $promote['status'] = 1;
            $data['proid_id'] = $promote['proid_id'];
            $pc['terminal_id'] = 2;
            $m['terminal_id'] = 1;
            $pcPagesTypeList = D('Proid', 'Service')->getPagesType($pc);
            $data['pcPagesTypeList'] = $pcPagesTypeList['data'];
            $mPagesTypeList = D('Proid', 'Service')->getPagesType($m);
            $data['mPagesTypeList'] = $mPagesTypeList['data'];

            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 删除推广计划
     * @author Nixx
     */
    public function delPro()
    {
        $prom['promote_id'] = I('post.promote_id');
        if (!$prom['promote_id']) {
            $this->ajaxReturn(301, '请选择要删除的计划');
        }
        $backInfo = D('Proid', 'Service')->deletePro($prom);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn($backInfo['code'], '删除失败');
        }
        $promote = D('Proid', 'Service')->getPromoteInfo($prom);
        $this->ajaxReturn(0, '删除成功', U('System/Proid/index',array('proid_id'=>$promote['data']['proid_id'])));
    }

    /**
     * 获取推广计划id
     * @author Nixx
     */
    public function prolevPlanList()
    {
        $proid_id = I('post.proid_id');
        if (!$proid_id) {
            $this->ajaxReturn(301, '参数丢失');
        }
        $res = D('Proid', 'Service')->getProLevPlanList($proid_id);
        if (!$res['data']) {
            $this->ajaxReturn($res['code'], '该账号下尚未添加计划');
        } 

        $this->ajaxReturn(0, '数据获取成功', $res['data']);

    }

    /**
     * 获取推广计划单元id
     * @author Nixx
     */
    public function prolevPlanunitList()
    {
        $pro_lev_id = I('post.pro_lev_id');
        if (!$pro_lev_id) {
            $this->ajaxReturn(301, '参数丢失');
        }
        $res = D('Proid', 'Service')->getProLevPlanunitList(array('pid'=>$pro_lev_id));
        if (!$res['data']) {
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
        $proid_id = I("get.proid_id");
        $setPages['type'] = 1;
        $res = D('Proid', 'Service')->getSetPages($setPages);
        foreach ($res['data'] as $key => $value) {
            $value['createtime'] = date('Y-m-d H:d:s', $value['createtime']);
            $pagesList[$key] = $value;
        }
        $this->assign('urlDelSetPages', U("System/Proid/delSetPages"));
        $this->assign('proid_id', $proid_id);
        $this->assign('pagesList', $pagesList);
        $this->display();

    }

    /**
     * 添加模板
     * @author 
     * 
     */
    public function addSetTemplate()
    {   
        $proid_id = I("get.proid_id");
        if (IS_POST) {
            $setPages = I("post.");           
            $setPages['type'] = 1;
            $result = D('Proid', 'Service')->createSetPages($setPages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            $this->ajaxReturn(0, '设置模板成功', U('System/Proid/setPages', array('proid_id' => $proid_id))); 
        }
        $this->assign('page_head',C('page_head'));
        $this->assign('proid_id', $proid_id);
        $this->display();

    }

    /**
     * 删除模板
     * @author 
     * 
     */
    public function delSetPages()
    {   
        $proid_id = I("get.proid_id");
        $setpages['setpages_id'] = I("post.setpages_id");
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
        $proid_id = I("get.proid_id");
        $setpages_id = I("get.setpages_id");
        if (IS_POST) {
            $setpages = I("post.");
            $setpages['setpages_id'] = $setpages_id;
            $setpages['sign'] = explode(',',$setpages['sign']);
            foreach ($setpages['sign'] as $key => $sign) {
                $setpages['sign'][$key] = explode('-',$sign);
            } 
            $result = D('Proid', 'Service')->editSetPages($setpages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            $this->ajaxReturn(0, '修改模板成功', U('System/Proid/setPages', array('proid_id' => $proid_id))); 
        }
        $setpages['system_user_id'] = $this->system_user_id;
        $setpages['setpages_id'] = $setpages_id;
        $result = D('Proid', 'Service')->getSetPages($setpages);
        if(!$result['data'])
        {
            $this->ajaxReturn(201, '无法获取模板信息');
        }
        $pagesInfo=$result['data'][0];
        $head_info=D('Proid', 'Service')->getSetPagesInfos($setpages_id); 
        $head_name_arr=array();
        foreach ($head_info['data'] as $key => $value) {
            $head_name_arr[]=$value['headname'];
        }
        $pagesInfo['head_info']=$head_info['data'];
        $this->assign('page_head',C('page_head'));
        $this->assign('head_name_arr',$head_name_arr);
        $this->assign('proid_id', $proid_id);       
        $this->assign('pagesInfo', $pagesInfo);
        $this->display();

    }

    /**
     * 导入计划
     * @author Nixx
     */
    public function inputPlan()
    {
        $proid_id = I("get.proid_id");
        $proid = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid_id));
        $proidInfo = $proid['data'];
        if(IS_POST)
        {
            $request = I("post.");
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
        }else{
            $set['type'] = 1;    
            $res = D('Proid', 'Service')->getSetPages($set);
            $setPages = $res['data'];
            $pc['terminal_id'] = 2;
            $m['terminal_id'] = 1;
            $pcPagesTypeList = D('Proid', 'Service')->getPagesType($pc);
            $pro['pcPagesTypeList'] = $pcPagesTypeList['data'];
            $mPagesTypeList = D('Proid', 'Service')->getPagesType($m);
            $pro['mPagesTypeList'] = $mPagesTypeList['data'];
            $this->assign('pro', $pro);
            $this->assign('setPages', $setPages);
            $this->assign('proidInfo', $proidInfo);
            $this->display();
        }
        
    }

    /**
     * [inputClient description]
     * @return [type] [description]
     */
    public function inputClient()
    {
        $faile = session('faile_input');
        $success = session('success_input');        
        $newPromoteList = session('newPromoteList');      
        $letters = session('letters'); 
        $proid_id = I("get.proid_id");
        $excel_key_value = array(
            'plan'=>'推广计划名称',
            'planunit'=>'推广单元名称',
            'keyword'=>'关键词名称',
            'pc_pages'=>'PC模板',
            'm_pages'=>'移动模板',
        );        
        $letters = array_keys($letters);
        $filename = "proPlan";
        outExecl($filename,array_values($excel_key_value),$newPromoteList,$letter);
        $successCount = count($success);
        $faileCount = count($faile);
        $this->assign('faile', $faile);
        $this->assign('success', $success);
        $this->assign('successCount', $successCount);
        $this->assign('faileCount', $faileCount);
        $this->assign('proid_id', $proid_id);
        $this->display();
    }

    /**
     * 批量导出计划
     * @author Nixx
     */
    public function outPlan()
    {
        $proid_id = I('get.proid_id');
        if (IS_POST) {
            $request = I('post.');        
            $result = D('Proid', 'Service')->outputPlan($request, $proid_id);
            if($result['code']==0){
                return $result['data'];
            }else{
                $this->ajaxReturn($result['code'], $result['msg']);
            }
        }
        $proid = D('Proid', 'Service')->getProInfo(array('proid_id'=>$proid_id));
        $pro['proid'] = $proid['data'];
        $promoteList = D('Proid', 'Service')->getProLevPlanList($proid_id);  
        $pro['promoteList'] = $promoteList['data'];        
        $this->assign('pro', $pro);
        $this->display();
    }

    

    
}
