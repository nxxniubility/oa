<?php
/*
|--------------------------------------------------------------------------
| 人事管理控制器
|--------------------------------------------------------------------------
| createtime：2016-04-14
| updatetime：2016-05-03
| updatename：zgt
*/
namespace System\Controller;
use Common\Controller\EducationController;
use Common\Controller\SystemController;
use Common\Controller\NodeController as NodeMain;
use Common\Controller\RoleController as RoleMain;
use Common\Controller\SystemUserController as SystemMain;
use Common\Controller\DepartmentController as DepartmentMain;
use Common\Controller\ZoneController as ZoneMain;

class PersonnelController extends SystemController {

    //控制器前置
    public function _initialize(){
        parent::_initialize();
    }

    /*
     * ****************************************************************
     *  部门管理
     * ****************************************************************
     * @author zgt
     */
    public function department()
    {
        //获取参数
        $request = I('get.');
        //返回、修改、操作地址
        $data['url_addDepartment'] = U('System/Personnel/addDepartment');
        $data['url_delDepartment'] = U('System/Personnel/delDepartment');
        $data['url_dispostDepartment'] = U('System/Personnel/dispostDepartment');
        //获取排序 分页参数
        $re_page = isset($request['page'])?$request['page']:1;
        unset($request['page']);
        $order = !empty($request['order'])?str_replace('-',' ',$request['order']):'sort desc';

        if($request['order']=='sort-desc')
		{
			$data['url_sort'] = U('System/Personnel/department').'?order=sort-asc';
        }else {
			$data['url_sort'] = U('System/Personnel/department').'?order=sort-desc';
		}
		
		if($request['order']=='department_id-desc')
		{
			$data['url_department_id'] = U('System/Personnel/department').'?order=department_id-asc';
		}else{
			$data['url_department_id'] = U('System/Personnel/department').'?order=department_id-desc';
		}
        //获取部门管理列表
        $departmentMain = new DepartmentMain();
        $data['departmentAll'] = $departmentMain->getList(null, $order,$re_page.',30');
        //加载分页类
        $data['paging'] = $this->Paging($re_page,30,$data['departmentAll']['count'],$request);
        $this->assign('data', $data);
        $this->display();
    }
    /**
     * 部门管理-添加
     * @author zgt
     */
    public function addDepartment()
    {
        if(IS_POST) {
            //获取参数 验证
            $departmentname = I('post.departmentname',null);
            $sort = I('post.sort',0);
            if(empty($departmentname)) $this->ajaxReturn(1, '部门名称不能为空', '', 'departmentname');
            $departmentMain = new DepartmentMain();
            $result = $departmentMain->create(array('departmentname'=>$departmentname, 'sort'=>$sort));
            //添加部门成功
            if(empty($result)) $this->ajaxReturn(1, '数据添加失败');
            else $this->success('添加部门成功', 0, U('System/Personnel/department'));
        }else{
            $data['url_department'] = U('System/Personnel/department');
            $this->assign('data', $data);
            $this->display();
        }
    }
    /**
     * 部门管理-修改
     * @author zgt
     */
    public function editDepartment()
    {
        $departmentname_id = I('get.dep_id',null);
        if( empty($departmentname_id) )$this->error('非法请求！');
        if(IS_POST) {
            //获取参数 验证
            $departmentname = I('post.departmentname',null);
            //是否只修改排序值 --其他ajax调用
            if(empty($departmentname)) $this->ajaxReturn(1, '部门名称不能为空', '', 'departmentname');
            $requery_data['departmentname'] = $departmentname;
            $requery_data['department_id'] = $departmentname_id;
            $departmentMain = new DepartmentMain();
            $result = $departmentMain->edit($requery_data);
            //修改部门成功
            if(empty($result)) $this->ajaxReturn(1, '数据修改失败');
            else $this->success('修改成功', 0, U('System/Personnel/department'));
        }else{
            //获取相关部门详情
            $departmentMain = new DepartmentMain();
            $data['department'] = $departmentMain->getInfo($departmentname_id);
            $data['url_department'] = U('System/Personnel/department');
            $this->assign('data', $data);
            $this->display();
        }
    }
    /**
     * 部门管理-操作页(删除/修改排序)
     * @author zgt
     */
    public function dispostDepartment()
    {
        if(IS_POST) {
            //获取参数 验证
            $departmentname_id = I('post.departmentname_id',null);
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                if(empty($departmentname_id)) $this->ajaxReturn(1, '部门ID不能为空', '', 'departmentname_id');
                $requery_data['status'] = 0;
                $requery_data['departmentname_id'] = $departmentname_id;
                $departmentMain = new DepartmentMain();
                $result = $departmentMain->edit($requery_data);
                //删除部门
                if(empty($result)) $this->ajaxReturn(1, '数据删除失败');
                else $this->success('删除部门成功', 0, U('System/Personnel/department'));
            }else if( isset($type) && $type=='sort' ){
                $sort_data = I('post.sort_data','');
                if(empty($sort_data)) $this->ajaxReturn(1, '请输入要修改的排序值');
				if($sort_data!='') {
					$sort_data=explode(',',$sort_data);
					$new_sort_data=array();
					foreach($sort_data  as $k=>$v) {
						$tmp=explode('-',$v);
						$new_sort_data[$tmp[0]]=$tmp[1];
					}
					$result = D('Department')->batch_update($new_sort_data,'department_id','sort');
                    //排序修改
                    if($result!==false){
                        F('Cache/Personnel/department', null);
                        $this->ajaxReturn(0, '排序修改成功', U('System/Personnel/position'));
                    }else{
                        $this->ajaxReturn(1, '排序修改失败');
                    }
				}
            }
        }
    }


    /*
     * ****************************************************************
     *  职位管理（原用户权限组role）
     * ****************************************************************
     * @author zgt
     */
    public function position()
    {
        //post=》列表页快捷修改权限
        if(IS_POST){
            $role_id = I('post.role_id');
            //获取职位详情
            $roleMain = new RoleMain();
            $result = $roleMain->getRoleAccess($role_id);
            if(!empty($result)){
                $roleAccess = '';
                foreach($result as $k=>$v){
                    if($k==0){
                        $roleAccess.=$v['node_id'];
                    }else{
                        $roleAccess.=','.$v['node_id'];
                    }
                }
            }
            $this->success('获取职位权成功', 0, $roleAccess);
        }else{
            //获取参数
            $request = I('get.');
            //添加、修改、操作地址
            $data['url_addPosition'] = U('System/Personnel/addPosition');
            $data['url_editPosition'] = U('System/Personnel/editPosition');
            $data['url_dispostPosition'] = U('System/Personnel/dispostPosition');
            //获取排序 分页参数
            $re_page = isset($request['page'])?$request['page']:1;
            $order = !empty($request['order'])?str_replace('-',' ',$request['order']):'sort desc';

            if($request['order']=='sort-desc')
			{
				$data['url_sort'] = U('System/Personnel/position').'?order=sort-asc';
			}else{
				$data['url_sort'] = U('System/Personnel/position').'?order=sort-desc';
			}
			if($request['order']=='id-desc')
			{
				$data['url_id'] = U('System/Personnel/position').'?order=id-asc';
			}else{
				$data['url_id'] = U('System/Personnel/position').'?order=id-desc';
			}
			
            //获取数据
            $roleMain = new RoleMain();
            $data['roleAll'] = $roleMain->getAllRole(null, $order,$re_page.',30');
            //获取节点列表
            $nodeMain = new NodeMain();
            $data['nodeHtml'] = $nodeMain->getAllNodeHtml();
            //加载分页类
            $data['paging'] = $this->Paging($re_page,30,$data['roleAll']['count'],$request);
            $this->assign('data', $data);
            $this->display();
        }
    }
    /**
     * 职位管理-添加
     * @author zgt
     */
    public function addPosition()
    {
        if(IS_POST) {
            //获取参数 验证
            $request = I('post.');
            if(empty($request['positionname'])) $this->ajaxReturn(1, '职位名称不能为空', '', 'positionname');
            if(empty($request['remark'])) $this->ajaxReturn(1, '请添加该职位描述', '', 'remark');
            if(empty($request['department_id'])) $this->ajaxReturn(1, '请选择所属部门');
            if(empty($request['access'])) $this->ajaxReturn(1, '请先设置权限');
            //权限内容处理
            $access = explode(',', $request['access']);
            if(!empty($access)){
                $access_new = array();
                foreach($access as $v){
                    $v = explode('-', $v);
                    $access_new[] = array('node_id'=>$v[0], 'pid'=>$v[1], 'level'=>$v[2]);
                }
                unset($request['access']);
            }
            $request['name'] = $request['positionname'];
            $roleMain = new RoleMain();
            $result = $roleMain->create($request,$access_new);
            //添加部门成功
            if(isset($result)) $this->success('添加部门成功', 0, U('System/Personnel/position'));
            else $this->ajaxReturn(1, '数据添加失败');
        }else{
            //职位列表
            $roleMain = new RoleMain();
            $data['roleAll'] = $roleMain->getAllRole();
            //获取部门管理列表
            $departmentMain = new DepartmentMain();
            $data['departmentAll'] = $departmentMain->getList();
            //获取节点列表
            $nodeMain = new NodeMain();
            $data['nodeHtml'] = $nodeMain->getAllNodeHtml();
            //返回地址
            $data['url_position'] = U('System/Personnel/position');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 职位管理-修改
     * @author zgt
     */
    public function editPosition()
    {
        $role_id = I('get.role_id',null);
        if( empty($role_id) )$this->error('非法请求！');
        if(IS_POST) {
            $request = I('post.');
            if(empty($request['positionname'])) $this->ajaxReturn(1, '职位名称不能为空', '', 'positionname');
            if(empty($request['remark'])) $this->ajaxReturn(1, '请添加该职位描述', '', 'remark');
            if(empty($request['department_id'])) $this->ajaxReturn(1, '请选择所属部门');
            if(empty($request['access'])) $this->ajaxReturn(1, '请先设置权限');
            //权限内容处理
            $access = explode(',', $request['access']);
            if(!empty($access)){
                $access_new = array();
                foreach($access as $v){
                    $v = explode('-', $v);
                    $access_new[] = array('node_id'=>$v[0], 'pid'=>$v[1], 'level'=>$v[2]);
                }
                unset($request['access']);
            }
            $request['name'] = $request['positionname'];
            $roleMain = new RoleMain();
            $result = $roleMain->edit($request,$access_new,$role_id);
            //添加部门成功
            if(!empty($result)) $this->success('修改职位成功', 0, U('System/Personnel/position'));
            else $this->ajaxReturn(1, '数据修改失败');
        }else{
            //职位列表
            $roleMain = new RoleMain();
            $data['roleAll'] = $roleMain->getAllRole();
            //获取部门管理列表
            $departmentMain = new DepartmentMain();
            $data['departmentAll'] = $departmentMain->getList();
            //获取节点列表
            $nodeMain = new NodeMain();
            $data['nodeHtml'] = $nodeMain->getAllNodeHtml();
            //获取职位详情
            $roleMain = new RoleMain();
            $data['roleInfo'] = $roleMain->getInfo($role_id);
            $roleAccess = $roleMain->getRoleAccess($role_id);
            $data['roleAccess'] = '';
            foreach($roleAccess as $k=>$v){
                if($k==0){
                    $data['roleAccess'].=$v['node_id'];
                }else{
                    $data['roleAccess'].=','.$v['node_id'];
                }
            }
            //返回地址
            $data['url_position'] = U('System/Personnel/position');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 职位管理-操作页(删除/修改排序/权限)
     * @author zgt
     */
    public function dispostPosition()
    {
        if(IS_POST) {
            //实例化
            $RoleModel = D('Role');
            //获取参数 验证
            $role_id = I('post.role_id',null);
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                if(empty($role_id)) $this->ajaxReturn(1, '职位ID不能为空', '', 'role_id');
                $roleMain = new RoleMain();
                $result = $roleMain->edit(array('status'=>0),'',$role_id);
                //删除部门
                if(!empty($result)) $this->success('删除职位成功', 0, U('System/Personnel/department'));
                else $this->ajaxReturn(1, '数据删除失败');
            }else if( isset($type) && $type=='sort' ){
				$sort_data = I('post.sort_data','');
                if(empty($sort_data)) $this->ajaxReturn(1, '请输入要修改的排序值');
				if($sort_data!='') {
					$sort_data=explode(',',$sort_data);
					$new_sort_data=array();
					foreach($sort_data  as $k=>$v)
					{
						$tmp=explode('-',$v);
						$new_sort_data[$tmp[0]]=$tmp[1];
					}
					$result = D('Role')->batch_update($new_sort_data,'id','sort');
					//排序修改
					if($result!==false) {
                        F('Cache/Personnel/role', null);
                        $this->ajaxReturn(0, '排序修改成功', U('System/Personnel/position'));
                    }else{
                        $this->ajaxReturn(1, '排序修改失败');
                    }
				}
            }else if( isset($type) && $type=='access' ){
                $access = I('post.access',null);
                if(empty($role_id)) $this->ajaxReturn(1, '职位ID不能为空', '', 'role_id');
                //权限内容处理
                $access = explode(',', $access);
                $access_new = array();
                foreach($access as $v){
                    $v = explode('-', $v);
                    $access_new[] = array('node_id'=>$v[0], 'pid'=>$v[1], 'level'=>$v[2]);
                }
                $roleMain = new RoleMain();
                $result = $roleMain->edit(null,$access_new,$role_id);
                //权限修改
                if(!empty($result)) $this->success('权限修改成功', 0, U('System/Personnel/department'));
                else $this->ajaxReturn(1, '权限修改失败');
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 员工列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function systemUserList()
    {
        //获取参数 页码
        $requestG = I('get.');
        $where['usertype'] = !empty($requestG['usertype'])?$requestG['usertype']:array('neq',10);
        $where['status'] = 1;
        $re_page = isset($requestG['page'])?$requestG['page']:1;
        //查询条件where处理
        if(!empty($requestG['key_value']) && !empty($requestG['key_name'])) {
            if($requestG['key_name']=='username'){
                $where['username'] = encryptPhone(trim($requestG['key_value']), C('PHONE_CODE_KEY'));
            }else{
                $where[$requestG['key_name']] = array('like', "%".$requestG['key_value']."%");
            }
        }
        if(!empty($requestG['role_id'])) $where['role_id'] = $requestG['role_id'];
        $where['zone_id'] = !empty($requestG['zone_id'])?$requestG['zone_id']:$this->system_user['zone_id'];
        if(IS_POST){
            $requestP = I('post.');
            if($requestP['type']='getCount'){
                //异步获取分页数据
                $systemMain = new SystemMain();
                $result = $systemMain->getCount($where);
                //加载分页类
                $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $result['data'], $requestG);
                $this->ajaxReturn(0, '', $paging_data);
            }
        }
        //员工列表
        $systemMain = new SystemMain();
        $data['systemUserAll'] = $systemMain->getList($where,null,(($re_page-1)*30).',30');
        //获取职位及部门
        $departmenMain = new DepartmentMain();
        $data['departmentAll'] = $departmenMain->getList();
        $roleMain = new RoleMain();
        $data['roleAll'] = $roleMain->getAllRole();
        //获取区域ID 获取下拉框
        $zoneMain = new ZoneMain();
        $data['zoneAll'] = $zoneMain->getZoneList($this->system_user['zone_id']);
        //员工状态
        $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v['text']=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        $data['requery'] = $requestG;
        $data['url_addSystemUser'] = U('System/Personnel/addSystemUser');
        $data['url_editSystemUser'] = U('System/Personnel/editSystemUser');
        $data['url_dispostSystemUser'] = U('System/Personnel/dispostSystemUser');
        $data['url_systemUserInfo'] = U('System/Personnel/systemUserInfo');
        $data['url_editSystemUserInfo'] = U('System/Personnel/editSystemUserInfo');
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 离职员工列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function leaveSystemUserList()
    {
        //获取参数 页码
        $requestG = I('get.');
        $where['usertype'] = 10;
        $where['status'] = 1;
        $re_page = isset($requestG['page'])?$requestG['page']:1;
        //查询条件where处理
        if(!empty($requestG['key_value']) && !empty($requestG['key_name'])) {
            if($requestG['key_name']=='username'){
                $where['username'] = encryptPhone(trim($requestG['key_value']), C('PHONE_CODE_KEY'));
            }else{
                $where[$requestG['key_name']] = array('like', "%".$requestG['key_value']."%");
            }
        }
        if(!empty($requestG['role_id'])) $where['role_id'] = $requestG['role_id'];
        $where['zone_id'] = !empty($requestG['zone_id'])?$requestG['zone_id']:$this->system_user['zone_id'];
        if(IS_POST){
            $requestP = I('post.');
            if($requestP['type']='getCount'){
                //异步获取分页数据
                $systemMain = new SystemMain();
                $result = $systemMain->getCount($where);
                //加载分页类
                $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $result['data'], $requestG);
                $this->ajaxReturn(0, '', $paging_data);
            }
        }
        //员工列表
        $systemMain = new SystemMain();
        $data['systemUserAll'] = $systemMain->getList($where,null,(($re_page-1)*30).',30');
        //获取职位及部门
        $departmenMain = new DepartmentMain();
        $data['departmentAll'] = $departmenMain->getList();
        $roleMain = new RoleMain();
        $data['roleAll'] = $roleMain->getAllRole();
        //获取区域ID 获取下拉框
        $zoneMain = new ZoneMain();
        $data['zoneAll'] = $zoneMain->getZoneList($this->system_user['zone_id']);
        //员工状态
        $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v['text']=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        $data['requery'] = $requestG;
        $data['url_addSystemUser'] = U('System/Personnel/addSystemUser');
        $data['url_editSystemUser'] = U('System/Personnel/editSystemUser');
        $data['url_dispostSystemUser'] = U('System/Personnel/dispostSystemUser');
        $data['url_systemUserInfo'] = U('System/Personnel/systemUserInfo');
        $this->assign('data', $data);
        $this->display();
		
    }
    /**
     * 员工-离线
     * @author zgt
     */
    public function SystemUserOffline()
    {
        $request = I('post.');
        $system_user_id = !empty($request['system_user_id'])?$request['system_user_id']:null;
        $systemMain = new SystemMain();
        $flag = $systemMain->removeToken($system_user_id);
        if($flag['code']==0) $this->ajaxReturn(0, $flag['msg']);
        else $this->ajaxReturn($flag['code'], $flag['msg']);
    }
    /**
     * 员工-添加
     * @author zgt
     */
    public function addSystemUser()
    {
        if(IS_POST) {
            //实例化
            $checkform = new \Org\Form\Checkform();
            //获取参数 验证
            $request = I('post.');
            if(empty($request['realname'])) $this->ajaxReturn(1, '真实姓名不能为空', '', 'realname');
            if(empty($request['username'])) $this->ajaxReturn(1, '手机号码不能为空', '', 'username');
            if(!$checkform->checkMobile($request['username'])) $this->ajaxReturn(1, '手机号码格式有误', '', 'username');
            if(!$checkform->isCompanyEmail($request['email'])) $this->ajaxReturn(1, '邮箱地址输入有误', '', 'email');
            if(empty($request['zone_id'])) $this->ajaxReturn(1, '请选择所属区域');
            if(empty($request['role_id'])) $this->ajaxReturn(1, '请选择所属部门及职位' );
            if(empty($request['usertype'])) $this->ajaxReturn(1, '请选择员工状态' );
            if(empty($request['check_id'])) $this->ajaxReturn(1, '指纹编号不能为空' );
            if(empty($request['entrytime'])) $this->ajaxReturn(1, '入职时间不能为空' );
            if(empty($request['straightime'])) $this->ajaxReturn(1, '转正时间不能为空' );
            $request['entrytime'] = strtotime($request['entrytime']);
            $request['straightime'] = strtotime($request['straightime']);
            //获取 数据判断
            $systemMain = new SystemMain();
            $addSystemUser = $systemMain->create($request);
            if($addSystemUser['code']==0) $this->ajaxReturn(0, $addSystemUser['msg'], U('System/Personnel/systemUserList'));
            else $this->ajaxReturn($addSystemUser['code'], $addSystemUser['msg']);
        }else{
            //获取职位及部门
            $departmenMain = new DepartmentMain();
            $data['departmentAll'] = $departmenMain->getList();
            $roleMain = new RoleMain();
            $data['roleAll'] = $roleMain->getAllRole();
            //获取区域ID 获取下拉框
            $zoneMain = new ZoneMain();
            $data['zoneAll'] = $zoneMain->getZoneList($this->system_user['zone_id']);
            //员工状态
            $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
            foreach($data['systemUserStatus'] as $k=>$v){
                if($v['text']=='离职'){
                    unset($data['systemUserStatus'][$k]);
                }
            }
            $data['url_systemUser'] = U('System/Personnel/systemUserList');
            $data['url_getZoneSelect'] = U('System/Personnel/getZoneSelect');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 员工-修改
     * @author zgt
     */
    public function editSystemUser()
    {
        $system_user_id = I('get.user_id',null);
        if( empty($system_user_id) )$this->error('非法请求！');
        if(IS_POST) {
            //获取参数 验证
            $request = I('post.');
            if(!empty($request['type']) && $request['type']=='editzone'){
                $systemMain = new SystemMain();
                $where['system_user_id'] = $system_user_id;
                $editUserZone = $systemMain->editUserZone($where);
                if($editUserZone['code']==0) $this->ajaxReturn(0, '该员工的客户已修改成功', U('System/Personnel/systemUserList'));
                else $this->ajaxReturn(1, '数据操作失败');
            }else{
                $checkform = new \Org\Form\Checkform();
                if(empty($request['realname'])) $this->ajaxReturn(1, '真实姓名不能为空', '', 'realname');
                if(empty($request['username'])) $this->ajaxReturn(1, '手机号码不能为空', '', 'username');
                if(!$checkform->checkMobile($request['username'])) $this->ajaxReturn(1, '手机号码格式有误', '', 'username');
                if(!$checkform->isCompanyEmail($request['email'])) $this->ajaxReturn(1, '邮箱地址输入有误', '', 'email');
                if(empty($request['zone_id'])) $this->ajaxReturn(1, '请选择所属区域');
                if(empty($request['role_id'])) $this->ajaxReturn(1, '请选择所属部门及职位' );
                if(empty($request['usertype'])) $this->ajaxReturn(1, '请选择员工状态' );
                if(empty($request['check_id'])) $this->ajaxReturn(1, '指纹编号不能为空' );
                if(empty($request['entrytime'])) $this->ajaxReturn(1, '入职时间不能为空' );
                if(empty($request['straightime'])) $this->ajaxReturn(1, '转正时间不能为空' );
                $request['entrytime'] = strtotime($request['entrytime']);
                $request['straightime'] = strtotime($request['straightime']);
                //获取 数据判断
                $request['system_user_id'] = $system_user_id;
                $systemMain = new SystemMain();
                $editSystemUser = $systemMain->edit($request);
                if($editSystemUser['code']==0){
                    $getUserCount = $systemMain->getUserCount($request);
                    if($getUserCount['code']==0){
                        $this->ajaxReturn(2001, '该员工下面有客户，是否需要带走客户');
                    }
                    $this->ajaxReturn(0, '员工账号修改成功', U('System/Personnel/systemUserList'));
                }
                $this->ajaxReturn($editSystemUser['code'], $editSystemUser['msg']);
            }
        }else{
            //获取员工信息
            $systemMain = new SystemMain();
            $systemUserInfo = $systemMain->getInfo($system_user_id);
            if(!empty($systemUserInfo['data']['user_roles'])){
                foreach($systemUserInfo['data']['user_roles'] as $k=>$v){
                    $data['is_roles'][] = $v['role_id'];
                    if($k==0) $data['roles'] .= $v['role_id'];
                    else $data['roles'] .= ','.$v['role_id'];
                }
            }
            $data['SystemUserInfo'] = $systemUserInfo['data'];
            //获取职位及部门
            $departmenMain = new DepartmentMain();
            $data['departmentAll'] = $departmenMain->getList();
            $roleMain = new RoleMain();
            $data['roleAll'] = $roleMain->getAllRole();
            //获取区域ID 获取下拉框
            $zoneMain = new ZoneMain();
            $data['zoneAll'] = $zoneMain->getZoneList($this->system_user['zone_id']);
            //员工状态
            $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
            $data['url_systemUser'] = U('System/Personnel/systemUserList');
            $data['system_user_id'] = $system_user_id;
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 员工-操作方法（删除/离职）
     * @author zgt
     */
    public function dispostSystemUser(){
        $system_user_id = I('post.system_user_id');
        $type = I('post.type');
        if($type=='del'){
            $data['flag'] = 'del';
            $data['system_user_id'] = $system_user_id;
            $systemMain = new SystemMain();
            $flag = $systemMain->del($data);
            if($flag['code']==0) $this->ajaxReturn(0, '账号删除成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }else if($type=='dels'){
            $data['flag'] = 'del';
            $data['system_user_id'] = $users = I('post.users');
            if(empty($users)) $this->ajaxReturn(1, '请先选中所需删除项');
            $systemMain = new SystemMain();
            $flag = $systemMain->del($data);
            if($flag['code']==0) $this->ajaxReturn(0, '账号删除成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }else if($type=='usertype'){
            $data['flag'] = 'usertype';
            $data['system_user_id'] = $system_user_id;
            $systemMain = new SystemMain();
            $flag = $systemMain->del($data);
            if($flag['code']==0) $this->ajaxReturn(0, '离职设置成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }
//        else if($type=='call'){
//            $system_user = $this->system_user;
//            $myPhone = decryptPhone($system_user['username'],C(PHONE_CODE_KEY));
//            $phone = decryptPhone(I('post.userKey'),C(PHONE_CODE_KEY));
//            if($myPhone==$phone) $this->ajaxReturn(1, '无法与自己通话');
//            $reflag = $this->call($myPhone,$phone,$myPhone);
//            if(!empty($reflag) && $reflag->code==0) $this->ajaxReturn(0, '网络电话已开始拨打，请确保您手机信号通畅，注意：最大通话时长5分钟！');
//            else $this->ajaxReturn(1, '网络电话拨打失败');
//        }
    }


    /**
     * 员工档案-修改
     * @author zgt
     */
    public function editSystemUserInfo(){
        //获取被查看用户ID
        $system_user_id = I('get.user_id',null);
        if(IS_POST) {
            //获取参数 验证
            $request = I('post.');
            $request['system_user_id'] = $system_user_id;
            if(empty($system_user_id)) $this->ajaxReturn(1, '非法操作');
            if(empty($request['birthday'])) $this->ajaxReturn(1, '生日不能为空', '', 'birthday');
            if(empty($request['nativeplace'])) $this->ajaxReturn(1, '籍贯不能为空', '', 'nativeplace');
            if(empty($request['education_id'])) $this->ajaxReturn(1, '学历不能为空');
            if(empty($request['school'])) $this->ajaxReturn(1, '毕业学校不能为空', '', 'school');
            if(empty($request['plivatemail'])) $this->ajaxReturn(1, '个人邮箱不能为空', '', 'plivatemail');
            if(empty($request['usertype'])) $this->ajaxReturn(1, '用户状态不能为空');
            if(empty($request['entrytime'])) $this->ajaxReturn(1, '开始时间', '', 'entrytime');
            if(empty($request['straightime'])) $this->ajaxReturn(1, '结束时间', '', 'straightime');
            if(empty($request['check_id'])) $this->ajaxReturn(1, '指纹编号不能为空', '', 'check_id');
            $request['birthday'] = strtotime($request['birthday']);
            $request['entrytime'] = strtotime($request['entrytime']);
            $request['straightime'] = strtotime($request['straightime']);
            if ($request['entrytime'] > $request['straightime']) {
                $this->ajaxReturn(2, '转正时间不能早于入职时间', '', '');
            }
            //数据操作
            $systemMain = new SystemMain();
            $flag = $systemMain->editInfo($request);
            if($flag['code']==0) $this->ajaxReturn(0, '员工档案修改成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }else{
            //获取员工信息
            $systemMain = new SystemMain();
            $systemUserInfo = $systemMain->getInfo($system_user_id);
            if(!empty($systemUserInfo['data']['user_roles'])){
                foreach($systemUserInfo['data']['user_roles'] as $k=>$v){
                    $data['is_roles'][] = $v['role_id'];
                    if($k==0) $data['roles'] .= $v['role_id'];
                    else $data['roles'] .= ','.$v['role_id'];
                }
            }
            $data['systemUserInfo'] = $systemUserInfo['data'];
            //学历
            $educationMain = new EducationController();
            if ($data['systemUserInfo']['education_id']) {
                $edu = $educationMain->getInfo($data['systemUserInfo']['education_id']);
                $data['systemUserInfo']['educationname'] = $edu['data'];
            }
            //员工状态
            $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
            foreach($data['systemUserStatus'] as $k=>$v){
                if($v['text']=='离职'){
                    unset($data['systemUserStatus'][$k]);
                }
            }
            //获取学历表
            $res = $educationMain->getlist();
            $data['educationAll'] = $res['data'];
            $data['url_systemUser'] = U('System/Personnel/systemUserList');
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 员工档案-查看
     * @author zgt
     */
    public function systemUserInfo(){
        //获取被查看用户ID
        $system_user_id = I('get.user_id',null);
        $systemMain = new SystemMain();
        $systemUserInfo = $systemMain->getInfo($system_user_id);
        if(!empty($systemUserInfo['data']['user_roles'])){
            foreach($systemUserInfo['data']['user_roles'] as $k=>$v){
                $data['is_roles'][] = $v['role_id'];
                if($k==0) $data['roles'] .= $v['role_id'];
                else $data['roles'] .= ','.$v['role_id'];
            }
        }
        $data['systemUserInfo'] = $systemUserInfo['data'];
        //获取学历表
        $educationMain = new EducationController();
        $educationList = $educationMain->getList();
        $data['educationAll'] = $educationList['data'];
        //员工状态
        $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v['text']=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        $this->assign('data', $data);
        $this->display();
    }

    /*
     * 获取区域列表 分级获取-- AJAX动态下拉框(三级联动)
     * @author zgt
     */
    public function getZoneSelect(){
        $zone_id = I('post.zone_id');
        $zoneMain = new ZoneMain();
        $zoneList = $zoneMain->getZoneList($zone_id);
        $zoneHtml =
        "<dt>
            <div class='select_title l'>所有</div>
            <div class='arrow r'></div>
        </dt>
        <dd class='fxDone' data-level='{$zoneList['level']}' data-value='{$zoneList['zone_id']}'>所有</dd>";

        foreach($zoneList['children'] as $k=>$v){
            $zoneHtml.=
                "<dd class='fxDone' data-level='".$v['level']."' data-value='".$v['zone_id']."'>".$v['name']."</dd>";
        }
        $this->ajaxReturn(0, '数据获取成功', $zoneHtml);
    }

    /**
     * 员工获取 该权限下的地区 中心下拉选择框HTML
     * @author zgt
     */
    public function getZoneList(){
        $zone_id = I('post.zone_id');
        $zoneMain = new ZoneMain();
        $zoneList = $zoneMain->getZoneList($zone_id);
        $this->ajaxReturn(0, '数据获取成功', $zoneList);
    }
}
