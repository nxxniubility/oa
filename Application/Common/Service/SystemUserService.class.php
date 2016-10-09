<?php
/*
* 员工服务接口
* @author zgt
*
*/
namespace Common\Service;

use Common\Service\DataService;
use Common\Service\BaseService;

class SystemUserService extends BaseService
{
    //初始化
    protected $DB_PREFIX;

    public function _initialize()
    {
        parent::_initialize();
        $this->DB_PREFIX = C('DB_PREFIX');
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表
    |--------------------------------------------------------------------------
    | $type
    | @author zgt
    */
    public function getList($where, $order=null, $limit=null, $type=null)
    {
        //参数处理
        $where = $this->dispostWhere($where);
        //获取Model数据
        $field = array(
            "{$this->DB_PREFIX}system_user.system_user_id",
            "{$this->DB_PREFIX}system_user.username",
            "{$this->DB_PREFIX}system_user.realname",
            "{$this->DB_PREFIX}system_user.sign",
            "{$this->DB_PREFIX}system_user.face",
            "{$this->DB_PREFIX}system_user.email",
            "{$this->DB_PREFIX}system_user.sex",
            "{$this->DB_PREFIX}system_user.check_id",
            "{$this->DB_PREFIX}system_user.isuserinfo",
            "{$this->DB_PREFIX}system_user.usertype",
            "{$this->DB_PREFIX}system_user.logintime",
            "{$this->DB_PREFIX}system_user.loginip",
            "{$this->DB_PREFIX}zone.zone_id",
            "{$this->DB_PREFIX}zone.level as zonelevel",
            "{$this->DB_PREFIX}zone.name as zonename",
            "{$this->DB_PREFIX}system_user.createtime",
            "{$this->DB_PREFIX}system_user.createip",
            "{$this->DB_PREFIX}system_user_engaged.status as engaged_status"
        );
        $join = 'LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id
                 LEFT JOIN __SYSTEM_USER_ENGAGED__ on __SYSTEM_USER_ENGAGED__.system_user_id=__SYSTEM_USER__.system_user_id';
        if(!empty($order)){
            $order = "{$this->DB_PREFIX}system_user.".$order;
        }else{
            $order = "{$this->DB_PREFIX}system_user.sign";
        }
        $result = D('SystemUser')->getList($where, $order, $limit, $field, $join);
        //添加多职位
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['realname'] = $v['sign'].'-'.$v['realname'];
                $roles = $this->getSystemUserRole($v['system_user_id']);
                foreach($roles as $k2=>$v2){
                    if($k2==0) {
                        $roleNames = $v2['departmentname'].'/'.$v2['name'];
                        $roleName = $v2['name'];
                    }else{
                        $roleNames .= '，'.$v2['departmentname'].'/'.$v2['name'];
                        $roleName .= '，'.$v2['name'];
                    }
                }
                $result[$k]['role_names'] = $roleNames;
                $result[$k]['rolename'] = $roleName;
                $result[$k]['roles'] = $roles;
            }
        }

        // 是否添加分配渠道统计
        if(!empty($type)) $result = $this->getInfoqualityCount($result);
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取员工列表总数
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getCount($where)
    {
        $where = $this->dispostWhere($where);
        $join = 'LEFT JOIN __ZONE__ on __ZONE__.zone_id=__SYSTEM_USER__.zone_id';
        //获取Model数据
        $result = D('SystemUser')->getCount($where, $join);
        //返回数据与状态
        return array('code'=>'0', 'data'=>empty($result)?0:$result);
    }

    /*
	|--------------------------------------------------------------------------
	| 获取员工列表--缓存
	|--------------------------------------------------------------------------
	| $type 是否添加分配渠道统计
	| @author zgt
	*/
    public function getListCache($where, $order=null, $limit=null, $type=null)
    {
        $where = array_filter($where);
        $where['usertype'] = !empty($where['usertype'])?$where['usertype']:10;
        if(F('Cache/Personnel/system_list') && empty($where['realname'])){
            $systemUserAll = F('Cache/Personnel/system_list');
        }else{
            if(!empty($where['realname'])){
                $systemUserList = $this->getList($where);
            }else{
                $systemUserList = $this->getList();
            }
            $systemUserCount = $this->getCount();
            $systemUserAll['data'] = $systemUserList['data'];
            $systemUserAll['count'] = $systemUserCount['data'];
            if(empty($where['realname'])){
                F('Cache/Personnel/system_list',$systemUserAll);
            }
        }
        if(!empty($where['zone_id'])){
            $zoneIdArr = array();
            $zoneIdArr = $this->getZoneIds($where['zone_id']);
            foreach($systemUserAll['data'] as $k=>$v){
                if(!empty($where['zone_id']) && !in_array($v['zone_id'],$zoneIdArr)){
                    unset($systemUserAll['data'][$k]);
                }
                if(!empty($where['role_id'])){
                    $in_flag = false;
                    foreach($v['roles'] as $k2=>$v2){
                        if($v2['role_id']==$where['role_id'] ){
                            $in_flag = true;
                        }
                    }
                    if($in_flag===false) unset($systemUserAll['data'][$k]);
                }
            }
        }
        if(!empty($where['system_user_id'])){
            foreach($systemUserAll['data'] as $k=>$v){
                if($v['system_user_id']==$where['system_user_id']){
                    $systemUserAll['data'] = $v;
                }
            }
        }elseif($where['usertype'] == 10){
            foreach($systemUserAll['data'] as $k=>$v){
                if($v['usertype']==$where['usertype']){
                    unset($systemUserAll['data'][$k]);
                }
            }
            $systemUserAll['count'] = count($systemUserAll['data']);
        }
        if($limit!==null){
            $limit = explode(',',$limit);
            $systemUserAll['data'] = array_slice($systemUserAll['data'], $limit[0], $limit[1]);
        }
        //返回数据与状态
        return array('code'=>'0', 'data'=>$systemUserAll['data']);
    }

