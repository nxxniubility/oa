<?php
/*
|--------------------------------------------------------------------------
| 员工表
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：2016-04-12
| updatename：zgt
*/
namespace Common\Model;
use Common\Model\SystemModel;

class SystemUserModel extends SystemModel{

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getList($where=null, $order='createime DESC', $limit='0,30', $field='*', $join=null){
        return $this->field($field)->where($where)->join($join)->order($order)->limit($limit)->select();
    }


    /*
    |--------------------------------------------------------------------------
    | 获取员工总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where=null, $join=null){
        return $this->where($where)->join($join)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工基本信息
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getInfo($where,$field="*", $join=null){
        return $this->field($field)->join($join)->where($where)->find();
    }


    public function getSystemUser($where,$field="*", $join=null){
        return $this->field($field)->join($join)->where($where)->find();
    }

    /*
    |--------------------------------------------------------------------------
    | 获取单条记录
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getFind($where=null, $field='*', $join=null)
    {
        return $this->field($field)->where($where)->join($join)->find();
    }

    /*
     * 添加登录日志
     * @author zgt
     * @return false
     */
    public function addSystemUserLogs($systemUserId){
        $add_log['logintime'] = time();
        $add_log['loginip'] = get_client_ip();
        $addflag = $this->where("system_user_id = $systemUserId")->save($add_log);
        if($addflag!==false){
            $add_log['system_user_id'] = $systemUserId;
            return M('system_user_logs')->data($add_log)->add();
        }

    }

