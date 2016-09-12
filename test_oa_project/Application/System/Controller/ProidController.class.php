<?php
namespace System\Controller;
use Common\Controller\SystemController;
use Common\Controller\ProidController as ProidMain;
use Common\Controller\CourseController as CourseMain;
use Common\Controller\ChannelController as ChannelMain;
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
        $own['system_user_id'] = $this->system_user_id;
        $own['status'] = 1;
        //排序URL
        if($request['servicecode_id']=='asc'){
            $data['url_servicecode_id'] = U('System/Proid/servJsList').'?servicecode_id=desc';
        } else {
            $data['url_servicecode_id'] = U('System/Proid/servJsList').'?servicecode_id=asc';
        }
        //获取数据
        $proidMain = new ProidMain();
        $result = $proidMain->getOwnServicecode($own);
        $data['servicecodeAll']['data'] = $result['msg'];
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
        $proidMain = new ProidMain();
        if(IS_POST){
            $requery = I('post.');
            if(empty($requery['title'])){
                $this->ajaxReturn(1, '标题不能为空', '', 'title');
            }
            if(empty($requery['terminal_id'])){
                $this->ajaxReturn(2, '请选择终端类型', '', 'terminal_id');
            }
            if(empty($requery['url'])){
                $this->ajaxReturn(3, '链接不能为空', '', 'url');
            }
            if(empty($requery['servicecode'])){
                $this->ajaxReturn(4, '客服代码不能为空', '', 'servicecode');
            }
            $requery['system_user_id'] = $this->system_user_id;
            $result = $proidMain->addServicecode($requery);

            //判断返回数据
            if($result['code'] != 0){
                $this->ajaxReturn(5, $result['msg']);
            }else{
                $this->success('添加成功', 0, U('System/Proid/servJsList'));
            }
        }else{
            //终端列表
            $res = $proidMain->getAllTerminal();
            $data['terminalAll'] = $res['msg'];
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
        if(!isset($servicecode_id)){
            $this->error('非法请求!');
        }
        $proidMain = new ProidMain();
        if(IS_POST){
            $request = I('post.');
            if(empty($request['title'])){
                $this->ajaxReturn(1, '标题不能为空', '', 'title');
            }
            if(empty($request['terminal_id'])){
                $this->ajaxReturn(2, '请选择终端类型', '', 'terminal_id');
            }
            if(empty($request['url'])){
                $this->ajaxReturn(3, '链接不能为空', '', 'url');
            }
            if(empty($request['servicecode'])){
                $this->ajaxReturn(4, '客服代码不能为空', '', 'servicecode');
            }
            $request['system_user_id'] = $this->system_user_id;
            $result = $proidMain->editServicecode($request,$servicecode_id);
            
            //判断返回数据
            if($result['code'] != 0){
                $this->ajaxReturn(5, '数据修改失败');
            }else{
                $this->success('修改成功', 0, U('System/Proid/servJsList'));
            }
        }else{
            //终端列表
            $res = $proidMain->getAllTerminal();
            $data['terminalAll'] = $res['msg'];
            $data['url_servJsList'] = U('System/Proid/servJsList');
            $servicecodeInfo = $proidMain->detailServicecode($servicecode_id);
            $data['Servicecode'] = $servicecodeInfo['msg'];
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
        $proidMain = new ProidMain();
        $servicecode_id = I('get.serv_id',null);
        if(empty($servicecode_id)){
            $this->ajaxReturn(1, '请求参数错误');
        }
        $res = $proidMain->detailServicecode($servicecode_id);
        $data['Servicecode'] = $res['msg'];
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 删除客服代码
     * @author zgt
     */
    public function delServ()
    {
        $proidMain = new ProidMain();
        $servicecode_id = I('post.serv_id',null);
        if(empty($servicecode_id)){
            $this->ajaxReturn(1, '参数异常');
        }
        $requery['status'] = 0;
        $result = $proidMain->editServicecode($requery,$servicecode_id);
        //判断返回数据
        if($result['code'] != 0){
            $this->ajaxReturn(2, $result['msg']);
        }
        $this->success('删除成功', 0, U('System/Proid/servJsList'));
    }


    /**
     * ****************************************************************
     * 专题页列表
     * ****************************************************************
     * @author zgt
     */
    public function pages()
    {
        $proidMain = new ProidMain();
        $courseMain = new CourseMain();
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
        $order = 'createtime desc';
        $pages = $proidMain->getAllPages($order,$re_page.',12',$request);
        $data['pagesAll'] = $pages['msg'];
        foreach($data['pagesAll']['data'] as $k=>$v){
            $remark = $proidMain->getPagesRemark($v['pages_id'],$this->system_user_id);
            if(!empty($remark)) {
                $data['pagesAll']['data'][$k]['remark'] = $remark['msg']['remark'];
            }
        }

        //模板分类列表
        $pagesType = $proidMain->getAllPagesType();
        $data['pagesType'] = $pagesType['msg'];
        //课程列表
        $courseAll = $courseMain->getAllCourse();
        $data['courseAll'] = $courseAll['data'];
        //终端列表
        $terminalAll = $proidMain->getAllTerminal();
        $data['terminalAll'] = $terminalAll['msg'];
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
        $proidMain = new ProidMain();
        $courseMain = new CourseMain();
        if(IS_POST){
            //获取参数 验证
            $request = I('post.');
            $request['litpic'] = $request['image'];
            if(empty($request['subject'])){
                $this->ajaxReturn(1, '模板主题不能为空', '', 'subject');
            }
            if(empty($request['terminal_id'])){
                $this->ajaxReturn(2, '请选择终端');
            }
            if(empty($request['pagestype_id'])){
                $this->ajaxReturn(3, '请选择模板分类');
            }
            if(empty($request['course_id']) && $request['course_id']!=0){
                $this->ajaxReturn(4, '请选择课程');
            }
            if(empty($request['image'])){
                $this->ajaxReturn(5, '请上传图片');
            }
            $result = $proidMain->addPages($request);
            if($result['code'] != 0){
                $this->ajaxReturn(6, '添加失败');
            }else{
                $this->ajaxReturn(0,'专题页添加成功，可以添加导航，或者返回专题页列表',U('System/Proid/addPagesNav?pages_id='.$result));
            }
        }else{

            //模板分类列表
            $pagesType = $proidMain->getAllPagesType();
            $data['pagesType'] = $pagesType['msg'];

            //课程列表
            $courseAll = $courseMain->getAllCourse();
            $data['courseAll'] = $courseAll['data'];

            //终端列表
            $terminalAll = $proidMain->getAllTerminal();
            $data['terminalAll'] = $terminalAll['msg'];

            $data['url_pages'] = U('System/Proid/pages');
            $data['url_disposTermin'] = U('System/Proid/disposTermin');
            $data['url_dispostPagesType'] = U('System/Proid/dispostPagesType');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 添加模板 导航
     * @author zgt
     */
    public function addPagesNav()
    {
        $proidMain = new ProidMain();
        $courseMain = new CourseMain();
        $pages_id = I('get.pages_id');
        if(IS_POST){
            $request = I('post.');
            $result = $proidMain->addPagesNav($request['navs'],$pages_id);
            if($result['code'] != 0){
                $this->ajaxReturn(1, '添加失败');
            }else{
                $this->ajaxReturn(0,'专题页导航添加成功', U('System/Proid/pages'));
            }
        }else{
            $detail = $proidMain->getPagesNav($pages_id);
            $data['detail'] = $detail['msg'];

            //模板分类列表
            $pagesType = $proidMain->getAllPagesType();
            $data['pagesType'] = $pagesType['msg'];

            //课程列表
            $courseAll = $courseMain->getAllCourse();
            $data['courseAll'] = $courseAll['data'];

            //终端列表
            $terminalAll = $proidMain->getAllTerminal();
            $data['terminalAll'] = $terminalAll['msg'];

            $data['url_pages'] = U('System/Proid/pages');
            $data['pages_id'] = $pages_id;
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 修改模板
     * @author zgt
     */
    public function editPages()
    {
        $proidMain = new ProidMain();
        $courseMain = new CourseMain();
        $pages_id = I('get.id');
        if( empty($pages_id) ){
            $this->error('非法请求！');
        }
        if(IS_POST){
            //获取参数 验证
            $request = I('post.');
            $request['litpic'] = $request['image'];
            if(empty($request['subject'])) {
                $this->ajaxReturn(1, '模板主题不能为空', '', 'subject');
            }
            if(empty($request['terminal_id'])) {
                $this->ajaxReturn(2, '请选择终端');
            }
            if(empty($request['pagestype_id'])) {
                $this->ajaxReturn(3, '请选择模板分类');
            }
            if(empty($request['course_id']) && $request['course_id']!=0) {
                $this->ajaxReturn(4, '请选择课程');
            }
            if(empty($request['image'])) {
                $this->ajaxReturn(5, '请上传图片');
            }
            $result = $proidMain->editPages($request,$pages_id);
            if($result['code'] != 0) {
                $this->ajaxReturn(6, $result['msg']);
            }else{
                 $this->success('专题页修改成功，可以修改导航，或者返回专题页列表', 0, U('System/Proid/addPagesNav?pages_id='.$pages_id));
            }
        }else{
            //模板详情
            $pagesInfo = $proidMain->getPagesInfo($pages_id);
            $data['pagesInfo'] = $pagesInfo['msg'];

            //模板分类列表
            $pagesType = $proidMain->getAllPagesType();
            $data['pagesType'] = $pagesType['msg'];

            //课程列表
            $courseAll = $courseMain->getAllCourse();
            $data['courseAll'] = $courseAll['data'];

            //终端列表
            $terminalAll = $proidMain->getAllTerminal();
            $data['terminalAll'] = $terminalAll['msg'];

            $data['url_pages'] = U('System/Proid/pages');
            $data['url_disposTermin'] = U('System/Proid/disposTermin');
            $data['url_dispostPagesType'] = U('System/Proid/dispostPagesType');
            
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 模板详情
     * @author zgt
     */
    public function detailPage()
    {
        $proidMain = new ProidMain();
        $channelMain = new ChannelMain();
        $pages_id = I('get.id');
        $channel_id = I('get.channel_id',null);
        $re_page = I('get.page',1);
        if( empty($pages_id) ){
            $this->error('非法请求！');
        }
        if( !empty($channel_id) ) $where[C('DB_PREFIX').'channel.channel_id'] = $channel_id;
        //模板详情
        $pageInfo = $proidMain->getPagesInfo($pages_id);
        $data['pagesInfo'] = $pageInfo['msg'];
        //模板相关计划列表
        $proList = $proidMain->getPagesPromote($pages_id, $where,(($re_page-1)*15).',15');
        $data['promoteList'] = $proList['msg'];
        //终端列表
        $terminalAll = $proidMain->getAllTerminal();
        $data['terminalAll'] = $terminalAll['msg'];
        //渠道列表
        $channelList = $channelMain->getAllChannel();
        $data['channel'] = $channelList['data'];
        //加载分页类
        $data['paging'] = $this->Paging($re_page,15,$data['promoteList']['count'],array('id'=>$pages_id));
        $data['url_pages'] = U('System/Proid/pages');
        $this->assign('data', $data);
        $this->display();

    }

    /**
     * 模板 修改专题页备注
     * @author zgt
     */
    public function editPagesRemark(){
        $proidMain = new ProidMain();
        if(IS_POST) {
            $pages_id = I('post.pages_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='remark' ){
                $request['remark'] = I('post.remark');
                $result = $proidMain->addPagesRemark($request['remark'],$pages_id,$this->system_user_id);
                if($result['code'] != 0) {
                    $this->ajaxReturn(1, '备注修改失败');
                } else{
                     $this->success('备注修改成功');
                }
            }
        }
    }

    /**
     * 模板 删除
     * @author zgt
     */
    public function delPages(){
        $proidMain = new ProidMain();
        if(IS_POST) {
            $pages_id = I('post.pages_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $request['status'] = 0;
                $result = $proidMain ->editPages($request,$pages_id);
                if($result['code'] != 0){
                    $this->ajaxReturn(1, '删除失败');
                }else {
                    $this->success('删除成功');
                }
            }
        }
    }

    /**
     * 获取指定类型模板
     * @author Nixx
     */
    public function pagesList()
    {
        $proidMain = new ProidMain();
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

            $result= $proidMain->getAllPages($order,$re_page.',8',$request);
            $pagesList = $result['msg'];
            $pagesList['paging'] = $this->Paging($re_page,8,$pagesList['count'],'','javascript:pagesList(',1);
            if (!$pagesList) {
                $this->ajaxReturn(2, '模板获取失败');
            }

            $this->ajaxReturn(0, '数据获取成功', $pagesList);
        }else{
            $order = 'createtime desc';
            $request['pagestype_id'] = I('post.pagesType_id');
            $request['terminal_id'] = I('post.terminal_id');
            $request['status'] = 1;
            if(!empty($request['key_name']) && !empty($request['key_val'])){
                $request[$request['key_name']] = '%%'.$request['key_val'];
            }
            unset($request['page']);
            unset($request['key_name']);
            unset($request['key_val']);
            $result= $proidMain->getPagesList($request);
            if ($result['code'] != 0) {
                $this->ajaxReturn(2, '模板获取失败');
            }
            $pagesList = $result['msg'];
            $systemuser_id = $this->system_user_id;
            foreach ($pagesList['data'] as $key => $value) {
                $remark = M("pages_remark")->where("system_user_id = $systemuser_id and pages_id = $value[pages_id]")->find();
                if ($remark['remark']) {
                    $pagesList['data'][$key]['remark'] = $remark['remark'];
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
        $proidMain = new ProidMain();
        if(IS_POST) {
            $terminal_id = I('post.terminal_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $request['status'] = 0;
                $result = $proidMain->editTerminal($request,$terminal_id);
                if($result['code'] != 0){
                    $this->ajaxReturn(1, $result['msg']);
                }else {
                    $this->success('删除成功', 0);
                }
            }else if( isset($type) && $type=='addEdit' ){
                $request['addTerminal'] = I('post.addTerminal');
                $request['editTerminal'] = I('post.editTerminal');
                if(empty($request['addTerminal']) && empty($request['editTerminal'])) $this->ajaxReturn(1, '未发现需要修改或添加内容');
                $addFlag = true;
                if(!empty($request['addTerminal'])){
                    $addTerminal = explode('@@',$request['addTerminal']);
                    foreach($addTerminal as $k=>$v){
                        if(!empty($v) && $addFlag){
                            $add_data['terminalname'] = $v;
                            $addFlag = $proidMain->addTerminal($add_data);
                        }
                    }
                }
                $editFlag = true;
                if(!empty($request['editTerminal'])){
                    $editTerminal = explode('@@',$request['editTerminal']);
                    foreach($editTerminal as $k=>$v){
                        if(!empty($v) && $editFlag!==false){
                            $new_v = explode('==',$v);
                            $edit_data['terminalname'] = $new_v[1];
                            $editFlag = $proidMain->editTerminal($edit_data, $new_v[0]);
                        }
                    }
                }
                if($editFlag['code']!=0 || $addFlag['code']!=0) {
                    $this->ajaxReturn(2, '操作失败');
                }else {
                    $this->success('提交成功', 0);
                }
                
            }
        }
    }

    /**
     * 模板分类  快捷操作(删除/修改)-ajax
     * @author zgt
     */
    public function dispostPagesType()
    {
        $proidMain = new ProidMain();
        if(IS_POST) {
            $pagestype_id = I('post.pagestype_id');
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $request['status'] = 0;
                $result = $proidMain->editPagesType($request,$pagestype_id);
                if($result['code'] != 0){
                    $this->ajaxReturn(1, '删除失败');
                }else {
                    $this->success('删除成功', 0);
                }
            }else if( isset($type) && $type=='addEdit' ){
                $request['addPagesType'] = I('post.addPagesType');
                $request['editPagesType'] = I('post.editPagesType');
                if(empty($request['addPagesType']) && empty($request['editPagesType'])){
                    $this->ajaxReturn(1, '未发现需要修改或添加内容');
                }
                $addFlag = true;
                if(!empty($request['addPagesType'])){
                    $addPagesType = explode('@@',$request['addPagesType']);
                    foreach($addPagesType as $k=>$v){
                        if(!empty($v) && $addFlag){
                            $new_v = explode('==',$v);
                            $add_data['typename'] = $new_v[0];
                            $add_data['terminal_id'] = $new_v[1];
                            $addFlag = $proidMain->addPagesType($add_data);
                        }
                    }
                }
                $editFlag = true;
                if(!empty($request['editPagesType'])){
                    $editPagesType = explode('@@',$request['editPagesType']);
                    foreach($editPagesType as $k=>$v){
                        if(!empty($v) && $editFlag!==false){
                            $new_v = explode('==',$v);
                            $edit_data['typename'] = $new_v[1];
                            $add_data['terminal_id'] = $new_v[2];
                            $editFlag = $proidMain->editPagesType($edit_data, $new_v[0]);
                        }
                    }
                }
                if($editFlag['code'] != 0 || $addFlag['code'] != 0){
                    $this->ajaxReturn(1, '操作失败');
                }else {
                    $this->success('提交成功', 0);
                }
            }
        }
    }

    /**
     * 推广账号管理
     * @author Nixx
     */
    public function id()
    {
        $proidMain = new ProidMain();
        $request = I("get.");
        // $re_page = isset($request['page'])?$request['page']:1;
        unset($request['page']);
        foreach ($request as $key => $req) {
            if ($req) {
                $pro[$key] = $req;
            }
        }
        $pro['system_user_id'] = $this->system_user_id;
        $pro['status'] = 1;
        $result = $proidMain->proidList($pro,'0,100');
        $data['proList'] = $result['msg'];
        foreach ($data['proList']['data'] as $key => $value) {
            $value['createtime'] = date('Y-m-d H:d:s', $value['createtime']);
            $data['proList']['data'][$key] = $value;
        }
        //加载分页类
        $pagingClass = new \Org\Util\Paging();
        $pagingClass->page = $re_page;
        $pagingClass->count = (isset($proList['count'])?$proList['count']:0);
        $pagingClass->shownum = 7;
        $pagingClass->url = U('System/Proid/id').'?'.http_build_query($request).'&page=';
        $pagingClass->type = 'system';
        $data['paging'] = $pagingClass->createPaging();
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 添加推广账号
     * @author Nixx
     */
    public function addPro()
    {
        $proidMain = new ProidMain();
        $channelMain = new ChannelMain();
        $pro['system_user_id'] = $this->system_user_id;
        if(IS_POST) {              
            $proid['accountname'] = I("post.accountname");
            $proid['channel_id'] = I("post.channel_id");
            $proid['domain'] = I("post.domain");
            $proid['totalcode'] = I("post.totalcode");
            $proid['pcservice_id'] = I("post.pcservice_id");
            $proid['mservice_id'] = I("post.mservice_id");
            $proid['system_user_id'] = $this->system_user_id;

            if (!$proid['accountname']) {
                $this->ajaxReturn(1,'请填写账号名称');
            }
            if (!$proid['channel_id']) {
                $this->ajaxReturn(2,'请选择渠道');
            }
            if (!$proid['domain']) {
                $this->ajaxReturn(3,'请填写域名');
            }else{
                $test = '/[。]/';
                if (preg_match($test, $proid['domain'])) {
                    $this->ajaxReturn(3,'请填写正确的域名');
                }
            }
            if (!$proid['totalcode']) {
                $this->ajaxReturn(4,'请填写统计代码');
            }
            if (!$proid['pcservice_id'] || !$proid['mservice_id']) {
                $this->ajaxReturn(5,'请选择PC客服代码或移动客服代码');
            }
            $proid['pcoffcode'] = I("post.pcoffcode");
            $proid['moffcode'] = I("post.moffcode");
            $proid['remark'] = I("post.remark");
            $result = $proidMain->createProid($proid);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'],$result['msg']);
            }
            $this->success('创建成功', 0, U('System/Proid/id'));
        }

        $result = $channelMain->getAllChannel();
        $channelList = $result['data'];
        if (!$channelList) {
            $this->ajaxReturn(9,'没有渠道可供选择');
        }
        $pc['terminal_id'] = 2;
        $pc['status'] = 1;
        $pc['system_user_id'] = $this->system_user_id;
        $m['status'] = 1;
        $m['terminal_id'] = 1;
        $m['system_user_id'] = $this->system_user_id;                             
        $pcser = $proidMain->getOwnServicecode($pc);
        $pcserviceList['data'] = $pcser['msg'];
        $mser = $proidMain->getOwnServicecode($m);
        $mserviceList['data'] = $mser['msg'];
        // if ($pcserviceList['data']['code'] != 0) {
        //     $this->ajaxReturn(10,'没有PC客服代码可供选择');
        // }
        // if ($mserviceList['data']['code'] != 0) {
        //     $this->ajaxReturn(11,'没有移动客服可供选择');
        // }
        $result = $proidMain->proidList($pro);
        $proList = $result['msg'];
        $proList['channelList'] = $channelList;
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
        $proidMain = new ProidMain();
        $channelMain = new ChannelMain();
        $proid_id = I("get.proid_id");
        if (!$proid_id) {
            $this->error('参数丢失');
        }
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
            $proid['system_user_id'] = $this->system_user_id;
            $proid['remark'] = I("post.remark");
            if (!$proid['channel_id']) {
                $this->error('请选择渠道');
            }
            if (!$proid['pcservice_id'] || !$proid['mservice_id']) {
                $this->error('请选择客服代码');
            }
            if (!$proid['domain']) {
                $this->error('请填写域名');
            }
            $backInfo = $proidMain->editProid($proid);
            if ($backInfo['code'] != 0) {
                $this->error('修改失败');
            }
            $this->success('修改成功', 0, U('System/Proid/id'));
        }else{
            $channelAll = $channelMain->getAllChannel();
            $channelList = $channelAll['data'];
            if (!$channelList) {
                $this->error('没有渠道可供选择');
            }
            $result = $proidMain->getProInfo($proid_id);
            $proidInfo = $result['msg'];
            $pc['terminal_id'] = 2;
            $pc['status'] = 1;
            $system_user_id = $this->system_user_id;
            $pc['system_user_id'] = $system_user_id;
            $m['status'] = 1;
            $m['terminal_id'] = 1;
            $m['system_user_id'] = $system_user_id;                          
            
            $pcser= $proidMain->getOwnServicecode($pc);
            $pcserviceList['data']= $pcser['msg'];
            $pcserviceList['pcservice_id'] = $proidInfo['pcservice_id'];
            
            $mser = $proidMain->getOwnServicecode($m);
            $mserviceList['data']= $mser['msg'];
            $mserviceList['mservice_id'] = $proidInfo['mservice_id'];
            
            $proList['data'] = $proidInfo;
            $proList['channelList'] = $channelList;
            $this->assign('proid_id',$proid_id);
            $this->assign('proList', $proList);

            $this->assign('pcserviceList', $pcserviceList);
            $this->assign('mserviceList', $mserviceList);
            $this->display();
        }
    }

    /**
     * 推广账号详情
     * @author Nixx
     */
    public function proidInfo()
    {   
        $proidMain = new ProidMain();
        $proid_id = I("get.proid_id");
        if (!$proid_id) {
             $this->ajaxReturn(1,'访问存在异常');
        }
        $proInfo = $proidMain->getProInfo($proid_id);
        if ($proInfo['code'] != 0) {
            $this->ajaxReturn(2,'账号数据不存在');
        }
        $this->assign('proid_id', $proid_id);
        $this->assign('proInfo', $proInfo['msg']);
        $this->display();
    }

    /**
     * 删除推广账号
     * @author Nixx
     */
    public function delProid()
    {
        $proidMain = new ProidMain();
        $proid_id = I('get.proid_id');
        if (!$proid_id) {
            $this->ajaxReturn(1, '请选择要删除的账号');
        }
        $backInfo = $proidMain->deleteProid($proid_id);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn(2, $backInfo['msg']);
        }
        $this->success('删除成功', 0, U('System/Proid/id'));
    }

    /**
     * 计划列表
     * @author Nixx
     */
    public function index()
    {
        $proidMain = new ProidMain();
        $channelMain = new ChannelMain();
        $proid_id = I("get.proid_id");    
        $proid['proid_id'] = $proid_id;
        $re_page = I("get.page",1);
        $promote['proid_id'] = $proid_id;
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
                    $prolevList = $proidMain->getProLevPlanunitList($pro['pro_lev_id']);
                    $result = $proidMain->getIdString($prolevList['msg']);
                    $idString = $result['msg'];
                    $promote['pro_lev_id'] = array("IN", $idString);
                }
                if (!$pro['key_name'] && $pro['key_val']) {
                    $this->error('请选择搜索类型');
                }
                if ($pro['key_name'] == 'promote_id') {
                    $promote['promote_id'] = $pro['key_val'];
                }elseif ($pro['key_name'] == 'keyword') {
                    $promote['keyword'] = array('like', "%{$pro['key_val']}%" );
                }
            }
        }
        $promList = $proidMain->getPromoteList($promote,(($re_page-1)*15).',15');
        $data['promoteListAll'] = $promList['msg'];
        $result = $channelMain->getAllChannel();
        $data['channelList'] = $result['data'];

        //加载分页类
        $data['paging'] = $this->Paging($re_page,15,$data['promoteListAll']['count'],$proid);
        $data['proid_id'] = $proid['proid_id'];
        foreach ($data['promoteListAll']['promoteList'] as $key => $promote) {
            $proid = $proidMain->getProInfo($promote['proid_id']);
            if ($proid['code'] != 0) {
               $channeldata = $channelMain->getChannel($proid['msg']['channel_id']);
               $channelname = $channeldata['data'];
            }
            $data['promoteListAll']['promoteList'][$key]['channelname'] = $channelname['channelName'];
            $data['promoteListAll']['promoteList'][$key]['accountname'] = $proid['msg']['accountname'];
            
        }
        $promoteList = $proidMain->getProLevPlanList($proid_id);
        $this->assign('proid_id', $proid_id);    
        $this->assign('promoteList', $promoteList['msg']);    
        $this->assign('data', $data);
        $this->assign('urlDelPromote', U("System/Proid/delPro"));
        $this->display();

    }

    /**
     * 添加推广计划
     * @author Nixx
     */
    public function addPromote()
    {
        $proidMain = new ProidMain();
        $promote['proid_id'] = I("get.proid_id");
        if (IS_POST) {
            $promote['plan'] = I("post.plan");
            $promote['planunit'] = I("post.planunit");
            $promote['keyword'] = I("post.keyword");
            $promote['pcservice_id'] = I("post.pcservice_id");
            $promote['mservice_id'] = I("post.mservice_id");
            $promote['pc_pages_id'] = I("post.pcPageid");
            $promote['m_pages_id'] = I("post.mPageid");
            if (!$promote['plan'] && $promote['planunit']) {
                $this->ajaxReturn(1,'请填写计划');
            }
            if ($promote['plan'] && !$promote['planunit']) {
                $this->ajaxReturn(2,'请填写计划单元');
            }
            if (!$promote['keyword']) {
                $this->ajaxReturn(3,'请填写关键词');
            }
            if (!$promote['pc_pages_id']) {
                unset($promote['pc_pages_id']);
            }
            if (!$promote['m_pages_id']) {
                unset($promote['m_pages_id']);
            }
            $proidInfo = $proidMain->getProInfo($promote['proid_id']);
            if (!$promote['pcservice_id']) {
                $promote['pcservice_id'] = $proidInfo['msg']['pcservice_id'];
            }
            if (!$promote['mservice_id']) {
                $promote['mservice_id'] = $proidInfo['msg']['mservice_id'];
            }
            $prolev['proid_id'] = $promote['proid_id'];
            if ($promote['plan'] && $promote['planunit']) {
                $prolev['name'] = $promote['plan'];
                $proLevInfo = $proidMain->getProLevInfo($prolev);
                if ($proLevInfo['code'] != 0) {
                    $result = $proidMain->createProLev($prolev);
                    $prolev['name'] = $promote['planunit'];
                    $prolev['pid'] = $result['msg'];
                    $result = $proidMain->createProLev($prolev);
                    $prolev['pid'] = 0; //重置pid为0
                    $promote['pro_lev_id'] = $result['msg'];
                }else{
                    $prolev['name'] = $promote['planunit'];
                    $prolev['pid'] = $proLevInfo['msg']['pro_lev_id'];
                    $punitLevInfo = $proidMain->getProLevInfo($prolev);
                    if ($punitLevInfo['code'] == 0) {
                        $promote['pro_lev_id'] = $punitLevInfo['msg']['pro_lev_id'];
                    }else{
                        $result = $proidMain->createProLev($prolev);
                        $promote['pro_lev_id'] = $result['msg'];
                    }
                }   
            }
            $reback = $proidMain->createPromote($promote);
            if($reback['code'] != 0) {
                $this->ajaxReturn(4,'添加推广计划失败');
            }
            $this->success('添加计划成功', 0, U('System/Proid/index', array('proid_id'=>$promote['proid_id'])));
        }

        $proInfo = $proidMain->getProInfo($promote['proid_id']);
        $pro['proidInfo'] = $proInfo['msg'];
        $promote['system_user_id'] = $this->system_user_id;
        $promote['status'] = 1;
        $re_page = isset($request['page'])?$request['page']:1;
        $proInfos = $proidMain->getPromoteList($promote,(($re_page-1)*15).',15');
        $pro['promoteList'] = $proInfos['msg'];
        $pc['terminal_id'] = 2;
        $pc['status'] = 1;
        $pc['system_user_id'] = $this->system_user_id;
        $m['status'] = 1;
        $m['terminal_id'] = 1;
        $m['system_user_id'] = $this->system_user_id;                              
        $pcser = $proidMain->getOwnServicecode($pc);
        $pro['pcServicecode']['data'] = $pcser['msg'];
        $mser = $proidMain->getOwnServicecode($m);
        $pro['mServicecode']['data'] = $mser['msg'];
        $pcPagesTypeList = $proidMain->getPagesType($pc);
        $pro['pcPagesTypeList'] = $pcPagesTypeList['msg'];
        $mPagesTypeList = $proidMain->getPagesType($m);
        $pro['mPagesTypeList'] = $mPagesTypeList['msg'];
        $this->assign('pro', $pro);
        $this->display();

    }

    /**
     * 修改计划
     * @author Nixx
     */
    public function editPromote()
    {
        $proidMain = new ProidMain();
        $promote['promote_id'] = I("get.promote_id");
        $prom['promote_id'] = $promote['promote_id'];
        $promInfo = $proidMain->getPromoteInfo($prom);
        $promoteInfo = $promInfo['msg'];
        if (IS_POST) {
            $promote['pc_pages_id'] = I("post.pc_pages_id");
            $promote['m_pages_id'] = I("post.m_pages_id");
            $promote['pcservice_id'] = I("post.pcservice_id");
            $promote['mservice_id'] = I("post.mservice_id");
            $backInfo = $proidMain->editPromote($promote);
            if($backInfo['code'] != 0){
                $this->ajaxReturn(3, '修改失败');
            }
            $this->success('修改成功', 0, U('System/Proid/index', array('proid_id'=>$promoteInfo['proid_id'])));
        }
          
        $accountname=D('Proid')->where(array('proid_id'=>$promoteInfo['proid_id']))->getField('accountname'); 
        if(!$accountname)$this->error('无法获取推广计划完整信息！');  
        $promoteInfo['accountname']=$accountname; 
        $pc['terminal_id'] = 2;
        $pc['system_user_id'] = $this->system_user_id;
        $m['terminal_id'] = 1;
        $pc['status'] = 1;
        $m['status'] = 1;
        $m['system_user_id'] = $this->system_user_id;
        $pcser = $proidMain->getOwnServicecode($pc);
        $promoteInfo['pcServicecode']['data'] = $pcser['msg'];
        $mser = $proidMain->getOwnServicecode($m);
        $promoteInfo['mServicecode']['data'] = $mser['msg'];
        $promoteInfo['pc_page']=D("Pages")->where(array('pages_id'=>$promoteInfo['pc_pages_id']))->getField('subject');
        $promoteInfo['m_page']=D("Pages")->where(array('pages_id'=>$promoteInfo['m_pages_id']))->getField('subject');
        
        $pcPagesTypeList = $proidMain->getPagesType($pc);
        $promoteInfo['pcPagesTypeList'] = $pcPagesTypeList['msg'];
        $mPagesTypeList = $proidMain->getPagesType($m);
        $promoteInfo['mPagesTypeList'] = $mPagesTypeList['msg'];
        $this->assign('promoteInfo', $promoteInfo);
        $this->display();
        
    }


    /**
     * 批量修改计划
     * @author Nixx
     */
    public function editPromoteList()
    {
        $proidMain = new ProidMain();
        $promote['proid_id'] = I("get.proid_id");
        if (IS_POST) {
            $promote['pro_lev_id'] = I("post.pro_lev_id");
            $promote['pc_pages_id'] = I("post.pc_pages_id");
            $promote['m_pages_id'] = I("post.m_pages_id");
            $promote['mark'] = I("post.mark");//2-单元  1-计划
            if (!$promote['m_pages_id'] || !$promote['pc_pages_id']) {
                $this->ajaxReturn(1, '请选择模板');
            }
            $backInfo = $proidMain->editPromoteInfo($promote);
            if($backInfo['code'] != 0){
                $this->ajaxReturn(3, '修改失败');
            }
            $this->success('修改成功', 0, U('System/Proid/index',array('proid_id'=>$promote['proid_id'])));
        }else{
            $pro['system_user_id'] = $this->system_user_id;           
            $pro['status'] = 1;
            $res = $proidMain->getProList($pro,'0,100');
            $data['proidList'] = $res['msg'];
            $promote['status'] = 1;
            $data['proid_id'] = $promote['proid_id'];
            $pc['terminal_id'] = 2;
            $m['terminal_id'] = 1;
            $pcPagesTypeList = $proidMain->getPagesType($pc);
            $data['pcPagesTypeList'] = $pcPagesTypeList['msg'];
            $mPagesTypeList = $proidMain->getPagesType($m);
            $data['mPagesTypeList'] = $mPagesTypeList['msg'];
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
        $proidMain = new ProidMain();
        $prom['promote_id'] = I('post.promote_id');
        if (!$prom['promote_id']) {
            $this->ajaxReturn(1, '请选择要删除的计划');
        }
        $backInfo = $proidMain->deletePro($prom);
        if ($backInfo['code'] != 0) {
            $this->ajaxReturn(2, '删除失败');
        }
        $promote = $proidMain->getPromoteInfo($prom);
        $this->success('删除成功', 0, U('System/Proid/index',array('proid_id'=>$promote['msg']['proid_id'])));
    }

    /**
     * 获取推广计划id
     * @author Nixx
     */
    public function prolevPlanList()
    {
        $proidMain = new ProidMain();
        $proid_id = I('post.proid_id');
        if (!$proid_id) {
            $this->ajaxReturn(1, '参数丢失');
        }
        $res = $proidMain->getProLevPlanList($proid_id);
        $proLevPlanList = $res['msg'];
        if (!$proLevPlanList) {
            $this->ajaxReturn(2, '该账号下尚未添加计划');
        } 

        $this->ajaxReturn(0, '数据获取成功', $proLevPlanList);

    }

    /**
     * 获取推广计划单元id
     * @author Nixx
     */
    public function prolevPlanunitList()
    {
        $proidMain = new ProidMain();
        $pro_lev_id = I('post.pro_lev_id');
        if (!$pro_lev_id) {
            $this->ajaxReturn(1, '参数丢失');
        }
        $res = $proidMain->getProLevPlanunitList($pro_lev_id);
        $proLevPlanunitList = $res['msg'];
        if (!$proLevPlanunitList) {
            $this->ajaxReturn(2, '获取失败');
        }  
        $this->ajaxReturn(0, '数据获取成功', $proLevPlanunitList);

    }


    /**
     * 推广计划导入模板列表页
     * @author 
     */
    public function setPages()
    {   
        $proidMain = new ProidMain();
        $proid_id = I("get.proid_id");
        $setPages['system_user_id'] = $this->system_user_id;
        $setPages['type'] = 1;//1-代表推广
        $res = $proidMain->getSetPages($setPages);
        $pagesList = $res['msg'];
        foreach ($pagesList as $key => $value) {
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
        $proidMain = new ProidMain();
        $proid_id = I("get.proid_id");
        if (IS_POST) {
            $setPages = I("post.");           
            if (!$setPages['pagesname']) {
                $this->ajaxReturn(1, '请填写模板名称');
            }
            if (!$setPages['sign']) {
                $this->ajaxReturn(2, '请至少选择1个表头');
            }
            $setPages['sign'] = explode(',',$setPages['sign']);
            foreach ($setPages['sign'] as $key => $sign) {
                $setPages['sign'][$key] = explode('-',$sign);
            }            
            $setPages['system_user_id'] = $this->system_user_id;
            $setPages['type'] = 1;
            $result = $proidMain->createSetPages($setPages);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            $this->success('设置模板成功', 0, U('System/Proid/setPages', array('proid_id' => $proid_id))); 
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
        $proidMain = new ProidMain();
        $proid_id = I("get.proid_id");
        $setpages['setpages_id'] = I("post.setpages_id");
        $backInfo = $proidMain->delSetPages($setpages);
        if ($backInfo['code'] == 0) {
            $this->success('删除成功', 0, U('System/Proid/setPages', array('proid_id' => $proid_id)));
        }
        $this->ajaxReturn(1,'删除失败');

    }

    /**
     * 修改模板
     * @author 
     * 
     */
    public function editSetTemplate()
    {   
        $proidMain = new ProidMain();
        $proid_id = I("get.proid_id");
        $setpages_id = I("get.setpages_id");
        if (IS_POST) {
            $setpages = I("post.");
            $setpages['setpages_id'] = $setpages_id;
            $setpages['sign'] = explode(',',$setpages['sign']);
            foreach ($setpages['sign'] as $key => $sign) {
                $setpages['sign'][$key] = explode('-',$sign);
            } 
            $result = $proidMain->editSetPages($setpages);
            if ($result['code'] != 0) {
                $this->ajaxReturn(1, $result['msg']);
            }
            $this->success('修改模板成功', 0, U('System/Proid/setPages', array('proid_id' => $proid_id))); 
        }
        $setpages['system_user_id'] = $this->system_user_id;
        $setpages['setpages_id'] = $setpages_id;
        $result = $proidMain->getSetPages($setpages);
        if($result['code'] != 0)
        {
            $this->error('无法获取模板信息！');
            exit;
        }
        $pagesInfo=$result['msg'][0];
        $head_info=D('Setpageinfo')->where(array('setpages_id'=> $setpages_id))->select();  
        $head_name_arr=array();
        foreach ($head_info as $key => $value) {
            $head_name_arr[]=$value['headname'];
        }
        $pagesInfo['head_info']=$head_info;
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
        $proidMain = new ProidMain();
        $channelMain = new ChannelMain();
        $proid_id = I("get.proid_id");
        if (!$proid_id) {
            $this->error('账号参数丢失，有bug');
        }
        $proid = $proidMain->getProInfo($proid_id);
        $proidInfo = $proid['msg'];
        if(IS_POST)
        {
            session('faile_input', null);
            session('success_input', null);
            session('newPromoteList', null);
            session('letters', null);
            $pc_pages_id = I("post.pcPagesType_id");
            $m_pages_id  = I("post.mPagesType_id");
            $setpages_id = I("post.setpages_id");
            if (empty($pc_pages_id) || empty($m_pages_id)) {
                $this->error('请选择模板');
            }
            if (!empty($_FILES['file'])) {
                $exts = array('xls','xlsx');
                $rootPath = './Public/';
                $savePath = 'promote/';
                $uploadFile = $this->uploadFile($exts,$rootPath,$savePath);
                $filename = $rootPath.$uploadFile['file']['savepath'].$uploadFile['file']['savename'];
            }
            $datas = importExecl($filename);  
            unlink($filename);
            $setPagesInfo = $proidMain->getSetPagesInfo($setpages_id);
            $letters = $setPagesInfo['msg'];
            foreach ($letters as $k1 => $letter) {
                $k1 = $k1+1;
                $pro[$k1][] = $letter['pagehead'];
                $pro[$k1][] = $letter['headname'];
            }          
            /*对生成的数组进行字段对接*/
            foreach ($pro as $key => $p) {
                foreach ($datas as $k => $v){
                    if ($k>1) {
                        for ($i=0; $i < count($v); $i++) {
                            $keys = array_keys($v);
                            foreach ($keys as $k2 => $v1) {
                                if ($p[0] == $v1) {
                                    $promoteList[$k-2]["$p[1]"] = $v[$v1];
                                }
                            }
                        }
                        $promoteList[$k-2]['proid_id'] = $proid_id;
                        $promoteList[$k-2]['pc_pages_id'] = $pc_pages_id;
                        $promoteList[$k-2]['m_pages_id'] = $m_pages_id;
                    }
                }
            }
            //对数据进行处理
            foreach ($promoteList as $key => $promote) {
                $servicecode['system_user_id'] = $this->system_user_id;
                $servicecode['status'] = 1;
                if ($promote['pcservice']) {
                    $servicecode['servicecode'] = $promote['pcservice'];
                    $servicecode['terminal_id'] = 2; //PC端
                    $sercode = $proidMain->getServicecode($servicecode);
                    $serviceInfo = $sercode['msg'];
                    if ($serviceInfo) {
                        $promote['pcservice_id'] = $serviceInfo['servicecode_id'];
                    }else{
                        $pcser = $proidMain->addServicecode($servicecode);
                        $pcservice_id = $pcser['msg'];
                        if ($pcservice_id) {
                            $promote['pcservice_id'] = $pcservice_id;
                        }
                    }
                }else{
                    $promote['pcservice_id'] = $proidInfo['pcservice_id'];
                }
                if ($promote['mservice']) {
                    $servicecode['servicecode'] = $promote['mservice'];
                    $servicecode['terminal_id'] = 1; //移动端
                    $sercode = $proidMain->getServicecode($servicecode);
                    $serviceInfo = $sercode['msg'];
                    if ($serviceInfo) {
                        $promote['mservice_id'] = $serviceInfo['servicecode_id'];
                    }else{
                        $mser = $proidMain->addServicecode($servicecode);
                        $mservice_id = $mser['msg'];
                        if ($mservice_id) {
                            $promote['mservice_id'] = $mservice_id;
                        }
                    }
                }else{
                    $promote['mservice_id'] = $proidInfo['mservice_id'];
                }             
                if ($promote['pc_pages']) {
                    preg_match("/promote=([0-9]*)/", $promote['pc_pages'], $m);
                    if($m){
                        $promote['promote_id']=$m[1];
                    } 
                }elseif($promote['m_pages']){
                    preg_match("/promote=([0-9]*)/", $promote['pc_pages'], $m);
                    if($m){
                        $promote['promote_id']=$m[1];
                    } 
                }
                unset($promote['pc_pages']);
                unset($promote['m_pages']);
                if (!$promote['keyword'] || (!$promote['plan'] && $promote['planunit']) || ($promote['plan'] && !$promote['planunit'])) {
                    $errorData[$key] = $promoteList[$key];
                    $errorData[$key]['msg'] = '缺少关键字、有单元无计划，不是请联系程序猿哥哥';
                    unset($promoteList[$key]);
                }
                $promoteList[$key] = $promote;
            }
            foreach ($promoteList as $key => $promote) {               
                if ($promote['promote_id']) {
                    $info = $proidMain->getPromInfo($promote['promote_id']);
                    $promoteInfo = $info['msg'];
                }else{
                    $pross['plan'] = $promote['plan'];
                    $pross['planunit'] = $promote['planunit'];
                    $pross['keyword'] = $promote['keyword'];
                    $pross['proid_id'] = $proid_id;
                    $info = $proidMain->getPromoteInfo($pross);
                    $promoteInfo = $info['msg'];
                }
                if ($promoteInfo !== '没有数据') {
                    if (!$promote['pc_pages_id']) {
                        $promote['pc_pages_id'] = $promoteInfo['pc_pages_id'];
                    }
                    if (!$promote['m_pages_id']) {
                        $promote['m_pages_id'] = $promoteInfo['m_pages_id'];
                    }
                    $promote['promote_id'] = $promoteInfo['promote_id'];
                    $updatepromote = $proidMain->editPromote($promote);
                    $own['promote_id'] = $promote['promote_id'];
                    $proInfo = $proidMain->getPromoteInfo($own);
                    $ps['plan'] = $proInfo['msg']['plan'];
                    $ps['planunit'] = $proInfo['msg']['planunit'];
                    $ps['keyword'] = $proInfo['msg']['keyword'];
                    $ps['pc_pages'] = $proInfo['msg']['pc_pages'];
                    $ps['m_pages'] = $proInfo['msg']['m_pages'];
                    $newPromoteList[$key] = $ps;
                }else{
                    $prolev['proid_id'] = $promote['proid_id'];
                    $prolev['status'] = 1;
                    //只有计划没有单元
                    if ($promote['plan'] && !$promote['planunit']) {
                        unset($promote['planunit']);
                        $prolev['name'] = $promote['plan'];
                        $proLevInfo = $proidMain->getProLevInfo($prolev);
                        if ($proLevInfo['code'] == 0) {
                            $pro_lev_id = $proidMain->createProLev($prolev);
                            $promote['pro_lev_id'] = $pro_lev_id['msg'];
                        }else{
                            $promote['pro_lev_id'] = $proLevInfo['msg']['pro_lev_id'];
                        }
                    }//有计划有单元
                     elseif ($promote['plan'] && $promote['planunit']) {
                        $prolev['name'] = $promote['plan'];
                        $proLevInfo = $proidMain->getProLevInfo($prolev);
                        if ($proLevInfo['code'] == 0) {
                            $plan_lev_id = $proidMain->createProLev($prolev);
                            $prolev['name'] = $promote['planunit'];
                            $prolev['pid'] = $plan_lev_id['msg'];
                            $pro_lev_id = $proidMain->createProLev($prolev);
                            $prolev['pid'] = 0; //重置pid为0
                            $promote['pro_lev_id'] = $pro_lev_id['msg'];
                        }else{
                            $prolev['name'] = $promote['planunit'];
                            $prolev['pid'] = $proLevInfo['msg']['pro_lev_id'];
                            $punitLevInfo = $proidMain->getProLevInfo($prolev);
                            if ($punitLevInfo['code'] == 0) {
                                $promote['pro_lev_id'] = $punitLevInfo['msg']['pro_lev_id'];
                            }else{
                                $pro_lev_id = $proidMain->createProLev($prolev);
                                $promote['pro_lev_id'] = $pro_lev_id['msg'];
                            }
                            $prolev['pid'] = 0; //重置pid为0
                        }   
                    }
                    $result = $proidMain->createPromote($promote);
                    if ($result['code'] != 0) {
                        unset($promoteList[$key]);
                        continue;
                    }else{
                        $own['promote_id'] = $result['msg'];
                        $info = $proidMain->getPromoteInfo($own);
                        if ($info['code'] == 0) {
                            $proInfo = $info['msg'];
                            $ps['plan'] = $proInfo['plan'];
                            $ps['planunit'] = $proInfo['planunit'];
                            $ps['keyword'] = $proInfo['keyword'];
                            $ps['pc_pages'] = $proInfo['pc_pages'];
                            $ps['m_pages'] = $proInfo['m_pages'];
                            $newPromoteList[$key] = $ps;
                        }
                    }
                }
            }
            session('faile_input', $errorData);
            session('success_input', $promoteList);
            session('newPromoteList', $newPromoteList);           
            session('letters', $datas[1]);           
            $this->redirect('/System/Proid/inputClient', array('proid_id'=>$proid_id));
        }else{
            $set['system_user_id'] = $this->system_user_id;
            $set['type'] = 1;    
            $res = $proidMain->getSetPages($set);
            $setPages = $res['msg'];
            $pc['terminal_id'] = 2;
            $m['terminal_id'] = 1;
            $pcPagesTypeList = $proidMain->getPagesType($pc);
            $pro['pcPagesTypeList'] = $pcPagesTypeList['msg'];
            $mPagesTypeList = $proidMain->getPagesType($m);
            $pro['mPagesTypeList'] = $mPagesTypeList['msg'];
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
        $proidMain = new ProidMain();
        $proid_id = I('get.proid_id');
        if (IS_POST) {
            $keyword = I('post.keyword');        
            $pro_lev_id = I('post.pro_lev_id');                     
            $pro_lev_ids = I('post.pro_lev_ids');
            $excel_key_value = array(
                'plan'=>'推广计划名称',
                'planunit'=>'推广单元名称',
                'keyword'=>'关键词名称',
                'pc_pages'=>'PC模板',
                'm_pages'=>'移动模板',
            );
            $letter = array('A','B','C','D','E');
            $new_result = array();
            if(!$proid_id) {
                $this->error('缺省参数');
            }
            //若带了关键词则只导出该计划，否则导出所有单元或者该账号下所有计划
            if (!empty($keyword)) {
                $pList = $proidMain->searchName($keyword, $proid_id);
                $promoteList = $pList['msg'];
                if ($promoteList != 0) {
                    $this->error('没有数据');
                }
                foreach ($promoteList as $key => $promote) {
                    $p['promote_id'] = $promote['promote_id'];
                    $datas = $proidMain->getPromoteInfo($p);
                    $result = $datas['msg'];
                    unset($result['proid_id']);
                    unset($result['promote_id']);
                    $rresult[$key] = $result;
                }
                foreach ($rresult as $key => $value) {
                    foreach ($excel_key_value as $key2 => $value2) {
                        $new_result[$key][$key2] = $value[$key2];
                    }
                }            
            }elseif($pro_lev_id || $pro_lev_ids){
                if ($pro_lev_id) {
                    $datas = $proidMain->getPromoteInfoByProlevid($pro_lev_id);
                    if($datas['code'] != 0) {
                        $this->ajaxReturn(3,'没有数据');
                    }
                }elseif ($pro_lev_ids) {
                    $datas = $proidMain->getPromoteInfoByProlevid($pro_lev_ids);
                    if($datas['code'] != 0) {
                        $this->ajaxReturn(4,'没有数据');
                    } 
                }
                $result = $datas['msg'];
                foreach ($result as $key => $res) {
                    unset($res['pid']);
                    unset($res['name']);
                    unset($res['remark']);
                    unset($res['status']);
                    $sel[] = $res['pro_lev_id'];
                }
                $res['pro_lev_id'] = array("IN", $sel);
                $p = $proidMain->getPromoteInfos($res);
                $promoteList = $p['msg'];
                foreach ($promoteList as $k => $value) {        
                    foreach ($excel_key_value as $key => $value1) {
                        $new_result[$k][$key] = $value[$key];
                    }
                }
            }else{
                $pro['proid_id'] = $proid_id;
                $p = $proidMain->getPromoteInfos($pro);
                $promoteList = $p['msg'];
               
                foreach ($promoteList as $k => $value) {        
                    foreach ($excel_key_value as $key => $value1) {
                        $new_result[$k][$key] = $value[$key];
                    }
                }   
            }
            $filename = "proPlan";
            outExecl($filename,array_values($excel_key_value),$new_result,$letter);
        }
        $proid = $proidMain->getProInfo($proid_id);
        $pro['proid'] = $proid['msg'];
        $promoteList = $proidMain->getProLevPlanList($proid_id);  
        $pro['promoteList'] = $promoteList['msg'];        
        $this->assign('pro', $pro);
        $this->display();
    }

    

    
}
