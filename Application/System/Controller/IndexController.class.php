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
        }else{
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

            $this->assign('is_dida', $is_dida);
            $this->assign('engagedStatus', $engagedStatus);
            //模板变量赋值
            $this->assign('sidebar',$newSideber);
            $this->display();
        }
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
        $SystemUserModel = D('SystemUser');
        if (IS_POST) {
            //获取参数 验证
            $checkform = new \Org\Form\Checkform();
            $system_user_id = $this->system_user_id;                 //用户ID
            $request = I('post.');
            if (empty($system_user_id)) $this->ajaxReturn(1, '非法操作');
            if (empty($request['birthday'])) $this->ajaxReturn(1, '生日不能为空', '', 'birthday');
            if (empty($request['nativeplace'])) $this->ajaxReturn(1, '籍贯不能为空', '', 'nativeplace');
            if (empty($request['education_id'])) $this->ajaxReturn(1, '学历不能为空');
            if (empty($request['school'])) $this->ajaxReturn(1, '毕业学校不能为空', '', 'school');
            if (empty($request['plivatemail'])) $this->ajaxReturn(1, '个人邮箱不能为空', '', 'plivatemail');
            if(!$checkform->isEmail($request['plivatemail'])) $this->ajaxReturn(1, '个人邮箱格式有误', '', 'plivatemail');
            $request['birthday'] = strtotime($request['birthday']);
            session('addSystemUserInfo', $request);
            //进入下一步
            $this->ajaxReturn(0, '完善个人信息成功，请继续完善任职信息！', U('System/Index/addSystemUserInfoTwo'));
        } else {
            //获取基本信息
            $data['userInfo'] = $SystemUserModel->getSystemUserInfo($this->system_user_id);
            //学历
            if ($data['systemUserInfo']['education_id']) {
                $edu = D('Education')->getEducationInfo($data['systemUserInfo']['education_id']);
                $data['systemUserInfo']['educationname'] = $edu;
            }
            //获取学历表
            $data['educationAll'] = D('Education')->getAllEducation();
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
        //实例化
        $SystemUserModel = D('SystemUser');
        if (IS_POST) {
            //获取参数 验证
            $session_request = session('addSystemUserInfo');
            $system_user_id = $this->system_user_id;
            $request = array_merge($session_request, I('post.'));
            if (empty($system_user_id)) $this->ajaxReturn(1, '非法操作');
            //获取 数据判断
            $addSystemUserInfo = $SystemUserModel->addSystemUserInfo($request, $system_user_id);
            if (!empty($addSystemUserInfo)) {
                $system_user = $this->system_user;
                $system_user['isuserinfo'] = 1;
                session('system_user', $system_user);
                $this->ajaxReturn(0, '员工档案添加成功', U('System/Index/index'));
            } else {
                $this->ajaxReturn(1, '数据操作失败');
            }
        } else {
            //获取基本信息
            $data['userInfo'] = $SystemUserModel->getSystemUserInfo($this->system_user_id);
            //员工状态
            $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
            foreach($data['systemUserStatus'] as $k=>$v){
                if($v['text']=='离职'){
                    unset($data['systemUserStatus'][$k]);
                }
            }
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
            if (empty($nodes)) $this->ajaxReturn('1', '没有选择节点');
            $nodes = explode(',', $nodes);

            D('DefineNodes')->delDefineNodes($this->system_user_id);
            foreach ($nodes as $k => $v) {
                if (!empty($v)) {
                    $data = array('system_user_id' => $this->system_user_id, 'role_id' => $this->system_user_role_id, 'node_id' => $v, 'sort'=>$k);
                    //新增导航节点
                    $result = D('DefineNodes')->addDefineNode($this->system_user_id, $data);
                }
            }
            //判断返回数据
            if (!$result) {
                $this->ajaxReturn(1, '数据添加失败');
            } else {
                session('default_nodes_' . $this->system_user_role_id, null);
                $this->success('添加成功', 0, U('System/Index/main'));
            }
        } else {

            $this->display();
        }
    }

    /*
    *获取用户自定义的节点;
    * @author  cq
    */
    public function  getUserDefineNodes()
    {
//        session('default_nodes_' . $this->system_user_role_id, null);//cq
        if (session('default_nodes_' . $this->system_user_role_id)) {
            $userDefaultNodes = session('default_nodes_'.$this->system_user_role_id);
        } else {
            /* $userDefaultNodes = D('DefineNodes')->where(array('system_user_id' => $this->system_user_id, 'role_id' => $this->system_user_role_id))
                 ->join('LEFT JOIN zl_node on zl_node.id = zl_define_nodes.node_id')->select();*/
            $userDefaultNodes = D('DefineNodes')->getUserDefaultNodes($this->system_user_id, $this->system_user_role_id);

            if (empty($userDefaultNodes)) {
                $tempData = session('user_child_nodes');
                //如果孩子节点数大于8则默认取前面8个,否则取全部
                if (count($tempData) <= 8) {
                    $userDefaultNodes = $tempData;
                } else {
                    for ($i = 0; $i < 8; $i++) {
                        $userDefaultNodes[$i] = $tempData[$i];
                    }
                }
            }
            session('default_nodes_' . $this->system_user_role_id, $userDefaultNodes);
        }
        return $userDefaultNodes;
    }

    /**
     * 更新系统记录
     */
    public function  updateRecord()
    {
        if (IS_POST) {
            $updateData = I('post.');
            $type = $updateData['type'];
            $where = array('system_update_id' => $updateData['update_id']);
            if (1 == $type) {  //修改页面
                $curUpdateInfo = D('SystemUpdate')->getSystemUpdateInfo($where);
                session('curUpdateInfo', $curUpdateInfo[data]);
                $this->success('跳转到修改页面', 0, U('System/Index/addService'));

            } else {
                //删除指定一条信息
                $result = D('SystemUpdate')->delUpdateInfo($where);
                if ($result) {
                    $this->success('删除成功', 0, U('System/Index/updateRecord'));
                } else {
                    $this->ajaxReturn(1, '删除失败');
                }
            }
        } else {
            session('curUpdateInfo', null);
            //获取参数 页码
            $request = I('get.');
            $re_page = isset($request['page']) ? $request['page'] : 1;
            unset($request['page']);

            $perPageNum = C('PER_PAGE_NUM');
            $perPageNumLimit = ','.$perPageNum;
            $limit = (($re_page - 1) * $perPageNum).$perPageNumLimit; //分页显示,每页显示30条
            $sysUpdateData = $this->getCurSystemUpdateInfo('',$limit);

            //加载分页类
            $paging = $this->Paging($re_page,$perPageNum,$sysUpdateData['count'],$request);
            $this->assign('paging', $paging);
            $this->assign('system_user_role_id', $this->system_user_role_id);

            $this->display();
        }
    }

    /**
     *更新系统详情
     */
    public function updateDetail()
    {
        $updateItemId = I('get.system_update_id');
        $where = array('system_update_id' =>$updateItemId);
        $updateItem = D('SystemUpdate')->getSystemUpdateInfo($where);

        $this->assign('updateItem',  $updateItem['data'][0]);
        $this->display();
    }

    /**
     * 添加系统更新
     */
    public function  addService()
    {
        if (IS_POST) {
            $editContent = I('post.');

            if (!empty($editContent)) {

                if(empty($editContent['title'])){
                    $this->ajaxReturn('1','标题不能为空','','title');
                }
                if(empty($editContent['content'])){
                    $this->ajaxReturn('2','内容不能为空');
                }

                $data = array('system_user_id' => session('system_user_id'), 'uptitle' => $editContent['title'],
                    'upbody' => $editContent['content'], 'createtime' => time());
                if (1 == $editContent['type']) { //修改

                    $curUpdateInfo = session('curUpdateInfo');
                    $updateId = $curUpdateInfo[0]['system_update_id'];
                    $where = array('system_update_id' => $updateId);
                    $result = D('SystemUpdate')->modifyUpdateInfo($where, $data);

                    if ($result > 0) {
                        $this->success('修改成功', 0, U('Index/updateRecord'));
                    } else {
                        $this->ajaxReturn(1, '修改失败，请重新修改');
                    }
                } else {//添加

                    $result = D('SystemUpdate')->addNewUpdateInfo($data);
                    if ($result > 0) {
                        $this->success('添加成功', 0, U('Index/updateRecord'));
                    } else {
                        $this->ajaxReturn(1, '添加失败');
                    }
                }
            }
        } else {
            $curUpdateInfo = session('curUpdateInfo');
            if (!empty($curUpdateInfo)) {
                $jumpType = 1; //修改跳转
                $this->assign('curUpdateInfo', $curUpdateInfo);
            } else {
                $jumpType = 2; //添加跳转
            }
            $this->assign('jumpType', $jumpType);
            $this->display();
        }
    }



}