    /*
     * userid=>获取员工权限
     * @author zgt
     * @return array
     */
    public function getSystemUserRole($systemUserId){
        return M('role_user')
            ->field('name,role_id,departmentname')
            ->where(array('user_id'=>$systemUserId))
            ->join('__ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->order('role_id asc')
            ->select();
    }


    /*
     * userid=>获取员工忙线状态
     * @author zgt
     * @return array
     */
    public function getSystemEngagedStatus($systemUserId){
        return M('system_user_engaged')->where(array('system_user_id'=>$systemUserId))->find();
    }
    /*
     * userid=>修改员工忙线状态
     * @author zgt
     * @return array
     */
    public function editSystemEngagedStatus($data,$systemUserId){
        return M('system_user_engaged')->where(array('system_user_id'=>$systemUserId))->save($data);
    }

    /*
     * userid=>查看员工详情（基础信息+档案信息）
     * @author zgt
     * @return array
     */
    public function getSystemUserInfo($system_user_id){
        $DB_PREFIX = C('DB_PREFIX');
        $where[C('DB_PREFIX').'system_user.system_user_id'] = $system_user_id;
        $where[C('DB_PREFIX').'system_user.status'] = 1;
        $systemUserInfo = $this
            ->field(array(
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.username",
                "{$DB_PREFIX}system_user.realname",
                "{$DB_PREFIX}system_user.nickname",
                "{$DB_PREFIX}system_user.face",
                "{$DB_PREFIX}system_user.email",
                "{$DB_PREFIX}system_user.sex",
                "{$DB_PREFIX}system_user.check_id",
                "{$DB_PREFIX}system_user.isuserinfo",
                "{$DB_PREFIX}system_user.usertype",
                "{$DB_PREFIX}system_user_info.plivatemail",
                "{$DB_PREFIX}system_user_info.birthday",
                "{$DB_PREFIX}system_user_info.nativeplace",
                "{$DB_PREFIX}system_user_info.school",
                "{$DB_PREFIX}system_user_info.marital",
                "{$DB_PREFIX}system_user_info.province_id",
                "{$DB_PREFIX}system_user_info.city_id",
                "{$DB_PREFIX}system_user_info.area_id",
                "{$DB_PREFIX}system_user_info.address",
                "{$DB_PREFIX}system_user_info.education_id",
                "{$DB_PREFIX}system_user_info.school",
                "{$DB_PREFIX}system_user_info.majorname",
                "{$DB_PREFIX}system_user_info.entrytime",
                "{$DB_PREFIX}system_user_info.straightime",
                "{$DB_PREFIX}system_user_info.socialsecurity",
                "{$DB_PREFIX}system_user_info.providentfund",
                "{$DB_PREFIX}system_user_info.remark",
                "{$DB_PREFIX}system_user_info.worktime",
                "{$DB_PREFIX}system_user_info.workintroduction",
                "{$DB_PREFIX}system_user_info.status as info_status",
                "{$DB_PREFIX}zone.zone_id",
                "{$DB_PREFIX}zone.level as zonelevel",
                "{$DB_PREFIX}zone.name as zonename",
                "{$DB_PREFIX}department.department_id",
                "{$DB_PREFIX}department.departmentname",
                "{$DB_PREFIX}role.id as role_id",
                "{$DB_PREFIX}role.name as rolename",
                "{$DB_PREFIX}system_user.createtime",
                "{$DB_PREFIX}system_user.createip",
                "{$DB_PREFIX}system_user_engaged.status as engaged_status"
            ))
            ->where($where)
            ->join('LEFT JOIN __SYSTEM_USER_INFO__ ON __SYSTEM_USER_INFO__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->join('LEFT JOIN __ROLE_USER__ ON __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
            ->join('LEFT JOIN __ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->find();

        //添加多职位
        $roles = $this->getSystemUserRole($systemUserInfo['system_user_id']);
        $systemUserInfo['user_roles'] = $roles;
        $roleNames = '';
        foreach($roles as $k2=>$v2){
            if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
            else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
        }
        $systemUserInfo['role_names'] = $roleNames;

        return $systemUserInfo;
    }

    /*
     * userid=>获取员工列表
     * @author zgt
     * @return array
     */
    public function getSystemUserAll($where=null,$order=null,$limit='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $order = !empty($order)?$order:$DB_PREFIX.'system_user.system_user_id DESC';
        if(!empty($where['zone_id'])){
            $zoneIds = D('Zone')->getZoneIds($where['zone_id']);
            $zoneIdArr = array();
            foreach($zoneIds as $k=>$v){
                $zoneIdArr[] = $v['zone_id'];
            }
            $where[$DB_PREFIX.'system_user.zone_id'] = array('IN',$zoneIdArr);
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
            $where[$DB_PREFIX.'system_user.system_user_id'] = array('IN', $systemUser);
        }

        unset($where['zone_id']);
        unset($where['role_id']);
        $redata['data'] =
            $this
            ->field(array(
                "{$DB_PREFIX}system_user.system_user_id",
                "{$DB_PREFIX}system_user.username",
                "{$DB_PREFIX}system_user.realname",
                "{$DB_PREFIX}system_user.face",
                "{$DB_PREFIX}system_user.email",
                "{$DB_PREFIX}system_user.sex",
                "{$DB_PREFIX}system_user.check_id",
                "{$DB_PREFIX}system_user.isuserinfo",
                "{$DB_PREFIX}system_user.usertype",
                "{$DB_PREFIX}system_user.logintime",
                "{$DB_PREFIX}system_user.loginip",
                "{$DB_PREFIX}zone.zone_id",
                "{$DB_PREFIX}zone.level as zonelevel",
                "{$DB_PREFIX}zone.name as zonename",
                "{$DB_PREFIX}system_user.createtime",
                "{$DB_PREFIX}system_user.createip",
                "{$DB_PREFIX}system_user_engaged.status as engaged_status"
            ))
            ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
            ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->select();
        //统计总数
        if(!empty($redata['data'])){
            $redata['count'] = $this
                ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->where($where)
                ->count("{$DB_PREFIX}system_user.system_user_id");

            //添加多职位
            foreach($redata['data'] as $k=>$v){
                $roles = $this->getSystemUserRole($v['system_user_id']);
                $roleNames = '';
                foreach($roles as $k2=>$v2){
                    if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
                    else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                }
                $redata['data'][$k]['role_names'] = $roleNames;
            }
        }else{
            $redata['count'] = 0;
        }
        return $redata;
    }

    /*
     * userid=>获取员工列表
     * @author zgt
     * @return array
     */
    public function getSystemUserAllCache($where=null,$order=null,$limit='1,100'){
        $DB_PREFIX = C('DB_PREFIX');
        if(!empty($where['zoneIds'])){
            foreach($where['zoneIds'] as $k=>$v){
                $arr[] = $v['zone_id'];
            }
            $arr[]=0;
            $where[$DB_PREFIX.'system_user.zone_id'] = array('IN',$arr);
            unset($where['zoneIds']);
        }
        if (F('Cache/Personnel/system_user')) {
            $redata = F('Cache/Personnel/system_user');
        }else{
            $whereSystem[C('DB_PREFIX').'system_user.usertype'] = array('neq',10);
            $redata['data'] =
                $this
                    ->field(array(
                        "{$DB_PREFIX}system_user.system_user_id",
                        "{$DB_PREFIX}system_user.username",
                        "{$DB_PREFIX}system_user.realname",
                        "{$DB_PREFIX}system_user.face",
                        "{$DB_PREFIX}system_user.email",
                        "{$DB_PREFIX}system_user.sex",
                        "{$DB_PREFIX}system_user.check_id",
                        "{$DB_PREFIX}system_user.isuserinfo",
                        "{$DB_PREFIX}system_user.usertype",
                        "{$DB_PREFIX}system_user.logintime",
                        "{$DB_PREFIX}system_user.loginip",
                        "{$DB_PREFIX}zone.zone_id",
                        "{$DB_PREFIX}zone.level as zonelevel",
                        "{$DB_PREFIX}zone.name as zonename",
                        "{$DB_PREFIX}department.department_id",
                        "{$DB_PREFIX}department.departmentname",
                        "{$DB_PREFIX}role.id as role_id",
                        "{$DB_PREFIX}role.name as rolename",
                        "{$DB_PREFIX}system_user.createtime",
                        "{$DB_PREFIX}system_user.createip",
                        "{$DB_PREFIX}system_user_engaged.status as engaged_status"
                    ))
                    ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                    ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                    ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                    ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                    ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                    ->group("{$DB_PREFIX}system_user.system_user_id")->Distinct(true)
                    ->where($whereSystem)
                    ->select();
            //统计总数
            if(!empty($redata['data'])){
                $redata['count'] = $this
                    ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                    ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                    ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                    ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                    ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                    ->where($whereSystem)
                    ->count("{$DB_PREFIX}system_user.system_user_id");

                //添加多职位
                foreach($redata['data'] as $k=>$v){
                    $roles = $this->getSystemUserRole($v['system_user_id']);
                    $roleNames = '';
                    foreach($roles as $k2=>$v2){
                        if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
                        else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                    }
                    $redata['data'][$k]['role_names'] = $roleNames;
                }
            }
//            F('Cache/Personnel/system_user',$redata);
        }
        $redata = $this->disposeArray($redata, null, '1,100', $where);
        return $redata;
    }

    /*
     * userid=>添加员工
     * @author zgt
     * @return false
     */
        public function addSystemUser($data){
            $data['createtime'] = time();
            $data['createip'] = get_client_ip();
        $result = $this->field('realname,username,email,sex,check_id,zone_id,usertype,createtime,createip')->data($data)->add();
        if( !empty($result) ){
            if(!empty($data['role_id'])){
                $where_role['user_id'] = $result;
                $add_role = explode(',',$data['role_id']);
                foreach($add_role as $k=>$v){
                    $where_role['role_id'] = $v;
                    $reflag = M('role_user')->data($where_role)->add();
                }
            }
            $data['system_user_id'] = $result;
            $reflag = M('system_user_info')->field('system_user_id,entrytime,straightime')->data($data)->add();
            $flag_add = M('system_user_engaged')->data(array('system_user_id'=>$result,'status'=>2))->add();
        }
        F('Cache/Personnel/system_user',null);
        return $reflag;
    }

    /*
     * userid=>修改员工
     * @author zgt
     * @return false
     */
    public function editSystemUser($data,$system_user_id){
        //启动事务
        $this->startTrans();
        $result = $this->field('realname,username,face,password,check_id,email,emailpassword,sex,zone_id,usertype,createtime,status')->where("system_user_id = $system_user_id")->save($data);

        if( !empty($data['role_id']) ) {
            $where_role['user_id'] = $system_user_id;
            M('role_user')->where($where_role)->delete();
            $edit_role = explode(',',$data['role_id']);
            if(!empty($edit_role)){
                foreach($edit_role as $k=>$v){
                    $where_role['role_id'] = $v;
                    $reflag = M('role_user')->data($where_role)->add();
                }
                $flag_userINfo = M('system_user_info')->field('entrytime,straightime')->where(array('system_user_id'=>$system_user_id))->save($data);
                if($flag_userINfo!==false && $result!==false){
                    $this->commit();
                    return true;
                }else{
                    $this->rollback();
                    return false;
                }
            }
        }
        if($result!==false){
            F('Cache/Personnel/system_user',null);
            $this->commit();
            return true;
        }else{
            $this->rollback();
            return false;
        }
    }

    /*
     * 清空员工 所属客户
     * @author zgt
     * @return false
     */
    protected function emptyUser($system_user_id){
        $where['system_user_id'] = $system_user_id;
        $where['status'] = array('neq',160);
        $data['status'] = 160;
        $reflag = M('user')->where($where)->save($data);
        if( $reflag!==false ) return true;
        return false;
    }

    /*
     * 离职员工员工 回库带跟进与待联系客户
     * @author zgt
     * @return false
     */
    public function userAbandon($system_user_id){
        $where['system_user_id'] = $system_user_id;
        $where['status'] = array('IN','20,30');
        $data['status'] = 160;
        $reflag = M('user')->where($where)->save($data);
        if( $reflag!==false ) return true;
        return false;
    }

    /*
     * userid=>添加员工档案
     * @author zgt
     * @return false
     */
    public function addSystemUserInfo($data,$system_user_id){
        $data['creatime'] = time();
        $data['system_user_id'] = $system_user_id;
        $sex = isset($data['sex'])?$data['sex']:null;
        $check_id = isset($data['check_id'])?$data['check_id']:null;
        $usertype = isset($data['usertype'])?$data['usertype']:null;
        unset($data['sex']);unset($data['check_id']);unset($data['usertype']);
        //启动事务
        $this->startTrans();
        $result = M('system_user_info')->where(array('system_user_id'=>$system_user_id))->save($data);
        $system_user['isuserinfo'] = 1;
        if(!empty($sex)) $system_user['sex'] = $sex;
        if(!empty($check_id)) $system_user['check_id'] = $check_id;
        if(!empty($usertype)) $system_user['usertype'] = $usertype;
        $reflag = $this->where('system_user_id='.$system_user_id)->save($system_user);
        if($reflag!==false && $result!==false){
            $this->commit();
            return true;
        }else{
            $this->rollback();
            return false;
        }
    }

    /*
     * userid=>修改员工档案
     * @author zgt
     * @return false
     */
    public function editSystemUserInfo($data,$system_user_id){
        $sex = isset($data['sex'])?$data['sex']:1;
        $check_id = isset($data['check_id'])?$data['check_id']:null;
        $usertype = isset($data['usertype'])?$data['usertype']:null;
        unset($data['sex']);unset($data['check_id']);unset($data['usertype']);
        //启动事务
        $this->startTrans();
        $result = M('system_user_info')->where('system_user_id='.$system_user_id)->save($data);
        if(!empty($sex)) $system_user['sex'] = $sex;
        if(!empty($check_id)) $system_user['check_id'] = $check_id;
        if(!empty($usertype)) $system_user['usertype'] = $usertype;
        $reflag = $this->where('system_user_id='.$system_user_id)->save($system_user);
        if($reflag!==false && $result!==false){
            $this->commit();
            return true;
        }else{
            $this->rollback();
            return false;
        }
    }

    /*
     * userid=>获取员工列表
     * @author zgt
     * @return array
     */
    public function getSystemUserVisit($where=null,$order=null,$limit='0,10'){
        $DB_PREFIX = C('DB_PREFIX');
        $order = !empty($order)?$order:$DB_PREFIX.'system_user.system_user_id DESC';
        if(!empty($where['zoneIds'])){
            foreach($where['zoneIds'] as $k=>$v){
                $arr[] = $v['zone_id'];
            }
            $arr[]=0;
            $where[$DB_PREFIX.'system_user.zone_id'] = array('IN',$arr);
//            $where[$DB_PREFIX.'role.status'] = 1;
            unset($where['zoneIds']);
        }

        $redata['data'] =
            $this
                ->field(array(
                    "{$DB_PREFIX}system_user.system_user_id",
                    "{$DB_PREFIX}system_user.username",
                    "{$DB_PREFIX}system_user.realname",
                    "{$DB_PREFIX}system_user.face",
                    "{$DB_PREFIX}system_user.email",
                    "{$DB_PREFIX}system_user.sex",
                    "{$DB_PREFIX}system_user.check_id",
                    "{$DB_PREFIX}system_user.isuserinfo",
                    "{$DB_PREFIX}system_user.usertype",
                    "{$DB_PREFIX}system_user.logintime",
                    "{$DB_PREFIX}system_user.loginip",
                    "{$DB_PREFIX}zone.zone_id",
                    "{$DB_PREFIX}zone.level as zonelevel",
                    "{$DB_PREFIX}zone.name as zonename",
                    "{$DB_PREFIX}department.department_id",
                    "{$DB_PREFIX}department.departmentname",
                    "{$DB_PREFIX}role.id as role_id",
                    "{$DB_PREFIX}role.name as rolename",
                    "{$DB_PREFIX}system_user.createtime",
                    "{$DB_PREFIX}system_user.createip",
                    "{$DB_PREFIX}system_user_engaged.status as engaged_status",
                    "{$DB_PREFIX}user_visit_logs.visitnum"
                ))
                ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                ->join('LEFT JOIN __USER_VISIT_LOGS__ on __USER_VISIT_LOGS__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->group("{$DB_PREFIX}system_user.system_user_id")->Distinct(true)
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->select();
        //统计总数
        if(!empty($redata['data'])){
            $redata['count'] = $this
                ->join('LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id')
                ->join('LEFT JOIN __ROLE_USER__ on __ROLE_USER__.user_id=__SYSTEM_USER__.system_user_id')
                ->join('LEFT JOIN __ROLE__ on __ROLE__.id=__ROLE_USER__.role_id')
                ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
                ->join('LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id')
                ->where($where)
                ->count("{$DB_PREFIX}system_user.system_user_id");

            //添加多职位
            foreach($redata['data'] as $k=>$v){
                $roles = $this->getSystemUserRole($v['system_user_id']);
                $roleNames = '';
                foreach($roles as $k2=>$v2){
                    if($k2==0) $roleNames .= $v2['departmentname'].'/'.$v2['name'];
                    else $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                }
                $redata['data'][$k]['role_names'] = $roleNames;
            }
        }else{
            $redata['count'] = 0;
        }
        return $redata;
    }

}
