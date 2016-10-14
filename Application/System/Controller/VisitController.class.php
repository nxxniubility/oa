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
    * @author zgt
    */
    public function visitList()
    {
        if(IS_POST){
            //确认到访
            $marketArr = C('ADMIN_MARKET_ROLE');
            $educationalArr = C('ADMIN_EDUCATIONAL_ROLE');
            $request = I('post.');
            if($request['type']=='getSystemList') {
                $where_system[C('DB_PREFIX').'system_user.status'] = 1;
                $where_system['zone_id'] = !empty($request['zone_id'])?$request['zone_id']:$this->system_user['zone_id'];
                $where_system[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
                $where_system['role_id'] = array('in',$marketArr);
//                if(!empty($request['role_id'])) $where_system[C('DB_PREFIX').'role.id'] = $request['role_id'];
//                else $where_system[C('DB_PREFIX').'role.id'] = array('in',$marketArr);
                $systemUserList = D('SystemUser')->getSystemUserAll($where_system,'','0,100');
                if(!empty($systemUserList))  $this->ajaxReturn('0','获取成功',$systemUserList);
                else $this->ajaxReturn('1','获取失败');
            }else if($request['type']=='getUser'){
                $userInfo = D('User')->getUserInfo($request['user_id']);
                if(!empty($userInfo) && $userInfo['status']==160){
                    $where_system[C('DB_PREFIX').'system_user.status'] = 1;
                    $where_system[C('DB_PREFIX').'system_user.zone_id'] = $this->system_user['zone_id'];
                    $where_system[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
                    $where_system[C('DB_PREFIX').'role.id'] = array('in',$marketArr);
                    $where_system[C('DB_PREFIX').'system_user_engaged.status'] = array('neq',1);
                    $systemUserList = D('SystemUser')->getSystemUserVisit($where_system);
//                    if($systemUserList['count']>0){
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
                        $this->ajaxReturn('1','客户属于回库状态',$systemUserList['data'][$systemVisitKey]);
//                    }else{
//                        $this->ajaxReturn('5','本中心销售忙线');
//                    }
                }else{
                    $systemInfo = D('SystemUser')->getSystemUserInfo($userInfo['system_user_id']);
                    //属于教务或者销售
                    if(in_array($systemInfo['role_id'],explode(',', $marketArr)) || in_array($systemInfo['role_id'],explode(',', $educationalArr))){
                        //是否本中心
                        if($userInfo['zone_id'] == $this->system_user['zone_id']){
                            $this->ajaxReturn('2','该员工属于本中心员工',$systemInfo);
                        }else{
                            $this->ajaxReturn('3','该员工不属于本中心',$systemInfo);
                        }
                    }else{
                        $where_system[C('DB_PREFIX').'system_user.status'] = 1;
                        $where_system[C('DB_PREFIX').'system_user.zone_id'] = $this->system_user['zone_id'];
                        $where_system[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
                        $where_system[C('DB_PREFIX').'role.id'] = array('in',$marketArr);
                        $where_system[C('DB_PREFIX').'system_user_engaged.status'] = array('neq',1);
                        $systemUserList = D('SystemUser')->getSystemUserVisit($where_system);
//                        if($systemUserList['count']>0){
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
                            $this->ajaxReturn('4','操作者非销售/教务',!empty($systemUserList['data'][$systemVisitKey])?$systemUserList['data'][$systemVisitKey]:0);
//                        }else{
//                            $this->ajaxReturn('5','本中心销售忙线');
//                        }
                    }
                }
            }else if($request['type']=='submit'){
                $reflag = D('User')->addUserVisit($request['user_id'],$request['system_user_id'],$this->system_user_id);
                if($reflag['code']==0){
                    $visitLogs = D('UserVisitLogs')->where(array('date'=>date('Ymd'),'system_user_id'=>$request['system_user_id']))->find();
                    if(!empty($visitLogs)){
                        $data['visitnum'] = array('exp','visitnum+1');
                        D('UserVisitLogs')->where(array('date'=>date('Ymd'),'system_user_id'=>$request['system_user_id']))->save($data);
                    }else{
                        $data['date'] = date('Ymd');
                        $data['system_user_id'] = $request['system_user_id'];
                        $data['visitnum'] = 1;
                        D('UserVisitLogs')->data($data)->add();
                    }
                    $this->ajaxReturn('0','操作成功，请通知分配人员到前台接待',U('System/Visit/visitList'));
                }else{
                    $this->ajaxReturn('1',$reflag['msg']);
                }
            }
        }else{
            //获取参数 页码
            $request = I('get.');
            $where[C('DB_PREFIX') . 'user.zone_id'] = $this->system_user['zone_id'];
            if(!empty($request['search'])){
                $request['search'] = trim($request['search']);
                if($request['keyname']=='username'){
                    $where[C('DB_PREFIX') .'user.'. $request['keyname']] = encryptPhone($request['search'], C('PHONE_CODE_KEY'));
                }else{
                    $where[C('DB_PREFIX') .'user.'. $request['keyname']] = $request['search'];
                }
                unset($where[C('DB_PREFIX') . 'user.zone_id']);
            }else{
//                $where[C('DB_PREFIX') . 'user.attitude_id'] = 2;
//                $where[C('DB_PREFIX') . 'user.visittime'] = array('NEQ',0);
//                $where['_logic'] = 'or';
                if ($request['status'] == 0) {
                    $where[C('DB_PREFIX') . 'user.status'] = array('IN', array(20, 30));
                    unset($request['status']);
                }
                if(!empty($request['visittime'])){
                    $_time = explode('@', str_replace('/', '-', $request['visittime']));
                    $where[C('DB_PREFIX') . 'user.visittime'] = array(array('EGT', ($_time[0]==''?1: ($_time[0]== 'time' ? time() : strtotime($_time[0])) )), array('LT', ($_time[1] == 'time' ? time() : strtotime($_time[1].' 23:59'))), 'AND');
                }
            }
            $re_page = isset($request['page']) ? $request['page'] : 1;
            unset($request['page']);
            //客户列表
            $re_userAll = D('User')->getAllUser($where, C('DB_PREFIX') . 'user.visittime desc', (($re_page - 1) * 15) . ',15');
            $data['userAll'] = $re_userAll['data'];
            //加载分页类
            $data['paging'] = $this->Paging($re_page, 15, $re_userAll['count'], $request);
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