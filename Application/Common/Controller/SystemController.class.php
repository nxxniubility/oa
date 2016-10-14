<?php
/**
 * 后台基础类
 * @author Sunles
 */

namespace Common\Controller;
use Common\Controller\BaseController;
use Extend\TagLib\Paging;
use Org\Util\Rbac;

class SystemController extends BaseController{

    protected $system_user_id;
    protected $system_user;
    public function _initialize()
    {
        parent::_initialize();
        $this->system_user_id = session('system_user_id');
        $this->system_user = session('system_user');
        //系统登录判断
        $this->isLogin();
        $this->assign('system_user', $this->system_user);
        //是否已完善信息
        if(ACTION_NAME!='addSystemUserInfo' && ACTION_NAME!='addSystemUserInfoTwo' && $this->system_user['isuserinfo']==0) $this->redirect('System/Index/addSystemUserInfo');
        //登陆告警
        if(session('login_alarm')) $this->redirect('/System/Admin/loginAlarm');
        //职位ID
        $this->assign('system_user_role', session('system_user_role'));
        //权限验证
        if(C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',',C('NOT_AUTH_MODULE')))){
            if(!Rbac::AccessDecision()){
                if(C('RBAC_ERROR_PAGE')){
                    $this->redirect('暂无权限', 1);
                }
            }
            $result = D('SystemUser','Service')->proveToken($this->system_user_id);
            if ($result['code'] != 0) {
                $this->redirect(C('USER_AUTH_GATEWAY'), '请重新登录');
            }
        }
        //当前Controller 权限列表
        $_access_list = $_SESSION['_ACCESS_LIST'];
        $_access_list = $_access_list[strtoupper(MODULE_NAME)][strtoupper(CONTROLLER_NAME)];
        $this->assign('access_list', $_access_list);
    }

    /**
     * 系统登录判断
     * @author cq
     * @return boolean
     */
    public function isLogin(){
        if(!session(C('USER_AUTH_KEY'))){
            $this->redirect(C('USER_AUTH_GATEWAY'), '请重新登录');
        }
        //判断是否开启禁止登录功能
        $system_user_role = session('system_user_role');
        $role_id = $system_user_role[0]['id'];
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
     * system 后台分页生成
     * @author zgt
     * $page:页码 $shownum：显示数 $count：总数  $request:所有接收数据 $url：url
     */
    protected function Paging($page,$shownum,$count,$request,$url=__ACTION__,$isJs=null,$type='system',$urlhtml=null){
        if(!empty($request)) unset($request['page']);
        //加载分页类
        $pagingClass = new Paging();
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
}