    /*
	|--------------------------------------------------------------------------
	| 获取员工 呼叫号码设置
	|--------------------------------------------------------------------------
	| @author zgt
	*/
    public function getCallNumber($where)
    {
        $where['status'] = 1;
        $result = D('CallNumber')->getList($where,'call_number_id,number,number_type,number_start');
        //返回数据与状态
        return array('code'=>'0', 'data'=>$result);
    }

    /*
	|--------------------------------------------------------------------------
	| 添加员工 呼叫号码设置
	|--------------------------------------------------------------------------
	| @author zgt
	*/
    public function addCallNumber($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if($data['number_type']==1){
            if(!$checkform->checkTel($data['number'])) return array('code'=>201,'msg'=>'固话码格式有误');
        }else{
            if(!$checkform->checkMobile($data['number'])) return array('code'=>202,'msg'=>'手机号码格式有误');
        }
        $result = D('CallNumber')->addData($data);
        //返回数据与状态
        return $result;
    }

    /*
	|--------------------------------------------------------------------------
	| 修改员工 呼叫号码设置
	|--------------------------------------------------------------------------
	| @author zgt
	*/
    public function editCallNumber($data)
    {
        //实例验证类
        $checkform = new \Org\Form\Checkform();
        if($data['number_type']==1){
            if(!$checkform->checkTel($data['number'])) return array('code'=>201,'msg'=>'固话码格式有误');
        }else{
            if(!$checkform->checkMobile($data['number'])) return array('code'=>202,'msg'=>'手机号码格式有误');
        }
        $result = D('CallNumber')->editData($data,$data['call_number_id']);
        //返回数据与状态
        return $result;
    }

    /*
	|--------------------------------------------------------------------------
	| 启用员工 呼叫号码设置
	|--------------------------------------------------------------------------
	| @author zgt
	*/
    public function startCallNumber($data)
    {
        D()->startTrans();
        $re_all = D('CallNumber')->where(array('system_user_id'=>$data['system_user_id']))->save(array('start'=>0));
        $re_edit = D('CallNumber')->editData($data,$data['call_number_id']);
        if($re_all!==false && $re_edit['code']==0){
            D()->commit();
            //返回数据与状态
            return array('code'=>'0', 'data'=>$re_edit['data']);
        }else{
            D()->rollback();
            //返回数据与状态
            return array('code'=>'100', 'msg'=>'数据操作失败');
        }
    }

    /**
     * 参数过滤
     * @author zgt
     */
    protected function dispostWhere($where)
    {
        $where = array_filter($where);
        foreach($where as $k=>$v){
            if($k=='role_id' && $v!=0){
                $ids = $this->getRoleIds($v);
                if(empty($ids)) $ids='0';
                $where["{$this->DB_PREFIX}system_user.system_user_id"] = array('IN', $ids);
            }elseif(!empty($v)){
                $where["{$this->DB_PREFIX}system_user.".$k] = $v;
            }
            unset($where[$k]);
        }
        if(!empty($where["{$this->DB_PREFIX}system_user.zone_id"])){
            $zoneIdArr = $this->getZoneIds($where["{$this->DB_PREFIX}system_user.zone_id"]);
            $where[$this->DB_PREFIX.'system_user.zone_id'] = array('IN',$zoneIdArr);
            unset($where['zone_id']);
        }

        return $where;
    }
    /**
     * 职位ID  获取对应人员ID
     * @author zgt
     */
    protected function getRoleIds($role_id)
    {
        $reList = D('RoleUser')
            ->field('user_id')
            ->group("user_id")->Distinct(true)
            ->where(array('role_id'=>$role_id))
            ->select();
        $systemUserArr = array();
        foreach($reList as $v){
            $systemUserArr[] = $v['user_id'];
        }
        return $systemUserArr;
    }

    /**
     * 区域ID 获取子集包括自己的集合
     * @author zgt
     */
    protected function getZoneIds($zone_id)
    {
        $zoneIds = D('Zone')->getZoneIds($zone_id);
        $zoneIdArr = array();
        foreach($zoneIds as $k=>$v){
            $zoneIdArr[] = $v['zone_id'];
        }
        return $zoneIdArr;
    }

    /*
     * userid=>获取员工权限
     * @author zgt
     * @return array
     */
    protected function getSystemUserRole($systemUserId){
        return D('RoleUser')
            ->field('name,role_id,departmentname')
            ->where(array('user_id'=>$systemUserId))
            ->join('__ROLE__ ON __ROLE_USER__.role_id=__ROLE__.id')
            ->join('LEFT JOIN __DEPARTMENT__ on __DEPARTMENT__.department_id=__ROLE__.department_id')
            ->select();
    }
}