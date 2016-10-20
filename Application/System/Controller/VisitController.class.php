<?php
namespace System\Controller;
use Common\Controller\SystemController;

class VisitController extends SystemController
{
    //控制器前置
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
    * *****************************************************
    * 到访列表
    * *****************************************************
    * @author nxx
    */
    public function visitList()
    {
        if(IS_POST){
            //确认到访
            $marketArr = C('ADMIN_MARKET_ROLE');
            $educationalArr = C('ADMIN_EDUCATIONAL_ROLE');
            $request = I('post.');
            if($request['type']=='getSystemList') {
                $where_system['status'] = 1;
                $where_system['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $where_system['usertype'] = array('neq',10);
                $where_system['role_id'] = array('in',$marketArr);
                $systemUserList = D('SystemUser', 'Service')->getSystemUserList($where_system);
                if(!empty($systemUserList['data'])){
                    $this->ajaxReturn('0','获取成功',$systemUserList['data']);
                }else{
                    $this->ajaxReturn('1','获取失败');
                }
            }else if($request['type']=='getUser'){
                $userInfo = D('User', 'Service')->getUserInfo(array('user_id'=>$request['user_id']));
                $userInfo = $userInfo['data'];
                if(!empty($userInfo) && $userInfo['status']==160){
                    $where_system[C('DB_PREFIX').'system_user.status'] = 1;
                    $where_system[C('DB_PREFIX').'system_user.zone_id'] = $this->system_user['zone_id'];
                    $where_system[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
                    $where_system[C('DB_PREFIX').'role.id'] = array('in',$marketArr);
                    $where_system[C('DB_PREFIX').'system_user_engaged.status'] = array('neq',1);
                    $systemUserList = D('SystemUser', 'Service')->getSystemUserVisit($where_system);
                    foreach($systemUserList['data'] as $k=>$v){
                        if(empty($v['visitnum'])){
                            $systemVisitNum = 0;
                            $systemVisitKey = $k;
                        }else{
                            if(empty($systemVisitNum)){
                                $systemVisitNum = $v['visitnum'];
                                $systemVisitKey = $k;
                            }else{
                                if($systemVisitNum>$v['visitnum']){
                                    $systemVisitNum = $v['visitnum'];
                                    $systemVisitKey = $k;
                                }
                            }
                        }
                    }
                    $this->ajaxReturn(1,'客户属于回库状态',$systemUserList['data'][$systemVisitKey]);
                }else{
                    $systemInfo = D('SystemUser', 'Service')->getSystemUserInfo(array('system_user_id'=>$userInfo['system_user_id']));
                    $systemInfo = $systemInfo['data'];
                    $newRoles = array_merge(explode(',', $marketArr),explode(',', $educationalArr));
                    foreach ($systemInfo['user_roles'] as $key => $value) {
                        if (in_array($value['id'], $newRoles)) {
                            $theTure = 'ture';
                        }
                    }  
                    //属于教务或者销售
                    if($theTure){
                        //是否本中心
                        if($userInfo['zone_id'] == $this->system_user['zone_id']){
                            $this->ajaxReturn(2,'该员工属于本中心员工',$systemInfo);
                        }else{
                            $this->ajaxReturn(3,'该员工不属于本中心',$systemInfo);
                        }
                    }else{
                        $where_system[C('DB_PREFIX').'system_user.status'] = 1;
                        $where_system[C('DB_PREFIX').'system_user.zone_id'] = $this->system_user['zone_id'];
                        $where_system[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
                        $where_system[C('DB_PREFIX').'role.id'] = array('in',$marketArr);
                        $where_system[C('DB_PREFIX').'system_user_engaged.status'] = array('neq',1);
                        $systemUserList = D('SystemUser', 'Service')->getSystemUserVisit($where_system);
                        foreach($systemUserList['data']['data'] as $k=>$v){
                            if(empty($v['visitnum'])){
                                $systemVisitNum = 0;
                                $systemVisitKey = $k;
                            }else{
                                if(empty($systemVisitNum)){
                                    $systemVisitNum = $v['visitnum'];
                                    $systemVisitKey = $k;
                                }else{
                                    if($systemVisitNum>$v['visitnum']){
                                        $systemVisitNum = $v['visitnum'];
                                        $systemVisitKey = $k;
                                    }
                                }
                            }
                        }
                        $this->ajaxReturn(4,'所属人非教务/销售人员',!empty($systemUserList['data']['data'][$systemVisitKey])?$systemUserList['data']['data'][$systemVisitKey]:0);
                    }
                }
            }else if($request['type']=='submit'){
                $reflag = D('User', 'Service')->addUserVisit($request['user_id'],$request['system_user_id'],$this->system_user_id);
                if ($reflag['code'] == 0) {
                    $this->ajaxReturn('0','操作成功，请通知分配人员到前台接待',U('System/Visit/visitList'));
                }else{
                    $this->ajaxReturn(1,$reflag['msg']);
                }
            }
        }else{
            //获取参数 页码
            $request = I('get.');
            $where['zone_id'] = $this->system_user['zone_id'];
            if(!empty($request['search'])){
                $request['search'] = trim($request['search']);
                if($request['keyname']=='username'){
                    $where[$request['keyname']] = encryptPhone($request['search'], C('PHONE_CODE_KEY'));
                }else{
                    $where[$request['keyname']] = $request['search'];
                }
                unset($where['zone_id']);
            }else{
                if ($request['status'] == 0) {
                    $where['status'] = array('IN', array(20, 30));
                    unset($request['status']);
                }
                if(!empty($request['visittime'])){
                    $_time = explode('@', str_replace('/', '-', $request['visittime']));
                    $where['visittime'] = array(array('EGT', ($_time[0]==''?1: ($_time[0]== 'time' ? time() : strtotime($_time[0])) )), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1].' 23:59'))), 'AND');
                }
            }
            $re_page = isset($request['page']) ? $request['page'] : 1;
            unset($request['page']);
            //客户列表
            $re_userAll = D('User', 'Service')->getList($where, 'visittime desc', (($re_page-1)*15).',15');
            $data['userAll'] = $re_userAll['data']['data'];
            //加载分页类
            $data['paging'] = $this->Paging($re_page, 15, $re_userAll['data']['count'], $request);
            $data['request'] = $request;
            if(empty($data['request']['visittime'])){
                $data['request']['visittime'] = '@';
            }
            $data['request']['visittime_val'] = explode('@',$data['request']['visittime']);
            $this->assign('data', $data);
            $this->display();
        }
    }


}
