<?php
namespace Cmd\Controller;
use Common\Controller\BaseController;
use Common\Service\DataService;
use Org\Util\Date;
class AllotController extends BaseController {

    /*
    *定时任务
    *type
    *       10-客户分配
    *       20-客户回收
    *       30-
    *       40-
    */
    public function index()
    {
        set_time_limit(0);
        $allocation_id = I("get.allocation_id");
        $this->allot($allocation_id);//分配
    }

    //分配规则
    protected function allot($allocation_id="")
    {
        $DataService = new DataService();
        if(empty($allocation_id)){
            //获取分配规则
            $allots = D('UserAllocation')->where(array('status'=>1,'start'=>1))->order('sort asc')->select();
        }else{
            //获取分配规则
            $allots = D('UserAllocation')->where(array('user_allocation_id'=>$allocation_id))->order('sort asc')->select();
        }
        //获取转入申请不可分配的数据
        $zl_user_applydb = D('UserApply');
        $userApply = $zl_user_applydb->field('user_id')->where(array('status'=>10))->select();
        foreach($userApply as $k => $v){
            $userApplyArr[] = $v['user_id'];
        }
        //对分配规则进行筛选操作
        foreach ($allots as $k => $v) {
            if ($v['isave'] == 2) {        //不均的分配规则进行拆分重组
                $nums = explode(',', $v['allocationnum']);
                $roles = explode(',', $v['allocation_roles']);
                foreach ($nums as $k1 => $v1) {
                    unset($allots[$k]);
                    $v2[$k1] = $v;
                    $v2[$k1]['allocationnum'] = $v1;
                    $v2[$k1]['allocation_roles'] = $roles[$k1];
                    $allots[] = $v2[$k1];
                }
            }

        }
        foreach($allots as $topkey => $allot){
            //是否有指定日期
            $specify_days = array();
            if(!empty($allot['specify_days'])) {
                //转化时间戳匹配  防止一些浏览器时间格式不一致
                $specify_days = explode(',', $allot['specify_days']);
                foreach ($specify_days as $_days_k => $_days_v) {
                    $specify_days[$_days_k] = strtotime($_days_v);
                }
            }
            if(!in_array(strtotime(date('Y-m-d')), $specify_days)){
                //是否有节假日限制？
                if((!empty($allot['holiday']))){
                    $holiday = explode(',', $allot['holiday']);
                    $get_holiday = D('Api','Service')->getApiHoliday(date('Ymd'));
                    if($get_holiday['code']==0){
                        if(in_array($get_holiday['data'], $holiday)){
                            $falg_msg[] =  '失败原因:今天不在允许节假日限制，'.'规则名称:'.$allot['allocationname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
                        }
                    }
                }
                //是否有星期限制？
                if(!empty($allot['week_text']) && $allot['week_text']!=0){
                    $week_text = explode(',', $allot['week_text']);
                    if(!in_array(date('N'), $week_text)){
                        $falg_msg[] =  '失败原因:今天不在允许星期内，'.'规则名称:'.$allot['allocationname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
                    }
                }
            }
            //查询分配人
            $join = "left join zl_allocation_systemuser on zl_system_user.system_user_id=zl_allocation_systemuser.system_user_id";
            if ($allot['isave'] == 2) {
                $allotUser = D('SystemUser')->field('zl_system_user.system_user_id,realname,zone_id')->join($join)->where(array('user_allocation_id'=>$allot['user_allocation_id'],'zl_allocation_systemuser.status'=>1,'zl_allocation_systemuser.role_id'=>$allot['allocation_roles'],'usertype'=>array('NEQ',10)))->select();
            }else{
                $allotUser = D('SystemUser')->field('zl_system_user.system_user_id,realname,zone_id')->join($join)->where(array('user_allocation_id'=>$allot['user_allocation_id'],'zl_allocation_systemuser.status'=>1,'usertype'=>array('NEQ',10)))->select();
            }
            if(empty($allotUser)){
                $falg_msg[] =  '失败原因:找不到需要分配的员工，'.'规则名称:'.$allot['allocationname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
            }
            //查询分配渠道
            $channel = M('Channel')->select();
            $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $channelArr = $Arrayhelps->subFinds($channel,$allot['channel_id'],"channel_id","pid");
            $allotChannel = array();
            foreach($channelArr as $k => $v){
                $allotChannel[] =  $v['channel_id'];
            }
            $allotChannel[] = $allot['channel_id'];
            
            $allotUserTotal = count($allotUser);//分配总人数
            $allotTotal = $allotUserTotal * $allot['allocationnum'];//分配总数据条数
            $allotChannelTotal = count($allotChannel);//分配总渠道
            $allotChannelAverage = ceil($allot['allocationnum'] / $allotChannelTotal); //每人每渠道分配数量
            $nowtime = time();
            $userdb = D('User');
            $where = array();
            $where['status'] = 160;
            $where['attitude_id'] = array('IN',array('0','1','2','8','9','10'));
            $startnum = $nowtime - (86400 * $allot['startnum']);
            $intervalnum = $startnum - (86400 * $allot['intervalnum']);
            $where['createtime'][] = array('ELT',$startnum);
            $where['createtime'][] = array('EGT',$intervalnum);
            
            //查找区域
            $zonedb = D('Zone');
            $zone = $zonedb->field('zone_id,parentid')->where(array('status'=>1))->select();
            $zoneArr = $Arrayhelps->subFinds($zone,$allot['zone_id'],"zone_id","parentid");
            $zoneIds = array();
            foreach($zoneArr as $zkey => $zvalue){
                $zoneIds[] = $zvalue['zone_id'];
            }
            $zoneIds[] = $allot['zone_id'];
            
            $where['zone_id'] = array('IN',$zoneIds);
            $where['user_id'] = array('NOT IN',$userApplyArr);


            //获取近期放弃客户
            $excludeTime = $nowtime - (86400 * 7);
            $excludeUser = D('UserCallback')->field('user_id')->where(array('remark'=>array('LIKE','客户放弃:%'),'callbacktime'=>array('EGT',$excludeTime)))->select();
            $excludeUserArr = array();
            foreach($excludeUser as $k => $v){
                $excludeUserArr[$v['user_id']] = $v['user_id'];
            }
            if ($excludeUserArr) {
                $where['user_id'] = array('NOT IN',$excludeUserArr);
            }
            //获取所有资源信息
            $where['channel_id'] = array('IN',$allotChannel);
            if ($allot['banstatus']) {
                $bans = explode(',', $allot['banstatus']);
            }

            //判断是否有禁止的选项，有则剔除
            $statuss = C('USER_ATTITUDE');
            foreach ($statuss as $key => $value) {
                $statusss[] = $value['num'];
            }
            foreach ($statusss as $key => $value) {
                if (in_array($value, $bans)) {
                    unset($statusss[$key]);
                }
            }
            $where['attitude_id'] = array('IN', $statusss);
            $resource = $userdb->field('user_id,infoquality,channel_id,createtime')->where($where)->order('createupdatetime desc')->limit($allotTotal)->select();
            if(empty($resource)) {
                $falg_msg[] = '失败原因:客户资源不足分配，'.'规则名称:'.$allot['allocationname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
            }

            $resultUser = array();
            foreach($resource as $k => $v){
                $v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
                $resultUser[$v['channel_id']][] = $v;
            }

            //平均分配所有渠道数据
            $user = array();
            foreach($allotChannel as $k => $v){
                if(!empty($resultUser[$v])){
                    $user[$k]['user'] = $resultUser[$v];
                    $user[$k]['usertotal'] = count($user[$k]['user']);
                    $i=0;
                    for($i;$i<$allotChannelAverage;$i++){
                        foreach($allotUser as $userkey => $uservalue){
                            if($allotUser[$userkey]['allotnum'] <$allot['allocationnum']){
                                if(!empty($user[$k]['user'])){
                                    $current_user = reset($user[$k]['user']);
                                    $current_user_key = key($user[$k]['user']);
                                    $allotUser[$userkey]['arr'][] =$current_user;
                                    unset($user[$k]['user'][$current_user_key]);
                                    $allotUser[$userkey]['allotnum'] +=1;
                                    $current_user['infoquality'] = empty($current_user['infoquality']) ? 4 : $current_user['infoquality'];
                                    $allotUser[$userkey]['allotnumchannel'][$v][$current_user['infoquality']] +=1;
                                }
                            }
                        }
                    }
                }
            }



            //数据补全
            foreach($allotUser as $userkey1 => $uservalue1){
                if($uservalue1['allotnum'] < $allot['allocationnum']){
                    foreach($user as $userkey2 => $uservalue2){
                        foreach($user[$userkey2] as $userkey3 => $uservalue3){
                            if($userkey3 != 'usertotal'){
                                foreach($uservalue3 as $userkey4 => $uservalue4){
                                    if($allotUser[$userkey1]['allotnum'] < $allot['allocationnum'] && !empty($uservalue4)){
                                        $allotUser[$userkey1]['arr'][] = $uservalue4;
                                        $allotUser[$userkey1]['allotnum'] +=1;
                                        $uservalue4['infoquality'] = empty($uservalue4['infoquality']) ? 4 : $uservalue4['infoquality'];
                                        $allotUser[$userkey1]['allotnumchannel'][$uservalue4['channel_id']][$uservalue4['infoquality']] +=1;
                                        unset($user[$userkey2][$userkey3][$userkey4]);
                                    }
                                }
                            }
            
                        }
                    }
                }
            }
            
            $status = C('USER_STATUS');
            $dateYMD = date('Ymd',$nowtime);
            $zl_user_allocation_logsdb = D('UserAllocationLogs');
            $zl_user_callbackdb = D('UserCallback');
            foreach($allotUser as $allotkey => $allotvalue){
                $arrIds = array();
                foreach($allotvalue['arr'] as $arrkey => $arrvalue){
                    $arrIds[] = $arrvalue['user_id'];
                }
                if(empty($arrIds)) break;
                $data = array();
                $data['status'] = $status['20']['num'];
                $data['mark'] = 1;
                $data['zone_id'] = $allotvalue['zone_id'];
                $data['lastvisit'] = $nowtime;
                $data['nextvisit'] = 0;
                $data['visittime'] = 0;
                $data['attitude_id'] = 0;
                $data['callbacknum'] = 0;
                $data['abandonum'] = 0;
                $data['system_user_id'] = $allotvalue['system_user_id'];
                $data['allocationtime'] = $nowtime;
                $data['updatetime'] = $nowtime;
                $data['updateuser_id'] = $allotvalue['system_user_id'];
                $data['waytype'] = 0;
                $result = $userdb->where(array('user_id'=>array('IN',$arrIds)))->save($data);
                $callbackData['status'] = 0;
                $result2 = $zl_user_callbackdb->where(array('user_id'=>array('IN',$arrIds)))->save($callbackData);

                //插入回访记录
                foreach ($arrIds as $arrkey1 => $arrvalue1){
                    $callbackDataAdd = array();
                    $callbackDataAdd['user_id'] = $arrvalue1;
                    $callbackDataAdd['system_user_id'] = 0;
                    $callbackDataAdd['remark'] = '系统分配';
                    $callbackDataAdd['status'] = 1;
                    $callbackDataAdd['callbacktime'] = $nowtime;
                    $callbackDataAdd['nexttime'] = $nowtime;
                    $callbackDataAdd['callbacktype'] = 30;
                    D('UserCallback')->add($callbackDataAdd);
                    //添加数据记录
                    $dataLog['operattype'] = '2';
                    $dataLog['operator_user_id'] = 0;
                    $dataLog['user_id'] = $arrvalue1;
                    $dataLog['logtime'] = time();
                    $DataService->addDataLogs($dataLog);
                }

            }
            $falg_msg[] = '分配成功:'.$result.'条数据/每人，'.'规则名称:'.$allot['allocationname'].'，执行时间:'.date('Y-m-d H:i:s');
        }
        exit;
        if(!empty($allocation_id)){
            $this->success($falg_msg[0]);
        }elseif(!empty($falg_msg)){
            echo '自动分配---------------------------start:<br/>';
            foreach($falg_msg as $k=>$msg_v){
                echo '  '.($k+1).'、'.$msg_v.'<br/>';
            }
            echo '自动分配---------------------------end;<br/>';
        }
        
    }
    
    function printDIY($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

}