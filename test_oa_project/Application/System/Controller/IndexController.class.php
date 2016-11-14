<?php
/*
|--------------------------------------------------------------------------
| 后台首页控制器
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\SystemUserController;
use Common\Model\ValidateModel;
use Org\Util\Rbac;

class IndexController extends SystemController
{

    var $system_userDB; //系统用户数据句柄
    var $system_user; //系统用户信息

    /**
     * thinkPHP构造
     * (non-PHPdoc)
     * @see \Common\Controller\BaseController::_initialize()
     * @author cq
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     * @author  cq
     */
    public function index()
    {
        if(IS_POST){
            $request = I('post.');
            if($request['type']=='editStatus'){
                $edit_data['createtime']=time();
                $edit_data['isovertime']='0';
                $edit_data['isget']='0';
                $edit_data['status']=$request['status'];
                $edit_data['user_id']='0';
                $edit_data['markstring']= null;
                $reflag = D('SystemUser')->editSystemEngagedStatus($edit_data,$this->system_user_id);
                if($reflag!==false) $this->ajaxReturn('0','状态修改成功');
                else $this->ajaxReturn('1','状态修改失败');
            }elseif($request['type']=='getMsgList'){
                $systemUserController = new SystemUserController();
                $where['system_user_id'] = $this->system_user_id;
                $where['status'] = 1;
                $msgList = $systemUserController->getMsgList($where);
                if($msgList['code']==0){
                    $this->ajaxReturn('0','获取成功', $msgList['data']);
                }
                $this->ajaxReturn('1','获取失败');
            }else{
                //获取员工忙线状态
                $engagedStatus =  D('SystemUser','Service')->getSystemEngagedStatus();
                if(!empty($engagedStatus['data'])){
                    if($engagedStatus['data']['status']==1){
                        if($engagedStatus['data']['isget']==1){
                            //提示有客户到访
                            $edit_data['isget']='0';
                             D('SystemUser','Service')->editSystemEngagedStatus($edit_data);
                            $userInfo = D('User')->getUser(array('user_id'=>$engagedStatus['data']['user_id']));
                            if(!empty($userInfo)){
                                $userInfo['username'] = decryptPhone($userInfo['username'],C('PHONE_CODE_KEY'));
                            }
                            $this->ajaxReturn('10','您有客户到访，请到前台接待',$userInfo);
                        }else{
                            //提示超时时间
                            if( (time()-$engagedStatus['data']['createtime'])>3600 ) {
                                //是否超时警告
                                if($engagedStatus['data']['isovertime']==1){
                                    $edit_data['isovertime']='0';
                                    D('SystemUser','Service')->editSystemEngagedStatus($edit_data);
                                    //警告措施
                                }
                                $this->ajaxReturn('2','已超过60分钟');
                            }elseif( (time()-$engagedStatus['data']['createtime'])>3000 ) {
                                $this->ajaxReturn('2','已超时50分钟');
                            }elseif( (time()-$engagedStatus['data']['createtime'])>2400 ) {
                                $this->ajaxReturn('2','已超时40分钟');
                            }elseif( (time()-$engagedStatus['data']['createtime'])>1800 ){
                                $this->ajaxReturn('2','已超时30分钟');
                            }else{
                                $this->ajaxReturn('3','忙线还未超时');
                            }
                        }
                    }else{
                        $this->ajaxReturn('0','无任何到访通知');
                    }
                }
                $this->ajaxReturn('0','无任何到访通知');
            }
        }
        //是否滴答URL
        $get_url = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $is_dida = 0;
        if(strstr($get_url,'crm.didazp')){
            $is_dida = 1;
        }
        //获取左侧边栏
        $newSideber = $this->_sideMenu();
        //获取员工忙线状态
        $engagedStatus =  D('SystemUser','Service')->getSystemEngagedStatus();
        $this->getCurSystemUpdateInfo();
        //模板变量赋值
        $this->assign('is_dida', $is_dida);
        $this->assign('engagedStatus', $engagedStatus);
        $this->assign('sidebar',$newSideber);
        $this->display();
    }

    /**
     * 设置侧边菜单
     * @author cq
     */
    protected function _sideMenu()
    {
        $system_user_role = session('system_user_role');
        if(session('sidebar')){
            $sidebar = session('sidebar');
        }else{
            if($system_user_role[0]['id']!=C('ADMIN_SUPER_ROLE')){
                $re_sidebar = D('Role','Service')->getRoleNode();
            }else{
                $re_sidebar = D('Role','Service')->getRoleNodeAll();
            }
            $sidebar = $re_sidebar['data'];
            session('sidebar',$sidebar);
        }
        $newSideber = array();
        $childNodes = array();
        $i = 0;
        foreach ($sidebar as $k => $v) {
            if ($v['name'] == CONTROLLER_NAME){
                $controllerName = $v['title'];
            }
            if ($v['display'] == 3){
                $newSideber[$k] = $v;
            }
            unset($newSideber[$k]['children']);
            if (is_array($v['children'])) {
                foreach ($v['children'] as $c_key => $c_value) {
                    if ($v['name'] == CONTROLLER_NAME && $c_value['name'] == ACTION_NAME) $actionName = $c_value['title'];
                    if ($c_value['display'] == 3) {
                        $newSideber[$k]['children'][$c_key] = $c_value;
                        $url = U(MODULE_NAME . '/' . $v['name'] . '/' . $c_value['name']);
                        $newSideber[$k]['children'][$c_key]['url'] = $url;

                        //获得用户的所有子节点
                        $childNodes[$i] = $newSideber[$k]['children'][$c_key];
                        $i++;
                    }
                }
            }
        }
        return $newSideber;
    }

    /**
     * 读取当前数据库表中的系统更新信息,同时新增realname 和date 信息
     * @author cq
     */
    public function  getCurSystemUpdateInfo($where=null,$limit)
    {
        $sysUpdateData = D('SystemUpdate')->getSystemUpdateInfo($where,$limit);
        if (!empty($sysUpdateData['data'])) {

            foreach($sysUpdateData['data'] as $k => $v){

                if(empty($v['system_update_id'])){
                    $sysUpdateData['data'][$k]['system_update_id'] = '--';//空格字符串,防止页面显示不对齐的现象
                }
                if(empty($v['uptitle'])){
                    $sysUpdateData['data'][$k]['uptitle'] = '--';
                }
                if(empty($v['realname'])){
                    $sysUpdateData['data'][$k]['realname'] = '--';
                }
                if(empty($v['createtime'])){
                    $sysUpdateData['data'][$k]['createtime'] = '--';
                }else{
                    $sysUpdateData['data'][$k]['createtime'] = date('Y-m-d  H:i:s',$v['createtime']);
                }
            }
            session('sysUpdateData', $sysUpdateData['data']);
            $this->assign('sysUpdateData', $sysUpdateData['data']);
        }
        return $sysUpdateData;
    }

    /**
     * 员工档案-添加(入口判断 员工是否完善资料)
     * @author zgt
     */
    public function addSystemUserInfo()
    {
        //实例化
        if (IS_POST) {
            //获取参数 验证
            $system_user_id = $this->system_user_id;
            $request = I('post.');
            if (empty($system_user_id)) $this->ajaxReturn(1, '非法操作');
            if (empty($request['birthday'])) $this->ajaxReturn(300, '生日不能为空', 'birthday');
            if (empty($request['nativeplace'])) $this->ajaxReturn(301, '籍贯不能为空', 'nativeplace');
            if (empty($request['education_id'])) $this->ajaxReturn(302, '学历不能为空');
            if (empty($request['school'])) $this->ajaxReturn(303, '毕业学校不能为空', 'school');
            if (empty($request['plivatemail'])) $this->ajaxReturn(304, '个人邮箱不能为空', 'plivatemail');
            $preg_ramil = "/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i";
            if(!preg_match($preg_ramil, $request['plivatemail'])) $this->ajaxReturn(201, '个人邮箱格式有误', 'plivatemail');
            session('addSystemUserInfo', $request);
            //进入下一步
            $this->ajaxReturn(0, '完善个人信息成功，请继续完善任职信息！', U('System/Index/addSystemUserInfoTwo'));
        } else {
            //获取基本信息
            $systemUserInfo = D('SystemUser','Service')->getSystemUserInfo(array('system_user_id'=>$this->system_user_id));
            $data['userInfo'] = $systemUserInfo['data'];
            //获取学历表
            $data['educationAll'] = C('FIELD_STATUS.EDUCATION_ARRAY');
            $data['on_info'] = session('addSystemUserInfo');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 员工档案-添加(入口判断 员工是否完善资料) 第二步
     * @author zgt
     */
    public function addSystemUserInfoTwo()
    {
        if (IS_POST) {
            //获取参数 验证
            $system_user_id = $this->system_user_id;
            $session_request = session('addSystemUserInfo');
            $request = array_merge($session_request, I('post.'));
            if (empty($system_user_id)) $this->ajaxReturn(1, '非法操作');
            //获取 数据判断
            $request['isuserinfo'] = 1;
            $addSystemUserInfo = D('SystemUser','Service')->editSystemUserInfo($request);
            if ($addSystemUserInfo['code']==0) {
                $system_user = $this->system_user;
                $system_user['isuserinfo'] = 1;
                session('system_user', $system_user);
                $this->ajaxReturn(0, '员工档案添加成功', U('System/Index/index'));
            } else {
                $this->ajaxReturn($addSystemUserInfo['code'], $addSystemUserInfo['msg']);
            }
        } else {
            //获取基本信息
            $systemUserInfo = D('SystemUser','Service')->getSystemUserInfo(array('system_user_id'=>$this->system_user_id));
            $data['userInfo'] = $systemUserInfo['data'];
            //添加员工状态
            $system_info = session('addSystemUserInfo');
            $system_info['usertype'] = $data['userInfo']['usertype'];
            $system_info['entrytime'] = date('Y-m-d', $data['userInfo']['entrytime']);
            $system_info['straightime'] = date('Y-m-d', $data['userInfo']['straightime']);
            $system_info['check_id'] = $data['userInfo']['check_id'];
            session('addSystemUserInfo', $system_info);
            $data['url_addSystemUserInfo'] = U('System/Index/addSystemUserInfo');
            $this->assign('data', $data);
            $this->display();
        }
    }


    /**
     * 设置主页的路径
     * @author cq
     */
    public function  main()
    {
        if (IS_POST) {
            //获得提交的自定义节点
            $nodes = I('post.nodes');
            $result = D("Node", 'Service')->subNodes($nodes);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result['code'], $result['msg']);
            }
            $this->ajaxReturn(0, '添加成功', U('System/Index/main'));
        }
        
        $result = D('Node', 'Service')->getNodesData();
        foreach ($result['data']['userDefaultNodes'] as $key => $v) {
            $in_array[] = $v['node_id'];
        }
        $siderbar = session('sidebar');
        //获取系统更新
        $where_systemUpdate['page'] = '1,30';
        $sysUpdateData = D('SystemUpdate', 'Service')->getSystemUpdateList($where_systemUpdate);
        $data['systemUpdateList'] = $sysUpdateData['data'];
        $this->assign('data', $data);
        $this->assign('in_array', $in_array);
        $this->assign('navClass', $result['data']['navClass']);
        $this->assign('default_nodes', $result['data']['userDefaultNodes']);
        $this->assign('siderbar', $siderbar);
        $this->display();
    }

}