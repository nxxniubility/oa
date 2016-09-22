<?php
/*
|--------------------------------------------------------------------------
| 用户表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class UserModel extends SystemModel
{
    protected $userDb,$userInfoDb,$feeDb,$feelogDb,$userApplyDb;

    public function _initialize()
    {
        $this->userInfoDb = M('user_info');
        $this->feeDb = M('fee');
        $this->feelogDb = M('fee_logs');
        $this->userApplyDb = M('user_apply');
    }



    /*
     * 获取客户基本信息
     * @author zgt
     * @return array
     */
    public function getUser($where, $field = "*")
    {
        return $this
            ->field($field)
            ->where($where)
            ->find();
    }

    /*
     * 获取自定义列
     * @author zgt
     * @return array
     */
    public function getColumn($columnType,$system_user_id)
    {
        $result = M('user_column')->where(array('system_user_id'=>$system_user_id,'columntype'=>$columnType))->order('sort ASC')->select();
        return $result;
    }

    /*
     * 添加自定义列自定义列
     * $columnNames = name-sort,name-sort......
     * @author zgt
     * @return array
     */
    public function addColumn($columnNames,$columnType,$system_user_id)
    {
        M('system_user_column')->where(array('system_user_id'=>$system_user_id,'columntype'=>$columnType))->delete();
        $data['system_user_id'] = $system_user_id;
        $data['columntype'] = $columnType;
        $columnNames = explode(',', $columnNames);
        $flag = false;
        foreach($columnNames as $k=>$v){
            $v = explode('-', $v);
            $data['columnname'] = $v[0];
            $data['sort'] = $v[1];
            $reflag = M('system_user_column')->data($data)->add();
            if($reflag!==false) $flag = ($flag+1);
        }
        return $flag;
    }
    /*
      * 获取中心下拥有客户的操作人
      * @author zgt
      * $request array('zoneIds'=>'1,2,3','status'=>'20')
      * @return array
      */
    public function getUserSystem($where=null){
        $DB_PREFIX = C('DB_PREFIX');
        // 多级zone 拼接
        if(!empty($where['zoneIds'])){
            foreach($where['zoneIds'] as $k=>$v){
                $arr[] = $v['zone_id'];
            }
            $where[$DB_PREFIX.'user.zone_id'] = array('like',$arr,'OR');
            unset($where['zoneIds']);
        }
        $result = $this
            ->field(array(
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.realname"
            ))
            ->where($where)
            ->join('__SYSTEM_USER__ on __USER__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->group("{$DB_PREFIX}user.system_user_id")->Distinct(true)
            ->select();
			
        return $result;
    }

    /*
     * 客户列表
     * @author zgt
     * $request array('zoneIds'=>'1,2,3','status'=>'20')
     * @return array
     */
    public function getAllUser($where=null,$order=null,$limit='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $_order = !empty($order)?$order:"{$DB_PREFIX}user.allocationtime DESC";
        // 多级zone 拼接
        if(!empty($where['zoneIds'])){
            foreach($where['zoneIds'] as $k=>$v){
                $arr[] = $v['zone_id'];
            }
            $where[$DB_PREFIX.'user.zone_id'] = array('IN',$arr);
            unset($where['zoneIds']);
        }
        if(!empty($where['role_id']) && $where['role_id']!=0){
            $reList = M('role_user')
                ->field('user_id')
                ->group("user_id")->Distinct(true)
                ->where(array('role_id'=>$where['role_id']))
                ->select();
            $systemUser = array();
            foreach($reList as $v){
                $systemUser[] = $v['user_id'];
            }
            $where[$DB_PREFIX.'user.system_user_id'] = array('IN', $systemUser);
            unset($where['role_id']);
        }

        $allUser['data'] = $this
            ->field(array(
                "{$DB_PREFIX}user.user_id",
                "{$DB_PREFIX}user.username",
                "{$DB_PREFIX}user.tel",
                "{$DB_PREFIX}user.qq",
                "{$DB_PREFIX}user.phonevest",
                "{$DB_PREFIX}user.realname",
                "{$DB_PREFIX}user.email",
                "{$DB_PREFIX}user.status",
                "{$DB_PREFIX}user.mark",
                "{$DB_PREFIX}user.zone_id",
                "{$DB_PREFIX}user.learningtype",
                "{$DB_PREFIX}user.mark",
                "{$DB_PREFIX}user.searchkey",
                "{$DB_PREFIX}user.interviewurl",
                "{$DB_PREFIX}user.callbacknum",
                "{$DB_PREFIX}user.infoquality",
                "{$DB_PREFIX}user.updatetime",
                "{$DB_PREFIX}user.allocationtime",
                "{$DB_PREFIX}user.lastvisit",
                "{$DB_PREFIX}user.course_id",
                "{$DB_PREFIX}user.nextvisit",
                "{$DB_PREFIX}user.visittime",
                "{$DB_PREFIX}user.createtime",
                "{$DB_PREFIX}user.attitude_id",
                "{$DB_PREFIX}user.introducermobile",
                "{$DB_PREFIX}user.learningtype",
                "{$DB_PREFIX}user.updateuser_id",
                "{$DB_PREFIX}user.reservetype",
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.realname as system_realname",
                "{$DB_PREFIX}user.channel_id",
                "A.system_user_id as updateuser_id",
                "A.realname as updateuser_realname"
            ))
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
//            ->join("LEFT JOIN (SELECT user_id FROM __ROLE_USER__ LIMIT 1) B ON B.user_id=__USER__.system_user_id")
            ->order($_order)
            ->limit($limit)
            ->select();
            $allUser['count'] = $this
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
//            ->join("LEFT JOIN (SELECT role_id,user_id FROM __ROLE_USER__ LIMIT 1) B ON B.user_id=__USER__.system_user_id")
            ->count();
        return $allUser;
    }
	 /*
     * 导出客户列表
     * @author zgt
     * $request array('zoneIds'=>'1,2,3','status'=>'20')
     * @return array
     */
    public function getOutPutUser($where=null,$order=null,$limit='0,10',$fields){
        $DB_PREFIX = C('DB_PREFIX');
        $_order = !empty($order)?$order:"{$DB_PREFIX}user.allocationtime DESC";
       
		$all_fields=array(
                "{$DB_PREFIX}user.user_id",
                "{$DB_PREFIX}user.username",
                "{$DB_PREFIX}user.tel",
                "{$DB_PREFIX}user.qq",
                "{$DB_PREFIX}user.realname",
                "{$DB_PREFIX}user.email",
                "{$DB_PREFIX}user.status",
                "{$DB_PREFIX}user.mark",
                "{$DB_PREFIX}user.zone_id",
                "{$DB_PREFIX}user.learningtype",
                "{$DB_PREFIX}user.remark",
				"{$DB_PREFIX}user.weight",
                "{$DB_PREFIX}user.searchkey",
                "{$DB_PREFIX}user.interviewurl",
                "{$DB_PREFIX}user.callbacknum",
                "{$DB_PREFIX}user.infoquality",
                "{$DB_PREFIX}user.updatetime",
                "{$DB_PREFIX}user.allocationtime",
                "{$DB_PREFIX}user.lastvisit",              
                "{$DB_PREFIX}user.nextvisit",
                "{$DB_PREFIX}user.visittime",
                "{$DB_PREFIX}user.createtime",
                "{$DB_PREFIX}user.attitude_id",
                "{$DB_PREFIX}user.introducermobile",
                "{$DB_PREFIX}user.learningtype",
                "{$DB_PREFIX}user.updateuser_id",
                "{$DB_PREFIX}user.createuser_id",
                "{$DB_PREFIX}user.reservetype",
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}channel.channel_id",               
                "A.system_user_id"                
            );
		$new_field=array();
		foreach($fields as $k=> $v)
		{
			if($v=='systemusername')
			{
				$new_field[]="{$DB_PREFIX}system_user.realname as systemusername";				
			}else if($v=='updatename'){
				$new_field[]="A.realname as updatename";				
			}else if($v=='createname'){
                $new_field[] = "C.realname as createname";
            }else if($v=='channelname'){
				$new_field[]="{$DB_PREFIX}channel.channelname";				
			}else if($v=='course'){
				$new_field[]="{$DB_PREFIX}user.course_id";				
			}else if(in_array("{$DB_PREFIX}user.".$v,$all_fields))
			{
				$new_field[]="{$DB_PREFIX}user.".$v;
			}			
		}
		
        $allUser['data'] = $this->field($new_field)
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER__.channel_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->join('LEFT JOIN __SYSTEM_USER__ C ON C.system_user_id=__USER__.createuser_id')
            ->order($_order)
            ->limit($limit)
            ->select();
				
        $allUser['count'] = $this->where($where)
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER__.channel_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->join('LEFT JOIN __SYSTEM_USER__ C ON C.system_user_id=__USER__.createuser_id')
            ->count();
			
        return $allUser;
    }
    /*
     * 客户总量
     * @author zgt
     * $request array('zoneIds'=>'1,2,3','status'=>'20')
     * @return array
     */
    public function getAllUserCount($where=null){
        $DB_PREFIX = C('DB_PREFIX');
        // 多级zone 拼接
        if(!empty($where['zoneIds'])){
            foreach($where['zoneIds'] as $k=>$v){
                $arr[] = $v['zone_id'];
            }
            $where[$DB_PREFIX.'user.zone_id'] = array('IN',$arr);
            unset($where['zoneIds']);
        }
        $allUser = $this
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER__.channel_id')
            ->join('LEFT JOIN __SYSTEM_USER__ A ON A.system_user_id=__USER__.updateuser_id')
            ->where($where)
            ->count();

        return $allUser;
    }
    /*
     * 获取用户详情
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function getUserInfo($user_id){
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user.user_id'] = $user_id;
        $allUser = $this
            ->field(array(
                "{$DB_PREFIX}user.user_id",
                "{$DB_PREFIX}user.username",
                "{$DB_PREFIX}user.tel",
                "{$DB_PREFIX}user.qq",
                "{$DB_PREFIX}user.realname",
                "{$DB_PREFIX}user.email",
                "{$DB_PREFIX}user.status",
                "{$DB_PREFIX}user.mark",
                "{$DB_PREFIX}user.learningtype",
                "{$DB_PREFIX}user.searchkey",
                "{$DB_PREFIX}user.interviewurl",
                "{$DB_PREFIX}user.infoquality",
                "{$DB_PREFIX}user.createtime",
                "{$DB_PREFIX}user.updatetime",
                "{$DB_PREFIX}user.allocationtime",
                "{$DB_PREFIX}user.lastvisit",
                "{$DB_PREFIX}user.nextvisit",
                "{$DB_PREFIX}user.visittime",
                "{$DB_PREFIX}user.attitude_id",
                "{$DB_PREFIX}user.introducermobile",
                "{$DB_PREFIX}user.course_id",
                "{$DB_PREFIX}user_info.remark",
                "{$DB_PREFIX}user_info.sex",
                "{$DB_PREFIX}user_info.birthday",
                "{$DB_PREFIX}user_info.identification",
                "{$DB_PREFIX}user_info.homeaddress",
                "{$DB_PREFIX}user_info.address",
                "{$DB_PREFIX}user_info.urgentname",
                "{$DB_PREFIX}user_info.urgentmobile",
                "{$DB_PREFIX}user_info.postcode",
                "{$DB_PREFIX}user_info.education_id",
                "{$DB_PREFIX}user_info.major",
                "{$DB_PREFIX}user_info.school",
                "{$DB_PREFIX}user_info.workyear",
                "{$DB_PREFIX}user_info.lastposition",
                "{$DB_PREFIX}user_info.lastcompany",
                "{$DB_PREFIX}user_info.lastsalary",
                "{$DB_PREFIX}user_info.wantposition",
                "{$DB_PREFIX}user_info.wantsalary",
                "{$DB_PREFIX}user_info.workstatus",
                "{$DB_PREFIX}user_info.englishstatus",
                "{$DB_PREFIX}user_info.englishlevel",
                "{$DB_PREFIX}user_info.computerlevel",
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.realname as system_realname",
                "{$DB_PREFIX}channel.channel_id",
                "{$DB_PREFIX}channel.channelname",
                "{$DB_PREFIX}fee.loan_institutions_id",
                "{$DB_PREFIX}fee.discount_cost",
                "{$DB_PREFIX}fee.coursecount",
                "{$DB_PREFIX}fee.paycount",
                "{$DB_PREFIX}fee.arrearage",
                "{$DB_PREFIX}fee.studytype",
                "{$DB_PREFIX}fee.course_id as fee_course_id",
                "{$DB_PREFIX}zone.zone_id",
                "{$DB_PREFIX}zone.name as zonename",
                "{$DB_PREFIX}course.course_id",
                "{$DB_PREFIX}course.coursename",
                "B.system_user_id as updateuser_id",
                "B.realname as updateuser_realname",
                "C.system_user_id as createuser_id",
                "C.realname as createuser_realname",
                "D.course_id as fee_course_id",
                "D.coursename as fee_coursename "
            ))
            ->join('LEFT JOIN __USER_INFO__ ON __USER_INFO__.user_id=__USER__.user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER__.system_user_id')
            ->join('LEFT JOIN __SYSTEM_USER__ B ON B.system_user_id=__USER__.updateuser_id')
            ->join('LEFT JOIN __SYSTEM_USER__ C ON C.system_user_id=__USER__.createuser_id')
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER__.channel_id')
            ->join('LEFT JOIN __FEE__ ON __FEE__.user_id=__USER__.user_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER__.zone_id')
            ->join('LEFT JOIN __COURSE__ ON __COURSE__.course_id=__USER__.course_id')
            ->join('LEFT JOIN __COURSE__ D ON D.course_id=__FEE__.course_id')
            ->where($where)
            ->find();         
        return $allUser;
    }

    
    /*
     * 客户回库
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function abandonUser($data,$system_user_id,$flag=null){
        $user_id = $data['user_id'];
        unset($data['user_id']);
        $data['status'] = 160;
        if(empty($flag) || $flag!=10){
            $data['lastvisit'] = time();
            $data['weight'] = array('exp','weight+1');
        }
        if($system_user_id!=0 && empty($flag)){
            $_info = $this->field('user_id,system_user_id')->where(array('user_id'=>array('IN',$user_id)))->select();
            foreach($_info as $k=>$v){
                if($system_user_id!=$v['system_user_id']) return array('code'=>1,'msg'=>'只有归属人才能放弃该客户信息');
            }
        }
        $result = M('user')->where(array('user_id'=>array('IN',$user_id)))->save($data);
        if($result!==false){
            $user_id = explode(',', $user_id);
            foreach($user_id as $k=>$v){
                $log['abandontime'] = time();
                $log['user_id'] = $v;
                $log['system_user_id'] = $system_user_id;
                $log['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
                $log['remark'] = !empty($data['remark'])?$data['remark']:'';
                $add_log[] = $log;
                $callbackDate['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
                if(!empty($flag) && $flag==10){
                    $callbackDate['remark'] = !empty($data['remark'])?'批量回库原因:'.$data['remark']:'';
                    $callbackDate['user_id'] = $v;
                    $callbackDate['system_user_id'] = $system_user_id;
                    $callbackDate['nexttime'] = time();
                    $callbackDate['callbacktime'] = time();
                    M('user_callback')->add($callbackDate);
                }else{
                    $callbackDate['remark'] = !empty($data['remark'])?'放弃原因:'.$data['remark']:'';
                    $callbackDate['nexttime'] = time();
                    $this->addUserCallback($callbackDate,$v,$system_user_id,1);
                }
            }
            M('user_abandon_logs')->addAll($add_log);
            return array('code'=>0,'msg'=>'操作成功');
        }else{
            return array('code'=>1,'msg'=>"客户ID为{$user_id}回收失败");
        }
    }

    /*
     * 客户回库-自动程序
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function abandonUserAuto($where){
        $data['status'] = 160;
        $data['lastvisit'] = time();
        $data['attitude_id'] = 0;
        $data['weight'] = array('exp','weight+1');
        $userList = M('user')->where()->select();
        $result = M('user')->where($where)->save($data); 
        if ($result === false) {
            return array('code'=>1,'msg'=>'客户回收失败');
        } 
        foreach ($userList as $key => $user) {
            $log['abandontime'] = time();
            $log['user_id'] = $user['user_id'];
            $log['system_user_id'] = 0;
            $log['attitude_id'] = !empty($data['attitude_id'])?$data['attitude_id']:0;
            $log['remark'] = !empty($data['remark'])?$data['remark']:'';
            $res = M('user_abandon_logs')->data($log)->add();
            if ($res < 0) {
                return array('code'=>2,'msg'=>'客户回收记录创建失败');
            }
        }

    }


    /*
     * 客户转出（分配）
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function allocationUser($user_id,$tosystem_user_id,$system_user_id,$flag=null){
        $data['mark'] = 1;
        $data['nextvisit'] = time();
        $data['attitude_id'] = 0;
        $data['callbacknum'] = 0;
        $data['lastvisit'] = time();
        $data['allocationtime'] = time();
        $data['system_user_id'] = $tosystem_user_id;

        $_info = $this->field('user_id,status,channel_id,system_user_id,realname,infoquality')->where(array('user_id'=>array('IN',$user_id)))->select();
        $_systemInfo = M('system_user')->where(array('system_user_id'=>$tosystem_user_id))->find();
        foreach($_info as $k=>$v){
            if($v['status']=='80' || $v['status']=='60') return array('code'=>1,'msg'=>'该客户状态不予许分配');
            if(!empty($flag) && $flag==3 && $v['status']==20){
                //带联系出库
                $data['status'] = 20;
                $data['updateuser_id'] = $tosystem_user_id;
                $data['updatetime'] = time();
            }else if($v['status']==160){
                $data['status'] = 20;
                $data['updateuser_id'] = $tosystem_user_id;
                $data['remark'] = null;
                $data['updatetime'] = time();
                $this->heiddenOldInfo($v['user_id']);
            }else if(empty($flag)){
                if($system_user_id!=$v['system_user_id'] && $system_user_id!=0) return array('code'=>1,'msg'=>'只有归属人才能分配该客户信息');
                if($tosystem_user_id==$v['system_user_id'] && $system_user_id!=0) return array('code'=>1,'msg'=>'无法将客户转给自己哦');
            }
            //审核转入
            if(!empty($flag) && $flag!=2){
                $userApply = D('UserApply')->field('user_id')->where(array('user_id'=>$v['user_id'],'status'=>10))->find();
                if(!empty($userApply)){
                    return array('code'=>1,'msg'=>'客户 '.$v['realname'].' 正在审核转入中，无法转出');
                }
            }
        }

        $data['zone_id'] = $_systemInfo['zone_id'];
        $where['user_id'] = array('IN',$user_id);
        $result = M('user')->where($where)->save($data);

        if($result!==false){
            $logs = $this->addUserAllocationLogs($_info,$tosystem_user_id);
            if ($logs === false) {
                return array('code'=>2,'msg'=>'数据分配失败');
            }else{
                $this->heiddenOldInfo($user_id);
                return array('code'=>0,'msg'=>'数据分配成功');
            }
        }else{
            return array('code'=>1,'msg'=>'数据分配失败');
        }
    }

    /**
     * 批量分配数据处理
     * nxx
     */
    public function addUserAllocationLogs($infos,$tosystem_user_id)
    {
        $logData['system_user_id'] = $tosystem_user_id;
        $logData['date'] = date('Ymd');
        $channelList = M("channel")->where("pid = 0")->field('channel_id')->select();
        foreach ($channelList as $key => $value) {
            $channelList[$key] = $value['channel_id'];
        }
        
        M('user_callback')->startTrans();
        foreach ($infos as $key => $info) {
            if (in_array($info['channel_id'], $channelList)) {
                $logData['channel_id'] = $info['channel_id'];
            }else{
                $channelInfo = M("channel")->where("channel_id = $info[channel_id]")->find();
                $logData['channel_id'] = $channelInfo['pid'];
            }
            //隐藏
            if($info['status']==160){
                M('user_callback')->where(array('user_id'=>$info['user_id']))->save(array('status'=>0));
            }
            $infoquality = $info['infoquality'];
            $logs = $this->getUserAllocationLogs($infoquality, $logData);
            if ($logs === false) {
                M('user_callback')->rollback;
                return false;
            }
        }
        M('user_callback')->commit();
        return true;
    }

    // /*
    //  * 客户转出 - 自动分配规则
    //  * @author zgt
    //  * @return array('code'=>'','msg'=>'','data'=>'');
    //  */
    // public function allocationUserAuto($where,$tosystem_user_id){
    //     $data['status'] = 20;
    //     $data['nextvisit'] = 0;
    //     $data['attitude_id'] = 0;
    //     $data['callbacknum'] = 0;
    //     $data['remark'] = null;
    //     $data['lastvisit'] = time();
    //     $data['system_user_id'] = $tosystem_user_id;
    //     $data['allocationtime'] = time();

    //     $_systemInfo = M('system_user')->where(array('system_user_id'=>$tosystem_user_id))->find();
    //     $data['zone_id'] = $_systemInfo['zone_id'];

    //     $result = M('user')->where($where)->save($data);
    //     if($result!==false){
    //         //清除旧数据
    //         $this->heiddenOldInfo($where['user_id']);
    //         $log['system_user_id'] = $tosystem_user_id;
    //         $log['channel_id'] = $where['channel_id'];

    //         $infoquality = $where['infoquality'];

    //         $channelList = M("channel")->where("pid = 0")->field('channel_id')->select();
    //         foreach ($channelList as $key => $value) {
    //             $channelList[$key] = $value['channel_id'];
    //         }
    //         if (in_array($where['channel_id'], $channelList)) {
    //             $log_data['channel_id'] = $where['channel_id'];
    //         }else{
    //             $channelInfo = M("channel")->where("channel_id = $where[channel_id]")->find();
    //             $log_data['channel_id'] = $channelInfo['pid'];
    //         }
    //         $log_data['date'] = date('Ymd');
    //         $logs = M('user_allocation_logs')->where("system_user_id = $tosystem_user_id and channel_id = $log_data[channel_id] and date = $log_data[date]")->find();
    //         if ($logs) {
    //             if ($infoquality == 1) {
    //                 $logs['infoqualitya'] = $logs['infoqualitya'] + 1;
    //             }
    //             if ($infoquality == 2) {
    //                 $logs['infoqualityb'] = $logs['infoqualityb'] + 1;
    //             }
    //             if ($infoquality == 3) {
    //                 $logs['infoqualityc'] = $logs['infoqualityc'] + 1;
    //             }
    //             if ($infoquality == 4) {
    //                 $logs['infoqualityd'] = $logs['infoqualityd'] + 1;
    //             }
    //             $result = M('user_allocation_logs')->where("system_user_id = $tosystem_user_id and channel_id = $log_data[channel_id] and date = $log_data[date]")->save($logs);
    //             if ($result === false) {
    //                 return array('code'=>1,'msg'=>"客户ID为{$where['user_id']}分配失败");
    //             }
    //         }else{
    //             if ($infoquality == 1) {
    //                 $log_data['infoqualitya'] = 1;
    //             }
    //             if ($infoquality == 2) {
    //                 $log_data['infoqualityb'] = 1;
    //             }
    //             if ($infoquality == 3) {
    //                 $log_data['infoqualityc'] = 1;
    //             }
    //             if ($infoquality == 4) {
    //                 $log_data['infoqualityd'] = 1;
    //             }
    //             $log_data['system_user_id'] = $tosystem_user_id;
    //             $result = M('user_allocation_logs')->add($log_data);
    //             if ($result === false) {
    //                 return array('code'=>1,'msg'=>"客户ID为{$where['user_id']}分配失败");
    //             }
    //         }
    //         return array('code'=>0,'msg'=>"分配成功");
    //     }

    //     return array('code'=>1,'msg'=>"分配失败");

    // }

    /*
     * 客户审核转入通过 更新状态
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function allocationUserAudit($data,$tosystem_user_id,$system_user_id)
    {
        $save_data['status'] = 20;
        $save_data['mark'] = 1;
        $save_data['promote_id'] = 0;
        $save_data['nextvisit'] = 0;
        $save_data['attitude_id'] = 0;
        $save_data['callbacknum'] = 0;
        $save_data['updatetime'] = time();
        $save_data['lastvisit'] = time();
        $save_data['allocationtime'] = time();
        $save_data['system_user_id'] = $tosystem_user_id;
        $save_data['updateuser_id'] = $system_user_id;

        $save_data['channel_id'] = !empty($data['channel_id'])?$data['channel_id']:0;
        $save_data['searchkey'] = !empty($data['searchkey'])?$data['searchkey']:0;
        $save_data['introducermobile'] = !empty($data['introducermobile'])?$data['introducermobile']:0;
        $save_data['interviewurl'] = !empty($data['interviewurl'])?$data['interviewurl']:0;
        if(!empty($data['introducermobile'])){
            $save_data['introducermobile'] = $data['introducermobile'];
        }
        if(!empty($data['interviewurl'])){
            $save_data['interviewurl'] = $data['interviewurl'];
            preg_match("/promote[=|\/]([0-9]*)/", $data['interviewurl'], $promote);
            if(!empty($promote[1])){
                $promoteInfo = M('promote')
                    ->field('channel_id')
                    ->where(array('promote_id'=>$promote[1]))
                    ->join("__PROID__ on __PROID__.proid=__PROMOTE__.proid")
                    ->find();
                if(!empty($promoteInfo['channel_id'])){
                    $save_data['channel_id'] = $promoteInfo['channel_id'];
                    $save_data['promote_id'] = $promote[1];
                }
            }
        }

        $_systemInfo = M('system_user')->where(array('system_user_id'=>$tosystem_user_id))->find();
        $save_data['zone_id'] = $_systemInfo['zone_id'];
        $where['user_id'] = $data['user_id'];
        $result = M('user')->where($where)->save($save_data);

        if($result!==false){
            return array('code'=>0,'msg'=>'客户分配成功');
        }else{
            return array('code'=>1,'msg'=>'数据分配失败');
        }
    }
    /*
     * 隐藏客户旧数据
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function heiddenOldInfo($user_id,$user_apply_id=null){
        $where['user_id'] = array('IN',$user_id);
        $data['status'] = 0;
        $reflag = M('user_callback')->where($where)->save($data);
        if($reflag!==false){
            if(!empty($user_apply_id)){
                $userInfo = M('user_info')->where(array('user_id' => $user_id ))->find();
                if(!empty($userInfo['remark'])){
                    $where2['user_apply_id'] = $user_apply_id;
                    $data2['remark'] = $userInfo['remark'];
                    M('user_apply')->where($where2)->save($data2);
                    $where3['user_id'] = array('IN',$user_id);
                    $data3['remark'] = '';
                    M('user_info')->where($where3)->save($data3);
                }

            }
            return true;
        }
        return fasle;
    }   

    /*
    * 回收规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function abandonList($where=null,$page='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user_abandon.status'] = 1;
        $result['data'] = M('user_abandon')
            ->field(
                "user_abandon_id,
                abandonname,
                {$DB_PREFIX}user_abandon.createtime,
                abandon_roles,
                {$DB_PREFIX}user_abandon.channel_id,
                channelname,
                week_text,
                {$DB_PREFIX}user_abandon.zone_id,
                name as zonename"
            )
            ->where($where)
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER_ABANDON__.channel_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER_ABANDON__.zone_id')
            ->order('user_abandon_id ASC')
            ->limit($page)
            ->select();
        $result['count'] = M('user_abandon')->where($where)->count();
        return $result;
    }
    /*
    * 添加回收规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function abandonAdd($data){
        return M('user_abandon')->data($data)->add();
    }

    /*
    * 修改回收规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function abandonEdit($data,$id){
        $where['user_abandon_id'] = $id;
        return M('user_abandon')->where($where)->save($data);
    }

    /*
    * 查看回收规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function abandonDetail($id){
        $where['user_abandon_id'] = $id;
        $result = M('user_abandon')->where($where)->find();
        if(!empty($result['abandon_roles'])) {
            $abandon_roles = explode(',', $result['abandon_roles']);
            $_abandon_roles = array();
            foreach($abandon_roles as $k=>$v){
                $_abandon_roles[] = $v;
            }
            $role_where['id']= array('like',$_abandon_roles,'or');
            $result['roles'] = M('role')->where($role_where)->select();
        }
        return $result;
    }

    /*
    * 分配规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function allocationList($where=null,$page='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user_allocation.status'] = 1;
        $result['data'] = M('user_allocation')
            ->field(
                "user_allocation_id,
                allocationname,
                allocationnum,
                allocation_roles,
                startnum,
                intervalnum,
                week_text,
                {$DB_PREFIX}user_allocation.sort,
                {$DB_PREFIX}user_allocation.channel_id,
                {$DB_PREFIX}user_allocation.zone_id,
                {$DB_PREFIX}user_allocation.createtime,
                channelname,
                weighttype,
                name"
            )
            ->where($where)
            ->join('LEFT JOIN __CHANNEL__ ON __CHANNEL__.channel_id=__USER_ALLOCATION__.channel_id')
            ->join('LEFT JOIN __ZONE__ ON __ZONE__.zone_id=__USER_ALLOCATION__.zone_id')
            ->order($DB_PREFIX.'user_allocation.user_allocation_id ASC')
            ->limit($page)
            ->select();
//        foreach ($result['data'] as $key => $value) {
//            if ($value['weighttype'] == 10) {
//                $value['weightname'] = '全部';
//            }elseif ($value['weighttype'] == 20) {
//                $value['weightname'] = '分配新量';
//            }elseif ($value['weighttype'] == 30) {
//                $value['weightname'] = '分配旧量';
//            }
//            $result['data'][$key] = $value;
//        }
        $result['count'] = M('user_allocation')->where($where)->count();
        return $result;
    }

    /*
    * 添加分配规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function allocationAdd($data){
        if(!empty($data['system_user_ids'])){
            $ids = explode(',',$data['system_user_ids']);
        }else{
            $roles = explode(',',$data['allocation_roles']);
            $_syswhere["zl_role_user.role_id"]=array('IN',$data['allocation_roles']);
            $_syswhere["A.usertype"]=array('neq',10);

            $zoneids = D('Zone')->getZoneIds($data['zone_id']);
            foreach($zoneids as $k => $v){
                $zids[] = $v['zone_id'];
            }
            $_syswhere["A.zone_id"]= array('IN', $zids);
            $idsArr = M('role_user')
                ->field(array('A.system_user_id'))
                ->join('__SYSTEM_USER__ A ON A.system_user_id=__ROLE_USER__.user_id')
                ->where($_syswhere)
                ->select();
            if(!empty($idsArr)){
                foreach ($idsArr as $k => $v) {
                    $ids[] = $v['system_user_id'];
                }
            }
        }


        if($ids){
            $result = M('user_allocation')->add($data);
            foreach ($ids as $v) {
                $addData[] = array('user_allocation_id' => $result,'system_user_id'=>$v );
            }
            $res = M('allocation_systemuser')->addAll($addData);
            if ($res <= 0) {
                return false;
            }
        }
        return $result;
    }

    /*
    * 修改分配规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function allocationEdit($data,$id){
        $where['user_allocation_id'] = $id;
        if($id){
            $result = M('user_allocation')->where($where)->save($data);
            if(!empty($data['system_user_ids'])){
                $ids = explode(',',$data['system_user_ids']);
            }else{
                if(!empty($data['allocation_roles'])){
                    $roles = explode(',',$data['allocation_roles']);
                    $_syswhere["zl_role_user.role_id"]=array('IN',$roles);
                }
                $_syswhere["A.usertype"]=array('neq',10);

                $zoneids = D('Zone')->getZoneIds($data['zone_id']);
                foreach($zoneids as $k => $v){
                    $zids[] = $v['zone_id'];
                }
                $_syswhere["A.zone_id"]= array('IN', $zids);
                $idsArr = M('role_user')
                    ->field(array('A.system_user_id'))
                    ->join('__SYSTEM_USER__ A ON A.system_user_id=__ROLE_USER__.user_id')
                    ->where($_syswhere)
                    ->select();
                if(!empty($idsArr)){
                    foreach ($idsArr as $k => $v) {
                        $ids[] = $v['system_user_id'];
                    }
                }
            }
            if($ids){
                M('allocation_systemuser')->where($where)->delete();
                foreach ($ids as $v) {
                    $addData[] = array('user_allocation_id' => $id,'system_user_id'=>$v);
                }
                $res = M('allocation_systemuser')->addAll($addData);
                if ($res <= 0) {
                    return false;
                }
            }
        }
        return $result;
    }

    /*
    * 修改分配规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function allocationDel($id){
        $where['user_allocation_id'] = $id;
        $data['status'] = 0;
        $result = M('user_allocation')->where($where)->save($data);
        if($result!==false){
            return true;
        }
        return false;
    }

    /*
    * 查看分配规则
    * @author zgt
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function allocationDetail($id){
        $where['user_allocation_id'] = $id;
        $result = M('user_allocation')->where($where)->find();
        if ($result['weighttype'] == 10) {
            $result['weightname'] = '全部';
        }elseif ($result['weighttype'] == 20) {
            $result['weightname'] = '分配新量';
        }elseif ($result['weighttype'] == 30) {
            $result['weightname'] = '分配旧量';
        }
        if(!empty($result['allocation_roles'])) {
            $allocation_roles = explode(',', $result['allocation_roles']);
            $_allocation_roles = array();
            foreach($allocation_roles as $k=>$v){
                $_allocation_roles[] = $v;
            }
            $role_where['id']= array('like',$_allocation_roles,'or');
            $result['roles'] = M('role')->where($role_where)->select();
            if($result['allocation_system_users']==20){
                $DB_PREFIX = C('DB_PREFIX');
                $result['systemuser'] = M('allocation_systemuser')
                    ->field("{$DB_PREFIX}system_user.system_user_id,{$DB_PREFIX}system_user.realname")
                    ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__ALLOCATION_SYSTEMUSER__.system_user_id')
                    ->where(array('user_allocation_id'=>$result['user_allocation_id']))
                    ->select();
            }
        }
        return $result;
    }

    /*
     * 添加客户
     * @author zgt
     * @return array('code'=>'','msg'=>'','data'=>'');
     */
    public function addUser($data,$zone_id,$system_user_id){
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        //$data['nextvisit'] = time();
        $data['allocationtime'] = time();
        $data['updatetime'] = time();
        $data['lastvisit'] = time();
        $data['updatetime'] = time();
        $data['createtime'] = time();
        $data['createip'] = get_client_ip();
        $data['system_user_id'] = $system_user_id;
        $data['updateuser_id'] = $system_user_id;
        $data['createuser_id'] = $system_user_id;
        $data['zone_id'] = $zone_id;
        if(empty($data['username']) && empty($data['tel']) && empty($data['qq'])) return array('code'=>1,'msg'=>'手机号码 / 固定电话 / QQ 至少填写一项');
        if(empty($data['infoquality'])) return array('code'=>2,'msg'=>'信息质量不能为空');
        if(empty($data['channel_id'])) return array('code'=>3,'msg'=>'所属渠道不能为空');
        if(empty($system_user_id)) return array('code'=>4,'msg'=>'缺少创建者');
        if(empty($zone_id)) return array('code'=>5,'msg'=>'缺少归属区域');

        if(!empty($data['username'])) {
            $data['username'] = trim($data['username']);
            $username = $data['username'];
            if($checkform->checkMobile($data['username'])!==false) {
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
            }else{
                return array('code'=>6,'msg'=>'手机号码格式有误','sign'=>'username');
            }
            $username_where['username'] = array(array('eq',$data['username']),array('eq',$username0),'OR');
            $_info = $this->where($username_where)->find();
            if(!empty($_info)) return array('code'=>7,'msg'=>'手机号码格式已被注册','sign'=>'username','user_id'=>"$_info[user_id]", 'system_user_id'=>"$_info[system_user_id]",'status'=>"$_info[status]");
            $reApi = phoneVest($username);
            if(!empty($reApi)) {
                $data['phonevest'] = $reApi['city'];
            }
        }
        if(!empty($data['tel'])) {
            $data['tel'] = trim($data['tel']);
            if(!$checkform->checkTel($data['tel'])) return array('code'=>8,'msg'=>'固定号码格式有误','sign'=>'tel');
            $_info = $this->where(array('tel'=>$data['tel']))->find();
            if(!empty($_info)) return array('code'=>9,'msg'=>'固定号码已被注册','sign'=>'tel','user_id'=>"$_info[user_id]", 'system_user_id'=>"$_info[system_user_id]",'status'=>"$_info[status]");
        }
        if(!empty($data['qq'])) {
            $data['qq'] = trim($data['qq']);
            if(!$checkform->checkInt($data['qq'])!==false) return array('code'=>10,'msg'=>'QQ号码格式有误','sign'=>'qq');
            $_info = $this->where(array('qq'=>$data['qq']))->find();
            if(!empty($_info)) return array('code'=>11,'msg'=>'QQ号码已被注册','sign'=>'qq','user_id'=>"$_info[user_id]", 'system_user_id'=>"$_info[system_user_id]",'status'=>"$_info[status]");
        }
        if(!empty($data['introducermobile'])) {
            if($checkform->checkMobile($data['introducermobile'])!==false) $data['introducermobile'] = encryptPhone($data['introducermobile'], C('PHONE_CODE_KEY'));
            else  return array('code'=>12,'msg'=>'转介绍人手机号码格式有误','sign'=>'introducermobile');
            $introducer = $this->where(array('username'=>$data['introducermobile']))->find();
            if(!empty($introducer['channel_id'])) $data['channel_id'] = $introducer['channel_id'];

        }
        if(!empty($data['interviewurl'])){
            $valueUrl = $data['interviewurl'];
            preg_match("/promote[=|\/]([0-9]*)/", $valueUrl, $promote);
            if(!empty($promote[1])){
                $promoteInfo = M('promote')
                    ->field('channel_id')
                    ->where(array('promote_id'=>$promote[1]))
                    ->join("__PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
                    ->find();
                if(!empty($promoteInfo['channel_id'])){
                    $data['channel_id'] = $promoteInfo['channel_id'];
                    $data['promote_id'] = $promote[1];
                }
            }
        }
        //启动事务
        $this->startTrans();
        $reUserId = $this->data($data)->add();
        if(!empty($reUserId)){
            $data_info['user_id'] = $reUserId;
            $data_info['remark'] = !empty($data['remark'])?$data['remark']:'';
            $data_info['birthday'] = !empty($data['birthday'])?$data['birthday']:'';
            $data_info['sex'] = !empty($data['sex'])?$data['sex']:'';
            $data_info['workyear'] = !empty($data['workyear'])?$data['workyear']:'';
            $data_info['school'] = !empty($data['school'])?$data['school']:'';
            $data_info['educationname'] = !empty($data['educationname'])?$data['educationname']:'';
            $data_info['major'] = !empty($data['major'])?$data['major']:'';
            $data_info['address'] = !empty($data['address'])?$data['address']:'';
            $data_info['lastposition'] = !empty($data['lastposition'])?$data['lastposition']:'';
            $data_info['lastcompany'] = !empty($data['lastcompany'])?$data['lastcompany']:'';
            $data_info['wantposition'] = !empty($data['wantposition'])?$data['wantposition']:'';
            $data_info['wantsalary'] = !empty($data['wantsalary'])?$data['wantsalary']:'';
            $data_info['workstatus'] = !empty($data['workstatus'])?$data['workstatus']:'';
            $data_info['englishstatus'] = !empty($data['englishstatus'])?$data['englishstatus']:'';
            $data_info['education_id'] = !empty($data['education_id'])?$data['education_id']:'';
            $reUserInfo = D('UserInfo')->data($data_info)->add();
        }
        $log_data['system_user_id'] = $system_user_id;
        $log_data['date'] = date('Ymd');
        $channelList = M("channel")->where("pid = 0")->field('channel_id')->select();
        foreach ($channelList as $key => $value) {
            $channelList[$key] = $value['channel_id'];
        }
        if (in_array($data['channel_id'], $channelList)) {
            $log_data['channel_id'] = $data['channel_id'];
        }else{
            $channelInfo = M("channel")->where("channel_id = ".$data['channel_id'])->find();
            $log_data['channel_id'] = $channelInfo['pid'];
        }
        $infoquality = $_info['infoquality'];
        $logs = $this->getUserAllocationLogs($infoquality, $log_data);

        if(!empty($reUserId) && !empty($reUserInfo) && !empty($logs)){
            $this->commit();
            return array('code'=>0,'msg'=>'客户添加成功','data'=>$reUserId);
        }else{
            $this->rollback();
            return array('code'=>12,'msg'=>'数据添加失败');
        }
    }

    public function isUsername($username){
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(!$checkform->checkMobile($username)) return false;
        $username = encryptPhone($username, C('PHONE_CODE_KEY'));
        $username_where['username'] = array('eq',$username);
        $_info = $this->where($username_where)->find();
        if(!empty($_info)) return array('code'=>7,'msg'=>'手机号码格式已被注册','sign'=>'username','user_id'=>"$_info[user_id]", 'system_user_id'=>"$_info[system_user_id]",'status'=>"$_info[status]");

    }

    /*
     * 更新、修改客户
     * @author zgt
     * @return false
     */
    public function editUser($data,$user_id,$system_user_id){
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(empty($system_user_id)) return array('code'=>1,'msg'=>'缺少创建者');
        $user = M('user')->where(array('user_id'=>$user_id))->find();
        if($user['system_user_id']!=$system_user_id) return array('code'=>1,'msg'=>'只有归属人才能修改该客户信息');
         if(!empty($data['tel'])){
             if( $user['tel']!=$data['tel'] ) {
                 $data['tel'] = trim($data['tel']);
                 if (!$checkform->checkTel($data['tel'])) return array('code' => 1, 'msg' => '固定号码格式有误', 'sign' => 'tel');
                 $istel = M('user')->where(array('tel' => $data['tel'], 'user_id' => array('NEQ', $user_id)))->find();
                 if (!empty($istel)) return array('code' => 1, 'msg' => '固定电话已存在');
             }else{
                 unset($data['tel']);
             }
         }
         if(!empty($data['qq'])){
             if( $user['qq']!=$data['qq'] ) {
                 $data['qq'] = trim($data['qq']);
                 if (!$checkform->checkInt($data['qq'])) return array('code' => 1, 'msg' => 'qq格式有误', 'sign' => 'qq');
                 $isqq = M('user')->where(array('qq' => $data['qq'], 'user_id' => array('NEQ', $user_id)))->find();
                 if (!empty($isqq)) return array('code' => 1, 'msg' => 'qq号码已存在');
                 if (empty($data['email']) && !empty($user['email'])) $data['email'] = $data['qq'] . '@qq.com';
             }else{
                 unset($data['qq']);
             }
         }
        if(!empty($data['username'])){
            if( $user['username']!=encryptPhone($data['username'], C('PHONE_CODE_KEY')) ){
                $data['username'] = trim($data['username']);
                $username = $data['username'];
                if(!$checkform->checkMobile($data['username'])) return array('code'=>1,'msg'=>'手机号码格式有误','sign'=>'username');
                $username0 = encryptPhone('0'.$data['username'], C('PHONE_CODE_KEY'));
                $data['username'] = encryptPhone($data['username'], C('PHONE_CODE_KEY'));
                $isusername = M('user')->where(array('username'=>array(array('eq',$data['username']),array('eq',$username0),'OR'),'user_id'=>array('NEQ',$user_id)))->find();
                if(!empty($isusername)) return array('code'=>1,'msg'=>'手机号码已存在');
                $reApi = phoneVest($username);
                if(!empty($reApi)) {
                    $data['phonevest'] = $reApi['city'];
                }else{
                    $data['phonevest'] = '';
                }
            }else{
                unset($data['username']);
            }
        }
        if(!empty($data['interviewurl'])){
            $valueUrl = $data['interviewurl'];
            preg_match("/promote[=|\/]([0-9]*)/", $valueUrl, $promote);
            if(!empty($promote[1])){
                $promoteInfo = M('promote')
                    ->field('channel_id')
                    ->where(array('promote_id'=>$promote[1]))
                    ->join("__PROID__ on __PROID__.proid_id=__PROMOTE__.proid_id")
                    ->find();
                if(!empty($promoteInfo['channel_id'])){
                    $data['channel_id'] = $promoteInfo['channel_id'];
                    $data['promote_id'] = $promote[1];
                }
            }
        }

        $result = M('user')->where(array('user_id'=>$user_id))->save($data);

        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>1,'msg'=>'数据修改失败');
    }

    /*
     * 更新、修改客户详情
     * @author zgt
     * @return false
     */
    public function editUserInfo($data,$user_id,$system_user_id){
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if(empty($system_user_id)) return array('code'=>1,'msg'=>'缺少创建者');
        $user = M('user')->where(array('user_id'=>$user_id))->find();
        if($user['system_user_id']!=$system_user_id) return array('code'=>1,'msg'=>'只有归属人才能修改该客户信息');
        $userInfo = M('user_info')->where(array('user_id'=>$user_id))->find();
        if(!empty($data['identification'])){
            if(!$checkform->checkIdcard($data['identification'])) return array('code'=>1,'msg'=>'身份证格式格式错误','sign'=>'identification');
        }
        if(!empty($data['email'])){
            if(!$checkform->isEmail($data['email'])) return array('code'=>1,'msg'=>'邮箱格式格式错误','sign'=>'email');
        }
        if(!empty($data['urgentmobile'])){
            if(!$checkform->checkMobile($data['urgentmobile'])) return array('code'=>1,'msg'=>'联系人号码格式错误','sign'=>'urgentmobile');
        }
        if(empty($userInfo)){
            $data['user_id'] = $user_id;
            $result = M('user_info')->data($data)->add();
        }else{
            $result = M('user_info')->where(array('user_id'=>$user_id))->save($data);
        }
        if($result!==false) return array('code'=>0,'msg'=>'修改成功');
        else return array('code'=>1,'msg'=>'数据修改失败');
    }

    /*
     * 获取回访记录
     * @author zgt
     * @return arr
     */
    public function getUserCallback($user_id,$system_user_id,$type=null){
        $DB_PREFIX = C('DB_PREFIX');
        $where[$DB_PREFIX.'user_callback.user_id'] = $user_id;
//        $where[$DB_PREFIX.'user_callback.system_user_id'] = $system_user_id;
        if(empty($type)){
            $where[$DB_PREFIX.'user_callback.status'] = 1;
        }
        return M('user_callback')
            ->field(array(
                "{$DB_PREFIX}user_callback.user_id",
                "{$DB_PREFIX}user_callback.system_user_id",
                "{$DB_PREFIX}user_callback.waytype",
                "{$DB_PREFIX}user_callback.attitude_id",
                "{$DB_PREFIX}user_callback.remark",
                "{$DB_PREFIX}user_callback.nexttime",
                "{$DB_PREFIX}user_callback.callbacktime",
                "{$DB_PREFIX}system_user.realname",
                "{$DB_PREFIX}system_user.face"
            ))
            ->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__USER_CALLBACK__.system_user_id')
            ->where($where)
            ->order($DB_PREFIX.'user_callback.callbacktime DESC')
            ->select();
    }

    /*
     * 添加回访记录
     * @author zgt
     * @return false
     */
    public function addUserCallback($data,$user_id,$system_user_id,$abandon=null){
        $data['user_id'] = $user_id;
        $data['system_user_id'] = $system_user_id;
        $data['callbacktime'] = time();
        $reflag = M('user_callback')->data($data)->add();
        if($reflag!==false){
            $user = M('user')->where(array('user_id'=>$user_id))->find();
            if($user['status']==20 || $user['status']==160){
                if(empty($abandon)){
                    $data_user['status'] = 30;
                }
                $data_user['attitude_id'] = $data['attitude_id'];
                $data_user['lastvisit'] = $data['callbacktime'];
                $data_user['nextvisit'] = $data['nexttime'];
                $data_user['callbacknum'] = array('exp','callbacknum+1');
                M('user')->where(array('user_id'=>$user_id))->save($data_user);
                return array('code'=>0,'msg'=>'添加成功,该客户状态已转换为“待跟进”状态');
            }else{
                $data_user['attitude_id'] = $data['attitude_id'];
                $data_user['lastvisit'] = $data['callbacktime'];
                $data_user['nextvisit'] = $data['nexttime'];
                $data_user['callbacknum'] = array('exp','callbacknum+1');
                M('user')->where(array('user_id'=>$user_id))->save($data_user);
                return array('code'=>0,'msg'=>'添加成功');
            }
        }else{
            return array('code'=>1,'msg'=>'数据添加失败');
        }
    }

    /*
     * 获取客户缴费/预报记录
     * @author zgt
     * @return array
    */
    public function getUserFeeLog($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        return M('fee_logs')
            ->field(array(
                "{$DB_PREFIX}fee_logs.user_id",
                "{$DB_PREFIX}fee_logs.fee_logs_id",
                "{$DB_PREFIX}fee_logs.paytype",
                "{$DB_PREFIX}fee_logs.pay",
                "{$DB_PREFIX}fee_logs.receivetype",
                "{$DB_PREFIX}fee_logs.receivetime",
                "{$DB_PREFIX}fee_logs.auditortime",
                "{$DB_PREFIX}fee_logs.auditor_status",
                "{$DB_PREFIX}system_user.realname"
            ))
            ->join('__SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__FEE_LOGS__.system_user_id')
            ->where($where)
            ->order($DB_PREFIX.'fee_logs.receivetime DESC')
            ->select();
    }

    /*
     * 添加缴费
     * @author zgt
     * @return array
    */
    public function addUserFee($data,$user_id)
    {
        $isFee = M('fee')->where(array('user_id'=>$user_id))->find();
        if(!empty($isFee)){
            $isFee['discount_cost'] = !empty($isFee['discount_cost'])?$isFee['discount_cost']:0;
            $data['arrearage'] = ($data['coursecount']-$isFee['paycount']-$data['discount_cost']);
            if($data['arrearage']==0) $data['pay_status'] = 2;
            else $data['pay_status'] = 1;
            $relag = M('fee')->where(array('user_id'=>$user_id))->save($data);
        }else{
            $data['discount_cost'] = !empty($data['discount_cost'])?$data['discount_cost']:0;
            $data['arrearage'] = ($data['coursecount']-$data['discount_cost']);
            $data['user_id'] = $user_id;
            if($data['arrearage']==0) $data['pay_status'] = 2;
            else $data['pay_status'] = 1;
            $relag = M('fee')->data($data)->add();
        }
        if($relag!==false){
            $data_user['status'] = 80;
            if(!empty($data['username']))$data_info['username'] = encryptPhone($data['username'],C('PHONE_CODE_KEY'));
            M('user')->where(array('user_id'=>$user_id))->save($data_user);
            if(!empty($data['urgentname']))$data_info['urgentname'] = $data['urgentname'];
            if(!empty($data['urgentmobile']))$data_info['urgentmobile'] = $data['urgentmobile'];
            if(!empty($data['identification']))$data_info['identification'] = $data['identification'];
            if(!empty($data['urgentname']))$data_info['urgentname'] = $data['urgentname'];
            if(!empty($data['area_id']))$data_info['area_id'] = $data['area_id'];
            M('user_info')->where(array('user_id'=>$user_id))->save($data_info);
            return true;
        }
        return false;
    }

    /*
     * 添加客户缴费/预报记录
     * @author zgt
     * @return array
    */
    public function addUserFeeLog($data,$system_user_id)
    {
        $feeInfo = M('fee')->where(array('user_id'=>$data['user_id']))->find();
        if(!empty($feeInfo)){
            $fee_save['pay_status'] = '0';
            M('fee')->where(array('user_id'=>$data['user_id']))->save($fee_save);
        }else{
            $fee_save['pay_status'] = '0';
            $fee_save['user_id'] = $data['user_id'];
            M('fee')->data($fee_save)->add();
        }
        $data['system_user_id'] = $system_user_id;
        $logs_flag = M('fee_logs')->data($data)->add();
        if($logs_flag!==false){
            $userType['status'] = 60;
            $userType['reservetype'] = 10; 
            M('user')->where(array('user_id'=>$data['user_id']))->save($userType);
            return true;
        }
        return false;
    }

    /*
     * 修改客户缴费/预报记录
     * @author zgt
     * @return array
    */
    public function editUserFeeLog($data,$fee_logs_id)
    {
        $where['fee_logs_id'] = $fee_logs_id;
        return M('fee_logs')->data($where)->save($data);
    }

    /*
    * 更新用户缴费表
    * @author nxx
    * @return bool
    */
    public function updataFee($data)
    {
        $feeInfo = $this->feeDb->where("user_id = $data[user_id]")->find();
        if ($feeInfo) {
            $data['paycount'] = $feeInfo['paycount']+$data['paycount'];
            $data['arrearage'] = $feeInfo['arrearage']-$data['arrearage'];
            $result = $this->feeDb->where("user_id = $data[user_id]")->save($data);
            if (!$result) {
                return false;
            }
        }else{
            $result = $this->feeDb->data($data)->add();
            if (!$result) {
                return false;
            }
        }

        return true;
    }


    /**
     * 获得指定条件的最新一条申请转入记录
     * @param $where
     * @param $field
     * @author cq
     */
    public function getApplyRecord($where, $field='*'){
        $DB_PREFIX = C('DB_PREFIX');
        return $this->userApplyDb->field($field)->where($where)->order("{$DB_PREFIX}user_apply.applytime DESC")->find();
    }


    /*
    * 申请转入
    * @author cq
    * @return array('code'=>'','msg'=>'','data'=>'');
    */
    public function applyTransfer($userApply)
    {
        if (!$userApply['user_id']) {
            return array('code'=>1,'msg'=>'请选择客户');
        }
        if (!$userApply['channel_id']) {
            return array('code'=>2,'msg'=>'请选择渠道');
        }
        if (!$userApply['applyreason']) {
            return array('code'=>3,'msg'=>'请填写申请理由');
        }

        $where['user_id'] = $userApply['user_id'];
        $userData = $this->getUser($where);
        if($userData['status'] != 160){
            return array('code'=>4,'msg'=>'该客户不处于回库状态，不能申请，请联系技术人员！');
        }
        $applyRecord = $this->getApplyRecord($where); //获取当前最新的一条申请记录      
        if (!empty($applyRecord)) {
            //已经处于申请中,在等待审核
            if($applyRecord['status'] == 10 && empty($applyRecord['auditortime'])){
                return array('code'=>5,'msg'=>'该客户已被申请,在审核中!');
            }else if($applyRecord['status'] == 20){ //20---审核失败,才可以重新申请
                $userApply['status'] = 10; //设置为10待审核状态
            }
        }
        //申请时间
        $userApply['applytime'] = time();
        //所属人id
        if(!empty($userData['system_user_id'])){
            $userApply['affiliation_system_user_id'] = $userData['system_user_id'];
        }
        //所属人渠道id
        if(!empty($userData['channel_id'])){
            $userApply['affiliation_channel_id'] = $userData['channel_id'];
        }

        //添加客户申请转入记录
        $user_apply_id = $this->userApplyDb->data($userApply)->add();
        if(!$user_apply_id){
            return array('code'=>7,'msg'=>'申请转入失败');
        }
        return array('code'=>0,'msg'=>'','data'=>$user_apply_id);
    }

    /*
    * 审核操作
    * @author nxx
    */
    public function auditInfo($apply,$applyInfo)
    {
        if($applyInfo['status']!=30){
            $updataApply = $this->userApplyDb->where($apply)->save($applyInfo);
            if ($updataApply === false) {
                return array('code'=>1,'msg'=>'审核转入操作失败');
            }
            return array('code'=>0,'msg'=>'审核转入操作成功');
        }else{
            //启动事物
            $this->startTrans();
            //更新转入表
            $updataApply = $this->userApplyDb->where($apply)->save($applyInfo);
            //更新user表
            $where = array('user_apply_id' => $applyInfo['user_apply_id']);
            $applyData = D('User')->getApplyRecord($where);
            if(!empty($applyData['introducermobile'])){
                $PHONE_CODE_KEY  = C('PHONE_CODE_KEY');
                $applyData['introducermobile'] =  encryptPhone($applyData['introducermobile'],$PHONE_CODE_KEY);
                $updateData['introducermobile'] = $applyData['introducermobile']; //转介绍人手机号
            }
            $updateData['user_id'] = $applyData['user_id'];
            $updateData['channel_id'] = $applyData['channel_id'];
            $updateData['searchkey'] = $applyData['searchword'];
            $updateData['interviewurl'] = $applyData['interviewurl'];
            $updataUser = $this->allocationUserAudit($updateData, $applyInfo['system_user_id'], $this->system_user_id);
            if($updataApply!==false && $updataUser['code']==0){
                    $this->heiddenOldInfo($applyInfo['user_id'],$applyInfo['user_apply_id']);
                    $this->commit();
                    return array('code'=>0,'msg'=>'您的审核结果是:通过');
            }else{
                $this->rollback();
                return array('code'=>1,'msg'=>'审核转入失败');
            }
        }
    }


    /*
    *获取申请转入列表
    *@author cq
    */
    public function getApplyList($where, $limit)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}user_apply.user_apply_id",
            "{$DB_PREFIX}user_apply.applytime",
            "{$DB_PREFIX}user_apply.status as applystatus",
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.status as userstatus" ,
            "{$DB_PREFIX}user.infoquality",
            "{$DB_PREFIX}user.channel_id",
            "{$DB_PREFIX}channel.channelname",
            "B.realname as apply_realname" ,
            "B.system_user_id as apply_system_user_id" ,
            "C.realname as auditor_realname" ,
            "C.system_user_id as auditor_system_user_id",
            "D.channelname as apply_channelname",
            "D.channel_id as apply_channel_id",
            "E.realname as affiliation_realname" ,
            "E.system_user_id as affiliation_system_user_id",
            "F.channelname as affiliation_channelname",
            "F.channel_id as affiliation_channel_id"
        );
        $applyList['data'] = $this->userApplyDb->field($field)
            ->join('__USER__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__  ON  __CHANNEL__.channel_id = __USER__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ B ON  B.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ C ON  C.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ D ON  D.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ E ON  E.system_user_id = __USER_APPLY__.affiliation_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ F ON  F.channel_id = __USER_APPLY__.affiliation_channel_id')
            ->where($where)
            ->limit($limit)
            ->order("{$DB_PREFIX}user_apply.applytime DESC")
            ->select();

        $applyList['count'] = $this->userApplyDb->field($field)
            ->join('__USER__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__  ON  __CHANNEL__.channel_id = __USER__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ B ON  B.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ C ON  C.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ D ON  D.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ E ON  E.system_user_id = __USER_APPLY__.affiliation_system_user_id')
            ->join('LEFT JOIN  __CHANNEL__ F ON  F.channel_id = __USER_APPLY__.affiliation_channel_id')
            ->where($where)->count();

        return $applyList;
    }


    /**删除满足条件的客户申请记录
     * @param $where 条件
     * @author cq
     */
    public  function  DelUserApply($where){
        $result1 = $this->userApplyDb->where($where)->delete(); //删除用户申请表
        return $result1;
    }


    /*
    * 获取申请客户转入详情
     * @where 查询条件
    * @author cq
    */
    public function getApplyUserDetails($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.email",
            "{$DB_PREFIX}user_apply.searchword",
            "{$DB_PREFIX}user_apply.interviewurl",
            "{$DB_PREFIX}user.status as userstatus" ,
            "{$DB_PREFIX}user.infoquality",
            "{$DB_PREFIX}channel.channelname",
            "{$DB_PREFIX}course.coursename",
            "{$DB_PREFIX}user_apply.applytime",
            "{$DB_PREFIX}user_apply.applyreason",
            "{$DB_PREFIX}user_apply.status applystatus",
            "{$DB_PREFIX}user_apply.auditortime",
            "{$DB_PREFIX}user_apply.auditorreason",
            "{$DB_PREFIX}user_apply.introducermobile", //转介绍人手机号
            "{$DB_PREFIX}system_user.system_user_id as apply_system_user_id" ,
            "{$DB_PREFIX}system_user.realname as apply_realname" ,
            "{$DB_PREFIX}system_user.face as apply_face" ,
            "{$DB_PREFIX}system_user.sex as apply_sex" ,
            "A.system_user_id as auditor_system_user_id" ,
            "A.realname as auditor_realname" ,
            "A.face as auditor_face" ,
            "A.sex as auditor_sex",
        );
        $applyDetails = $this->field($field)
            ->join('LEFT JOIN  __USER_APPLY__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__ ON  __CHANNEL__.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ A ON  A.system_user_id = __USER_APPLY__.auditor_system_user_id')
            ->join('LEFT JOIN  __COURSE__ ON  __COURSE__.course_id = __USER__.course_id')
            ->where($where)
            ->select();

        return $applyDetails;
    }

    /**
     * 获取审核界面的客户信息
     * @param $where
     * @return mixed
     */
    public function getAuditUserDetails($where)
    {
        $DB_PREFIX = C('DB_PREFIX');
        $field  = array(
            "{$DB_PREFIX}user.user_id",
            "{$DB_PREFIX}user.realname",
            "{$DB_PREFIX}user.username",
            "{$DB_PREFIX}user.qq",
            "{$DB_PREFIX}user.tel",
            "{$DB_PREFIX}user.email",
            "{$DB_PREFIX}user.status as userstatus" ,
            "{$DB_PREFIX}user.infoquality",
            "{$DB_PREFIX}channel.channelname",
            "{$DB_PREFIX}course.coursename",
            "{$DB_PREFIX}system_user.realname as apply_realname" , //申请者
            "{$DB_PREFIX}user_apply.user_apply_id", //记录id
            "{$DB_PREFIX}user_apply.system_user_id", //申请人ID
            "{$DB_PREFIX}user_apply.searchword",
            "{$DB_PREFIX}user_apply.interviewurl",
            "{$DB_PREFIX}user_apply.applytime", //申请时间
            "{$DB_PREFIX}user_apply.applyreason",//审核时间
            "{$DB_PREFIX}user_apply.introducermobile", //转介绍人手机号
            "{$DB_PREFIX}user_apply.to_system_user_id", //审核通过后所属员工ID
            "{$DB_PREFIX}user_apply.remark" //备注
        );
        $audioDetails = $this->field($field)
            ->join(' __USER_APPLY__ ON  __USER__.user_id = __USER_APPLY__.user_id')
            ->join('LEFT JOIN  __CHANNEL__ ON  __CHANNEL__.channel_id = __USER_APPLY__.channel_id')
            ->join('LEFT JOIN  __SYSTEM_USER__ ON  __SYSTEM_USER__.system_user_id = __USER_APPLY__.system_user_id')
            ->join('LEFT JOIN  __COURSE__ ON  __COURSE__.course_id = __USER__.course_id')
            ->where($where)
            ->select();

        return $audioDetails;
    }




    /**************************************************************
     * 多层zone_id OR 拼接字符串
     * @author zgt
     * @return str;
     ***************************************************************/
    private function userWhere_zone($zone_ids){
        $DB_PREFIX = C('DB_PREFIX');
        $zone_ids = explode(',',$zone_ids);
        $_where = "";
        foreach($zone_ids as $k=>$v){
            if( count($zone_ids)>2 ){
                if($k==0){
                    $_where .= " ({$DB_PREFIX}user.zone_id={$v} ";
                }elseif($k==(count($zone_ids)-1)){
                    $_where .= " OR {$DB_PREFIX}user.zone_id={$v}) ";
                }else{
                    $_where .= " OR {$DB_PREFIX}user.zone_id={$v} ";
                }
            }else{
                $_where .= " {$DB_PREFIX}user.zone_id={$v} ";
            }
        }
        return $_where;
    }



    /**
     * 添加设置模板
     * @author   Nxx
     */
    public function createSetPages($setPages)
    {
        $set['system_user_id'] = $setPages['system_user_id'];
        $set['pagesname'] = $setPages['pagesname'];
        $set['type'] = $setPages['type'];
        $set['status'] = 1;
        if ($setPages['type'] == 2) {
            $set['channel_id'] = $setPages['channel_id'];
        }
        $result = M('setpages')->where($set)->find();
        if ($result) {
            $error['code'] = 1;
            $error['msg'] = '模板名已存在';
            return $error;
        }
        $set['createtime'] = time();
        
        
        foreach ($setPages['sign'] as $key => $pages) {
            $arr[] = $pages[0];
        }
        if (count($arr)>count(array_unique($arr))) {
            $error['code'] = 2;
            $error['msg'] = '请不要重复选择表头';
            return $error;
        }
        $setpages_id = M('setpages')->data($set)->add();
        if (!$setpages_id) {
            $error['code'] = 3;
            $error['msg'] = '模板添加失败';
            return $error;
        }
        foreach ($setPages['sign'] as $key => $pages) {
            $page['pagehead'] = strtoupper($pages[0]);
            $page['headname'] = $pages[1];
            $page['setpages_id'] = $setpages_id;
            $result = M("setpageinfo")->data($page)->add();
            if (!$result) {
                $updat = $this->setPagesDb->where("setpages_id = $setpages_id")->delete();
                $error['code'] = 4;
                $error['msg'] = '模板表头设置失败';
                return $error;
            }
        }
        $error['code'] = 0;
        $error['msg'] = $setpages_id;
        return $error;
    }

    /**
     * 修改设置模板
     * @author   Nxx
     */
    public function editSetPages($setPages)
    {
        $delInfo = M("setpageinfo")->where("setpages_id = $setpages_id")->delete();        
        if ($delInfo == false) {
            return false;
        }
        
        foreach ($setPages['sign'] as $key => $pages) {
            $page['pagehead'] = $key+1;
            $page['headname'] = $pages[$key];
            $page['setpages_id'] = $setpages_id;
            $result = M("setpageinfo")->data($page)->add();
            if (!$result) {
                return false;
            }
        }
        return true;
    }


    /**
     * 统计信息质量
     * @author nxx
     */
    public function getInfoqualityCount($where)
    {
        //$channelList = M('channel')->where("pid = $where[channel_id]")->select();
        // $cidString = $where['channel_id'];
        // foreach ($channelList as $key => $channel) {
        //     $cidString = $cidString.", $channel[channel_id]";
        // }
        // $where['channel_id'] = array("IN", $cidString);
        $result = M("user_allocation_logs")->where($where)->find();
        return $result;
    }

    /**
     * 添加客户到访 并分配到员工
     * @author zgt
     */
    public function addUserVisit($user_id,$tosystem_user_id,$system_user_id){
        //是否存在忙线记录
        $engaged = M('system_user_engaged')->where(array('system_user_id'=>$tosystem_user_id))->find();
        if(!empty($engaged)){
            if($engaged['status']==1){
//                return array('code'=>1,'msg'=>'该员工处于忙线状态');
            }
        }else{
            $flag_add = M('system_user_engaged')->data(array('system_user_id'=>$tosystem_user_id,'status'=>2))->add();
            if($flag_add===false){
                return array('code'=>1,'msg'=>'新增忙线数据失败');
            }
        }
        //客户分配
        $userInfo = $this->field('system_user_id,status')->where(array('user_id'=>$user_id))->find();
        if($tosystem_user_id!=$userInfo['system_user_id'] || $userInfo['status']=='160'){
            $reflag_allocation = $this->allocationUser($user_id,$tosystem_user_id,$system_user_id,1);
            $callbackDate['attitude_id'] = 0;
            $callbackDate['remark'] = '前台操作: 客户于 '.date('Y-m-d',time()).' 上门到访！';
            $callbackDate['nexttime'] = time();
            $this->addUserCallback($callbackDate,$user_id,$system_user_id,1);
        }
        if($reflag_allocation['code']==0){
            $data_engaged['user_id'] = $user_id;
            $data_engaged['createtime'] = time();
            $data_engaged['status'] = 1;
            $data_engaged['isovertime'] = 1;
            $data_engaged['isget'] = 1;
            M('user')->startTrans();
            //添加记录
            $flag_engaged_save = M('system_user_engaged')->where(array('system_user_id'=>$tosystem_user_id))->save($data_engaged);
            //重置zone_id
            $system = M('system_user')->field('system_user_id,zone_id')->where(array('system_user_id'=>$tosystem_user_id))->find();
            $flag_user_save = M('user')->where(array('user_id'=>$user_id))->save(array('zone_id'=>$system['zone_id'],'visittime'=>time(),'lastvisit' => time()));
            if($flag_engaged_save!==fasle && $flag_user_save!=false){
                M('user')->commit();
                return  array('code'=>0,'msg'=>'分配到访客户成功');
            }else{
                M('user')->rollback();
                return  array('code'=>0,'msg'=>'分配操作失败');
            }
        }else{
            return  array('code'=>1,'msg'=>$reflag_allocation['msg']);
        }
    }

    //缴费优惠项目表
    public function getFeeDiscount(){
        if(F('Cache/Fee/discount')){
            $discount = F('Cache/Fee/discount');
        }else{
            $discount = M('discount')->where('type=1')->select();
            F('Cache/Fee/discount', $discount);
        }
        if(!empty($discount)){
            //数组分级
            $Arrayhelps = new \Org\Arrayhelps\Arrayhelps();
            $newAll = $Arrayhelps->createTree($discount, 0, 'discount_id', 'pid');
        }else{
            $newAll = $discount;
        }
        return $newAll;
    }
    //缴费优惠项目表详情
    public function getFeeDiscountInfo($discount_id){
        if(F('Cache/Fee/discount')){
            $discount_cahe = F('Cache/Fee/discount');
            foreach($discount_cahe as $k=>$v){
                if($v['discount_id']==$discount_id){
                    $discount = $v;
                }
            }
        }else{
            $discount = M('discount')->where(array('type'=>1,'discount_id'=>$discount_id))->find();
        }

        return $discount;
    }
    
    public function getMissUser(){
        
    }



    /**
     * 分配、新增数据时，记录分配质量信息
     * @author nxx
     */
    public function getUserAllocationLogs($infoquality, $log_data)
    {
        $logs = M('user_allocation_logs')->where($log_data)->find();
        if ($logs) {
            if ($infoquality == 1) {
                $logs['infoqualitya'] = $logs['infoqualitya'] + 1;
            }
            if ($infoquality == 2) {
                $logs['infoqualityb'] = $logs['infoqualityb'] + 1;
            }
            if ($infoquality == 3) {
                $logs['infoqualityc'] = $logs['infoqualityc'] + 1;
            }
            if ($infoquality == 4) {
                $logs['infoqualityd'] = $logs['infoqualityd'] + 1;
            }
            $result = M('user_allocation_logs')->where($log_data)->save($logs);
            if ($result !== false) {
                return true;
            }
        }else{
            if ($infoquality == 1) {
                $log_data['infoqualitya'] = 1;
            }
            if ($infoquality == 2) {
                $log_data['infoqualityb'] = 1;
            }
            if ($infoquality == 3) {
                $log_data['infoqualityc'] = 1;
            }
            if ($infoquality == 4) {
                $log_data['infoqualityd'] = 1;
            }
            $result = M('user_allocation_logs')->add($log_data);
            if ($result !== false) {
                return true;
            }
        }
        return false;

    }



}