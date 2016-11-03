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
use Common\Controller\SystemController;

class PersonnelController extends SystemController {

    //控制器前置
    public function _initialize(){
        parent::_initialize();
    }

    /*
    |--------------------------------------------------------------------------
    | 部门管理---
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function department()
    {
        $this->display();
    }
    /**
     * 部门管理-添加
     * @author zgt
     */
    public function addDepartment()
    {
        $this->display();
    }
    /**
     * 部门管理-修改
     * @author zgt
     */
    public function editDepartment()
    {
        $department_id = I('get.dep_id',null);
        if( empty($department_id) )$this->error('非法请求！');
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 职位管理---
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function position()
    {
        //post=》列表页快捷修改权限
        if(IS_POST){
            $role_id = I('post.role_id');
            //获取职位详情
            $result = D('Role','Service')->getRoleNode(array('role_id'=>$role_id,'type'=>1));
            if(!empty($result['data'])){
                $roleAccess = '';
                foreach($result['data'] as $k=>$v){
                    if($k==0){
                        $roleAccess.=$v['node_id'];
                    }else{
                        $roleAccess.=','.$v['node_id'];
                    }
                }
            }
            $this->ajaxReturn(0, '获取职位权成功', $roleAccess);
        }
        //获取参数
        $request = I('get.');
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
        $where['order'] = $order;
        $where['page'] = $re_page.',30';
        $roleAll = D('Role','Service')->getRoleList($where);
        //加载分页类
        $data['paging'] = $this->Paging($re_page,30,$roleAll['data']['count'],$request);
        $data['roleAll'] = $roleAll['data'];
        //获取节点列表
        $data['nodeHtml'] = $this->_getAllNodeHtml();
        //加载分页类
        $data['paging'] = $this->Paging($re_page,30,$data['roleAll']['count'],$request);
        $this->assign('data', $data);
        $this->display();

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
            $request['access'] = $access_new;
            $result = D('Role','Service')->addRole($request);
            //添加职位成功
            if($result['code']==0){
                $this->ajaxReturn(0,'添加职位成功',U('System/Personnel/position'));
            }
            $this->ajaxReturn($result['code'],$result['msg']);
        }else{
            //职位列表
            $roleAll = D('Role','Service')->getRoleList();
            $data['roleAll'] = $roleAll['data'];
            //获取部门管理列表
            $departmentAll = D('Department','Service')->getDepartmentList();
            $data['departmentAll'] = $departmentAll['data'];
            //获取节点列表
            $data['nodeHtml'] = $this->_getAllNodeHtml();
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
            $request['access'] = $access_new;
            $request['role_id'] = $role_id;
            $result = D('Role','Service')->editRole($request);
            //添加职位成功
            if($result['code']==0){
                $this->ajaxReturn(0,'修改职位成功',U('System/Personnel/position'));
            }
            $this->ajaxReturn($result['code'],$result['msg']);
        }
        //职位列表
        $roleAll = D('Role','Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取部门管理列表
        $departmentAll = D('Department','Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        //获取节点列表
        $data['nodeHtml'] = $this->_getAllNodeHtml();
        //获取职位详情
        $role_info = D('Role','Service')->getRoleInfo(array('role_id'=>$role_id));
        $data['roleInfo'] = $role_info['data'];
        $roleAccess = D('Role','Service')->getRoleNode(array('role_id'=>$role_id));
        $data['roleAccess'] = '';
        foreach($roleAccess['data'] as $k=>$v){
            if($k==0){
                $data['roleAccess'].=$v['node_id'];
            }else{
                $data['roleAccess'].=','.$v['node_id'];
            }
        }
        //返回地址
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 职位管理-操作页(删除/修改排序/权限)
     * @author zgt
     */
    public function dispostPosition()
    {
        if(IS_POST) {
            //获取参数 验证
            $role_id = I('post.role_id',null);
            $type = I('post.type',null);
            if( isset($type) && $type=='del' ){
                $requery_data['role_id'] = $role_id;
                $result = D('Role','Service')->delRole($requery_data);
                //删除部门
                if($result['code']==0) $this->ajaxReturn(0, '删除职位成功', U('System/Personnel/position'));
                else $this->ajaxReturn($result['code'], '数据删除失败');
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
                        F('Cache/role', null);
                        $this->ajaxReturn(0, '排序修改成功', U('System/Personnel/position'));
                    }else{
                        $this->ajaxReturn(1, '排序修改失败');
                    }
				}
            }else if( isset($type) && $type=='access' ){
                $access = I('post.access',null);
                if(empty($role_id)) $this->ajaxReturn(1, '职位ID不能为空', '', 'role_id');
                if(!empty($access)){
                    //权限内容处理
                    $access = explode(',', $access);
                    $access_new = array();
                    foreach($access as $v){
                        $v = explode('-', $v);
                        $access_new[] = array('node_id'=>$v[0], 'pid'=>$v[1], 'level'=>$v[2]);
                    }
                    $request['access'] = $access_new;
                }
                $request['role_id'] = $role_id;
                $result = D('Role','Service')->editRole($request);
                //权限修改
                if($result['code']==0){
                    $this->ajaxReturn(0,'权限修改成功',U('System/Personnel/position'));
                }
                $this->ajaxReturn($result['code'],$result['msg']);
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
                $where['username'] = (trim($requestG['key_value']));
            }else{
                $where[$requestG['key_name']] = array('like', $requestG['key_value']);
            }
        }
        if(!empty($requestG['role_id'])) $where['role_ids'] = $requestG['role_id'];
        $where['zone_id'] = !empty($requestG['zone_id'])?$requestG['zone_id']:$this->system_user['zone_id'];
        //员工列表
        $_param = $where;
        $_param['page'] = $re_page.',30';
        $_param['order'] = 'system_user_id desc';
        $systemUserAll = D('SystemUser','Service')->getSystemUsersList($_param);
        $data['systemUserAll'] = $systemUserAll['data'];
        //加载分页类
        $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $data['systemUserAll']['count'], $requestG);
        $data['paging'] = $paging_data;
        //获取职位及部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取区域ID 获取下拉框
        $zoneAll = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneAll['data'];
        //员工状态
        $data['systemUserStatus'] = C('FIELD_STATUS.SYSTEMUSERSTATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        $data['requery'] = $requestG;
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
                $where['username'] = (trim($requestG['key_value']));
            }else{
                $where[$requestG['key_name']] = array('like', $requestG['key_value']);
            }
        }
        if(!empty($requestG['role_id'])) $where['role_ids'] = $requestG['role_id'];
        $where['zone_id'] = !empty($requestG['zone_id'])?$requestG['zone_id']:$this->system_user['zone_id'];
        //员工列表
        $_param = $where;
        $_param['page'] = $re_page.',30';
        $_param['order'] = 'system_user_id desc';
        $systemUserAll = D('SystemUser','Service')->getSystemUsersList($_param);
        $data['systemUserAll'] = $systemUserAll['data'];
        //加载分页类
        $paging_data = $this->Paging((empty($requestG['page'])?1:$requestG['page']), 30, $data['systemUserAll']['count'], $requestG);
        $data['paging'] = $paging_data;
        //获取职位及部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        $data['requery'] = $requestG;
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
        $param['system_user_id'] = !empty($request['system_user_id'])?$request['system_user_id']:null;
        $flag = D('SystemUser','Service')->removeToken($param);
        if($flag['code']==0) $this->ajaxReturn(0, $flag['msg']);
        else $this->ajaxReturn($flag['code'], $flag['msg']);
    }

    /*
    |--------------------------------------------------------------------------
    | 员工-添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addSystemUser()
    {
        if(IS_POST) {
            //获取参数 验证
            $request = I('post.');
            //获取 数据判断
            $addSystemUser = D('SystemUser','Service')->addSystemUser($request);
            if($addSystemUser['code']==0) $this->ajaxReturn(0, $addSystemUser['msg'], U('System/Personnel/systemUserList'));
            else $this->ajaxReturn($addSystemUser['code'], $addSystemUser['msg']);
        }
        //获取职位及部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取区域ID 获取下拉框
        $zoneAll = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneAll['data'];
        //员工状态
        $data['systemUserStatus'] = C('FIELD_STATUS.SYSTEMUSERSTATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        $data['url_systemUser'] = U('System/Personnel/systemUserList');
        $data['url_getZoneSelect'] = U('System/Personnel/getZoneSelect');
        $this->assign('data', $data);
        $this->display();
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
                $where['system_user_id'] = $system_user_id;
                $editUserZone = D('SystemUser','Service')->editUserZone($where);
                if($editUserZone['code']==0) $this->ajaxReturn(0, '已修改成功', U('System/Personnel/systemUserList'));
                else $this->ajaxReturn($editUserZone['code'], $editUserZone['msg']);
            }else{
                //获取 数据判断
                $request['system_user_id'] = $system_user_id;
                $editSystemUser = D('SystemUser','Service')->editSystemUser($request);
                if($editSystemUser['code']==0){
                    $where_user['system_user_id'] = $system_user_id;
                    $where_user['status'] = array('IN','20,30,60');
                    $getUserCount = D('User')->getCount($where_user);
                    if($getUserCount>0){
                        $this->ajaxReturn(2001, '该员工下面有客户，是否需要带走客户');
                    }
                    $this->ajaxReturn(0, '员工账号修改成功', U('System/Personnel/systemUserList'));
                }
                $this->ajaxReturn($editSystemUser['code'], $editSystemUser['msg']);
            }
        }
        //获取员工信息
        $systemUserInfo = D('SystemUser','Service')->getSystemUserInfo(array('system_user_id'=>$system_user_id));
        $data['SystemUserInfo'] = $systemUserInfo['data'];
        //获取职位及部门
        $departmentAll = D('Department', 'Service')->getDepartmentList();
        $data['departmentAll'] = $departmentAll['data'];
        $roleAll = D('Role', 'Service')->getRoleList();
        $data['roleAll'] = $roleAll['data'];
        //获取区域ID 获取下拉框
        $zoneAll = D('Zone', 'Service')->getZoneList(array('zone_id'=>$this->system_user['zone_id']));
        $data['zoneAll'] = $zoneAll['data'];
        //员工状态
        $data['systemUserStatus'] = C('FIELD_STATUS.SYSTEMUSERSTATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        if(!empty($systemUserInfo['data']['user_roles'])){
            foreach($systemUserInfo['data']['user_roles'] as $k=>$v){
                $data['is_roles'][] = $v['id'];
                if($k==0) $data['roles'] .= $v['id'];
                else $data['roles'] .= ','.$v['id'];
            }
        }
        $data['url_systemUser'] = U('System/Personnel/systemUserList');
        $data['system_user_id'] = $system_user_id;
        $this->assign('data', $data);
        $this->display();
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
            $flag = D('SystemUser','Service')->delSystemUser($data);
            if($flag['code']==0) $this->ajaxReturn(0, '账号删除成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }else if($type=='dels'){
            $data['flag'] = 'del';
            $data['system_user_id'] = I('post.users');
            if(empty($data['system_user_id'])) $this->ajaxReturn(1, '请先选中所需删除项');
            $flag = D('SystemUser','Service')->delSystemUser($data);
            if($flag['code']==0) $this->ajaxReturn(0, '账号删除成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }else if($type=='usertype'){
            $data['flag'] = 'usertype';
            $data['system_user_id'] = $system_user_id;
            $flag = D('SystemUser','Service')->delSystemUser($data);
            if($flag['code']==0) $this->ajaxReturn(0, '离职设置成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn(1, '数据操作失败');
        }
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
            //数据操作
            $flag = D('SystemUser','Service')->editSystemUserInfo($request);
            if($flag['code']==0) $this->ajaxReturn(0, '员工档案修改成功', U('System/Personnel/systemUserList'));
            else $this->ajaxReturn($flag['code'], $flag['msg']);
        }
        //获取员工信息
        $systemUserInfo = D('SystemUser','Service')->getSystemUserInfo(array('system_user_id'=>$system_user_id));
        $data['systemUserInfo'] = $systemUserInfo['data'];
        //员工状态
        $data['systemUserStatus'] = C('SYSTEM_USER_STATUS');
        foreach($data['systemUserStatus'] as $k=>$v){
            if($v['text']=='离职'){
                unset($data['systemUserStatus'][$k]);
            }
        }
        //获取学历表
        $data['educationAll'] = C('FIELD_STATUS.EDUCATION_ARRAY');
        $data['url_systemUser'] = U('System/Personnel/systemUserList');
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 员工档案-查看
     * @author zgt
     */
    public function systemUserInfo(){
        //获取被查看用户ID
        $system_user_id = I('get.user_id',null);
        //获取员工信息
        $systemUserInfo = D('SystemUser','Service')->getSystemUserInfo(array('system_user_id'=>$system_user_id));
        $data['systemUserInfo'] = $systemUserInfo['data'];
        //获取学历表
        $data['educationAll'] = C('FIELD_STATUS.EDUCATION_ARRAY');
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
        //获取区域ID
        $zoneAll = D('Zone', 'Service')->getZoneList(array('zone_id'=>$zone_id));
        $zoneList = $zoneAll['data'];
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
        $zoneList = D('Zone','Service')->getZoneList(array('zone_id'=>$zone_id));
        $this->ajaxReturn(0, '数据获取成功', $zoneList['data']);
    }
    /**
     * 生成节点列表HTML
     * @author
     */
    protected function _getAllNodeHtml()
    {
        $nodeAll = D('Node','Service')->getNodeList();
        $nodeAll_html = '';
        foreach ($nodeAll['data']['data'] as $k => $v) {
            $nodeAll_html .=
                "<tr id='node-{$v['id']}' class=' collapsed '>
                    <td style='padding-left: 30px;'>
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='node_id[]' value='{$v['id']}' pid='0' level='0' class='radio radio-node-{$v['id']}'  onclick='javascript:checknode(this);' autocomplete='off'> {$v['title']} ({$v['name']})</td>
                </tr>";
            if (!empty($v['children'])) {
                foreach ($v['children'] as $k2 => $v2) {
                    $nodeAll_html .=
                        "<tr id='node-{$v2['id']}' class='tr lt child-of-node-{$v2['pid']}  collapsed ui-helper-hidden'>
                            <td style='padding-left: 49px;'>
                                &nbsp;&nbsp;&nbsp;&nbsp;├─
                                <input type='checkbox' name='node_id[]' value='{$v2['id']}' class='radio radio-node-{$v2['id']}' pid='{$v2['pid']}' level='1'  onclick='javascript:checknode(this);' autocomplete='off'> {$v2['title']} ({$v2['name']})</td>
                        </tr>";
                    if (!empty($v2['children'])) {
                        foreach ($v2['children'] as $k3 => $v3) {
                            $nodeAll_html .=
                                "<tr id='node-{$v3['id']}' class='tr lt child-of-node-{$v3['pid']} ui-helper-hidden'>
                                    <td style='padding-left: 68px;'>&nbsp;&nbsp;&nbsp;&nbsp;│ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─
                                        <input type='checkbox' name='node_id[]' value='{$v3['id']}' class='radio radio-node-{$v3['id']}' pid='{$v3['pid']}' level='2'  onclick='javascript:checknode(this);' autocomplete='off'> {$v3['title']} ({$v3['name']})</td>
                                </tr>";
                            if (!empty($v3['children'])) {
                                foreach ($v3['children'] as $k4 => $v4) {
                                    $nodeAll_html .=
                                        "<tr id='node-{$v4['id']}' class='tr lt child-of-node-{$v4['pid']} ui-helper-hidden'>
                                            <td style='padding-left: 68px;'>&nbsp;&nbsp;&nbsp;&nbsp;│ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─
                                                <input type='checkbox' name='node_id[]' value='{$v4['id']}' class='radio radio-node-{$v4['id']}' pid='{$v4['pid']}' level='3'  onclick='javascript:checknode(this);' autocomplete='off'> {$v4['title']} ({$v4['name']})</td>
                                        </tr>";
                                }
                            }
                        }
                    }
                }
            }
        }
        return $nodeAll_html;
    }
}
