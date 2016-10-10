<?php
/*
|--------------------------------------------------------------------------
| System公共控制器
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace Common\Controller;
use Common\Controller\SystemUserController;
use Common\Controller\BaseController;
use Org\Util\Rbac;

class SystemController extends BaseController
{
    protected $system_user;
    protected $system_user_id;
    protected $system_user_role_id;
    public function _initialize()
    {
        parent::_initialize();
        $this->system_user_id = session('system_user_id');
        $this->system_user = session('system_user');
        //权限判断  换取节点
        $this->isLogin();
        $this->assign('system_user', $this->system_user);
        //是否已完善信息
        if(ACTION_NAME!='addSystemUserInfo' && ACTION_NAME!='addSystemUserInfoTwo' && $this->system_user['isuserinfo']==0) $this->redirect('System/Index/addSystemUserInfo');
        //登陆告警
        if(session('login_alarm')) $this->redirect('/System/Admin/loginAlarm');
        //职位ID
        $getRole = $this->changeUserRole();
        $this->system_user_role_id = $getRole['role_id'];
        $this->assign('system_user_role', session('system_user_role'));
        //权限验证
        if(C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',',C('NOT_AUTH_MODULE')))){
            if(!Rbac::AccessDecision()){
                if(!session(C('USER_AUTH_KEY'))){
                    $this->redirect('请重新登录', 1, C('USER_AUTH_GATEWAY'));
                }
                if(C('RBAC_ERROR_PAGE')){
                    $this->redirect('暂无权限', 1);
                    //$this->redirect(C('RBAC_ERROR_PAGE'));
                }
            }
            $systenUserMain = new SystemUserController();
            $result = $systenUserMain->proveToken($this->system_user_id);
            if ($result['code'] != 0) {
                $this->redirect('请重新登录', 1, C('USER_AUTH_GATEWAY'),0);
            }
            //当前Controller 权限列表
            $_access_list = $_SESSION['_ACCESS_LIST'];
            $_access_list = $_access_list[strtoupper(MODULE_NAME)][strtoupper(CONTROLLER_NAME)];
            $this->assign('access_list', $_access_list);
        }
        //获取左侧边栏
        $this->sideMenu();
    }

    /**
     * 系统登录判断
     * @author cq
     * @return boolean
     */
    public function isLogin(){
        if(!session(C('USER_AUTH_KEY'))){
            $this->redirect('请重新登录', 1, C('USER_AUTH_GATEWAY'),0);
        }
        $userInfo = $this->system_user;
        $this->assign('userinfo',$userInfo);
        //判断是否开启禁止登录功能
        $role_id = $userInfo['role_id'];
        $open_login = C('open_login');
        $w_list = C('w_list');//登录白名单组

        if(!Rbac::checkLogin()){
            session('system_user_id',null);
            $this->redirect(C('USER_AUTH_GATEWAY'));
        }else{
            if(!empty($open_login) && !in_array($role_id, $w_list)){
                session('system_user_id',null);
                $this->redirect(C('USER_AUTH_GATEWAY'));
            }
        }
    }

    /**
     * 获取职位ID（多个）cookie =》 角色切换
     * @author zgt
     */
    public function changeUserRole($role_id=null)
    {
        $session_role = session('system_user_role');
        if(empty($role_id)){
            if(session('system_user_role_'.$this->system_user_id)){
                $system_user_role = session('system_user_role_'.$this->system_user_id);
            }else{
                $system_user_role = $session_role[0];
                session('system_user_role_'.$this->system_user_id, $system_user_role);
            }
        }else{
            $system_user_role = false;
            foreach($session_role as $k=>$v){
                if($role_id==$v['role_id']){
                    $system_user_role = $v;
                    session('system_user_role_'.$this->system_user_id, $v);
                }
            }
        }
        session('ROLEID_AUTH_KEY',$system_user_role);
        return $system_user_role;
    }

    /**
     * 设置侧边菜单
     * @author cq
     */
    public function sideMenu()
    {
        $sysUserInfo = session('system_user');
        if(session('sidebar')){
            $sidebar = session('sidebar');
        }else{
            if($this->system_user_role_id!=C('ADMIN_SUPER_ROLE')){
                $sidebar = D('Role')->getRoleNode();
            }else{
                $sidebar = D('Role')->getRoleNodeAll();
            }
            session('sidebar',$sidebar);
        }

        $newSideber = array();
        $childNodes = array();
        $i = 0;
        foreach ($sidebar as $k => $v) {
             if ($v['name'] == CONTROLLER_NAME) $controllerName = $v['title'];
             if ($v[display] == 3) $newSideber[$k] = $v;
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
        //设置配置参数的值
        C('controller_name', $controllerName);
        C('action_name',$actionName);

        //设置当前用户能访问的所有节点
        session('sidebar',$newSideber);
        //设置当前用户能访问的所有子节点
        session('user_child_nodes', $childNodes);

        //模板变量赋值
        $this->assign('controllername', $controllerName);
        $this->assign('actionname',$actionName);
        $titleList[0]['titlename']=$actionName;
        $this->assign('titlelist',$titleList);
        $this->assign('sidebar',$newSideber);
        $this->assign('userinfo',$sysUserInfo);

    }

    /**
     * 网络电话公共接口
     * @author zgt
     * $caller_num:主叫方电话 $called_num：被叫方电话 $userSign标识记录 $maxtime:通话时长
     */
    protected function call($caller_num,$called_num,$userSign,$defaultCall='01053856070',$maxtime='300'){
        import('Vendor.Alidayu.TopSdk');

        $client = new \TopClient;
        $request = new \AlibabaAliqinFcVoiceNumDoublecallRequest;
        $request->setSessionTimeOut($maxtime);
        $request->setExtend($userSign);
        $request->setCallerNum($caller_num);
        $request->setCallerShowNum($defaultCall);
        $request->setCalledNum($called_num);
        $request->setCalledShowNum($defaultCall);
        return $client->execute($request);
    }

    /**
     * 手机号码归属地
     * @param $phone 手机号码
     * @return json
     * @author zgt
     */
    public function phoneVest2($phone)
    {
        import('Vendor.Alidayu.TopSdk');

        $client = new \TopClient;
        $request = new \AlibabaAliqinFcVoiceNumDoublecallRequest;
//        $request = new PlaceMobileGetRequest;
        $request->setPhone("13811139999");
        return $client->execute($request);
    }

    /**
     * 获取用户所属用户组
     * @author cq
     * @return array
     */
    public function getUserRoleID(){
        $user_id = $this->system_user_id;
        return D('RoleUser')->where(array('user_id'=>$user_id))->find();

    }

    /**
     * 比对指定用户是否在指定用户组内
     * @author cq
     * @param unknown $user_id
     * @param unknown $role_id 指定用户组
     */
    public function isRoleUser($user_id,$role_id){
        $result = D('RoleUser')->where(array('user_id'=>$user_id))->field('role_id')->select();
        foreach ($result as $key => $value) {
            if ($value['role_id'] == $role_id) return true;
        }
        return false;
    }

    /**
     * 导入Excel
     * @param  $filename 路径文件名
     * @author   Nxx
     */
    public function inputExcel($filename,$encode='utf-8')
    {
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel2005");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.Style.Alignment.php");
        import("Org.Util.PHPExcel.IOFactory.php");

        $objReader = \PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
                                    //load('C:/Users/Administrator/Desktop/1231_2016-04-25-17-17-01.xls');
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }


    /**
     * system 后台分页生成
     * @author zgt
     * $page:页码 $shownum：显示数 $count：总数  $request:所有接收数据 $url：url
     */
    protected function Paging($page,$shownum,$count,$request,$url=__ACTION__,$isJs=null,$type='system',$urlhtml=null){
        if(!empty($request)) unset($request['page']);
        //加载分页类
        $pagingClass = new \Org\Util\Paging();
        $pagingClass->page = $page;
        $pagingClass->count = $count;
        $pagingClass->shownum = $shownum;
        if(empty($isJs)){
            $pagingClass->url = $url.'?'.http_build_query($request).'&page=';
        }else{
            $pagingClass->url = $url;
            $pagingClass->urlhtml = ')';
        }
        if(!empty($urlhtml)){
            $pagingClass->urlhtml = $urlhtml;
        }
        $pagingClass->type = $type;
        return $pagingClass->createPaging();
    }

    /**
     * @author nxx
     * @param unknown $maxSize 文件上传大小
     * @param unknown $exts 文件上传类型
     * @param unknown $rootPath 附件根目录
     * @param unknown $savePath 附件二级目录
     * @param unknown $subName 附件三级目录
     */
    public function uploadFile($exts,$rootPath,$savePath,$maxSize=10567840,$subName=array('date','Ymd')){
        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize;// 设置附件上传大小
        $upload->exts = $exts;// 设置附件上传类型
        $upload->rootPath  = $rootPath;// 设置附件上传根目录
        $upload->savePath  = $savePath;
        $upload->subName = $subName;
        $upLoadInfo = $upload->upload();

        if(!$upLoadInfo) $this->error($upload->getError());
        return $upLoadInfo;
    }
    


    






}