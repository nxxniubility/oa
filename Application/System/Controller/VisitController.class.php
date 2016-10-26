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
            $request = I('post.');
            if($request['type']=='getUserVisitInfo'){
                $reflag = D('User', 'Service')->getUserVisitInfo($request);
                $this->ajaxReturn($reflag['code'],$reflag['msg'],$reflag['data']);
            }else if($request['type']=='submit'){
                $data['user_id'] = $request['user_id'];
                $data['system_user_id'] = $request['system_user_id'];
                $reflag = D('User', 'Service')->addUserVisit($data);
                if ($reflag['code'] == 0) {
                    $this->ajaxReturn('0','操作成功，请通知分配人员到前台接待',U('System/Visit/visitList'));
                }else{
                    $this->ajaxReturn(1,$reflag['msg']);
                }
            }
        }
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
        $re_userAll = D('User', 'Service')->getUserList($where, 'visittime desc', (($re_page-1)*15).',15');
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
