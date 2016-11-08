<?php
namespace Cmd\Controller;
use Common\Controller\BaseController;
use Common\Service\DataService;

class RecoverController extends BaseController {

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
        $abandon_id = I("get.abandon_id");
        $this->aband($abandon_id);//回收
        
    }

    //调用回收规则
    protected function aband($abandon_id=""){
        $DataService = new DataService();
        $UserAbandonDB = D('UserAbandon');
        if(!empty($abandon_id)){
            $abandons = $UserAbandonDB->getAbandonList(array('user_abandon_id'=>$abandon_id));
        }else{
            $abandons = $UserAbandonDB->getAbandonList(array('status'=>1,'start'=>1));
        }
        
        //获取所有渠道
        $channels = M('Channel')->select();
        $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
        $nowtime = time();
        $zonedb = D('Zone');
        $zone = $zonedb->field('zone_id,parentid')->where(array('status'=>1))->select();
        
        foreach($abandons as $k => $abandon){
            $time_now = time();
            //是否有指定日期
            $specify_days = array();
            if(!empty($abandon['specify_days'])){
                //转化时间戳匹配  防止一些浏览器时间格式不一致
                $specify_days = explode(',', $abandon['specify_days']);
                foreach($specify_days as $_days_k=>$_days_v){
                    $specify_days[$_days_k] = strtotime($_days_v);
                }
            }
            if(!in_array(strtotime(date('Y-m-d',$time_now)), $specify_days)){
                if(!empty($abandon['holiday'])){
                    //是否有节假日限制？
                    $holiday = explode(',', $abandon['holiday']);
                    $get_holiday = D('Api','Service')->getApiHoliday(date('Ymd',$time_now));
                    if($get_holiday['code']==0){
                        if(in_array($get_holiday['data'], $holiday)){
                            $falg_msg[] =  '失败原因:今天不在允许节假日限制，'.'规则名称:'.$abandon['abandonname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
                        }
                    }
                }
                if(!empty($abandon['week_text']) && $abandon['week_text']!=0){
                    //是否有星期限制？
                    $week_text = explode(',', $abandon['week_text']);
                    if(!in_array(date('N',$time_now), $week_text)){
                        $falg_msg[] =  '失败原因:今天不在允许星期内，'.'规则名称:'.$abandon['abandonname'].'，执行时间:'.date('Y-m-d H:i:s');continue;
                    }
                }
            }
            //查找相应渠道
            $channelArr = $Arrayhelps->subFinds($channels,$abandon['channel_id'],"channel_id","pid");
            $abandonChannel = array();
            foreach($channelArr as $k => $v){
                $abandonChannel[] =  $v['channel_id'];
            }
            $abandonChannel[] = $abandon['channel_id'];
            
            //获取相应区域
            $zoneArr = $Arrayhelps->subFinds($zone,$abandon['zone_id'],"zone_id","parentid");
            $zoneIds = array();
            foreach($zoneArr as $zkey => $zvalue){
                $zoneIds[] = $zvalue['zone_id'];
            }
            $zoneIds[] = $abandon['zone_id'];
            
            //获取职位对应员工
            $roleUserWhere['role_id'] = array('IN',explode(',',$abandon['abandon_roles']));
            //$roleUserWhere['status'] = 1;
            //$roleUserWhere['usertype'] = array('NEQ',10);
            $roleUserWhere['zone_id'] = array('IN',$zoneIds);
            $systemUserJoin = "left join zl_system_user on zl_role_user.user_id=zl_system_user.system_user_id";
            
            $abandonUser = D('RoleUser')->field("system_user_id,realname")->where($roleUserWhere)->join($systemUserJoin)->select();
            if(empty($abandonUser)){
                $falg_msg[] =  '失败原因:找不到需要分配的员工，'.'规则名称:'.$abandon['abandonname'];continue;
            }
            $abandonUserArr =array();
            foreach($abandonUser as $zbandon_k => $zbandon_v){
                $abandonUserArr[] = $zbandon_v['system_user_id'];
            }
            $where = array();
            $where1 = array();
            $map = array();

            $where['callbacknum'] = array('LT',$abandon['callbacknum']);
            //未到达保护天数 加上节假日处理 20161108
            $_curr_time = strtotime(data('Y-m-d',$time_now));
            $_day_interval['day_time'] = array(
                array('ELT', $_curr_time),
                array('EGT', ($_curr_time-($abandon['unsatisfieddays'] * 86400)) )
            );
            $_day_interval['day_type'] = 2;
            //获取区间节假日天数
            $_holidays = D('holiday')->where($_day_interval)->count();
            //计算一共保护天数
            $_unsatisfieddays = $abandon['unsatisfieddays'] + $_holidays;
            $lastvisit = $nowtime - (($_unsatisfieddays * 86400) - (3600*5));//2016-10-26 未达到要求保护天数减5小时
            $where['lastvisit'] = array('LT',$lastvisit);
            $where['_logic'] = 'and';

            //保护天数 加上节假日处理 20161108
            //重置查询条件day_time时间区间
            $_day_interval['day_time'] = array(
                array('ELT', $_curr_time),
                array('EGT', ($_curr_time-($abandon['attaindays'] * 86400)) )
            );
            //获取区间节假日天数
            $_holidays = D('holiday')->where($_day_interval)->count();
            //计算一共保护天数
            $_attaindays = $abandon['attaindays'] + $_holidays;
            $where1['callbacknum'] = array('EGT',$abandon['callbacknum']);
            $lastvisit1 = $nowtime - (($_attaindays * 86400) - (3600*5));//2016-10-26 达到要求保护天数减5小时
            $where1['lastvisit'] = array('LT',$lastvisit1);
            
            $map['_complex'][] = $where;
            $map['_complex'][] = $where1;
            $map['channel_id'] = array('IN',$abandonChannel);
            $USER_STATUS = C('USER_STATUS');
            $statusArr = array($USER_STATUS['20']['num']);
            $map['status'] = array('IN',$statusArr);
            $map['system_user_id'] = array('IN',$abandonUserArr);
            $map['_complex']['_logic'] = 'or';
            
            //按状态全部回收
            $USER_STATUS = C('USER_STATUS');
            $statusArr = array($USER_STATUS['20']['num'],$USER_STATUS['30']['num']);
            $map['status'] = array('IN',$statusArr);
            $map['system_user_id'] = array('IN',$abandonUserArr);
            $map['channel_id'] = array('IN',$abandonChannel);
            
            
            $data['status'] = $USER_STATUS['160']['num'];
            //$data['lastvisit'] = time();
            $user = D('User')->where($map)->field("user_id,system_user_id")->select();
            $result2 = D('User')->where($map)->setInc('weight',1);
            $result = D('User')->where($map)->save($data);
            
            if($result){
                $time_now_log = $time_now;
                foreach($user as $key => $value){
                    $callbackDataAdd['user_id'] = $value['user_id'];
                    $callbackDataAdd['system_user_id'] = $value['system_user_id'];
                    $callbackDataAdd['remark'] = '系统超时回收';
                    $callbackDataAdd['status'] = 1;
                    $callbackDataAdd['callbacktime'] = $time_now_log;
                    $callbackDataAdd['nexttime'] = $time_now_log;
                    $callbackDataAdd['callbacktype'] = 31;
                    D('UserCallback')->add($callbackDataAdd);
                    //添加数据记录
                    $dataLog['operattype'] = '7';
                    $dataLog['operator_user_id'] = 0;
                    $dataLog['user_id'] = $value['user_id'];
                    $dataLog['logtime'] = $time_now_log;
                    $DataService->addDataLogs($dataLog);
                }
            }
            $falg_msg[] = '已成功回收:'.$result.'条数据，'.'规则名称:'.$abandon['abandonname'].'，执行时间:'.date('Y-m-d H:i:s');
        }
        if(!empty($abandon_id)){
            $this->success($falg_msg[0]);
        }elseif(!empty($falg_msg)){
            echo '自动回收---------------------------start:<br/>';
            foreach($falg_msg as $k=>$msg_v){
                echo '  '.($k+1).'、'.$msg_v.'<br/>';
            }
            echo '自动回收---------------------------end;<br/>';
        }
    }
    
    function printDIY($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit();
    }

}