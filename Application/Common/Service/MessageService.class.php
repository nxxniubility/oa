<?php
/*
|--------------------------------------------------------------------------
| 网易云信相关的接口
|--------------------------------------------------------------------------
| @author zgt
*/
namespace Common\Service;

use Common\Service\BaseService;

class MessageService extends BaseService
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
    | 获取消息列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMsgList($param)
    {
        $param = array_filter($param);
        if(!empty($param['isread'])){
            $where['isread'] = $param['isread'];
        }
        if(!empty($param['msgtype'])){
            $where['msgtype'] = $param['msgtype'];
        }
        $where['system_user_id'] = $this->system_user_id;
        $where['status'] = 1;
        $order = 'createtime desc';
        $join = '__MESSAGE_USER__ ON __MESSAGE_USER__.message_id = __MESSAGE__.message_id';
        $limit = !empty($param['page'])?$param['page']:'0,15';
        $result['data'] = D('Message')->getList($where, null, $order, $limit, $join);
        $result['count'] = D('Message')->getCount($where,$join);
        //获取当前未读总数
        $where['isread'] = 1;
        $result['unread_count'] = D('Message')->getCount($where,$join);
        // 补上转换
        if($result['data']){
            $result['data'] = $this->_addStatus($result['data']);
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取当前消息提示
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMsgHint()
    {
        //获取弹窗信息
        $where['system_user_id'] = $this->system_user_id;
        $where['isread'] = 1;
        $where['status'] = 1;
        $where['readtype'] = 1;
        $order = 'createtime desc';
        $join = '__MESSAGE_USER__ ON __MESSAGE_USER__.message_id = __MESSAGE__.message_id';
        $result['read_msg'] = D('Message')->getFind($where, null, $join, $order);
        // 补上转换
        if(!empty($result['read_msg'])){
            $result['read_msg'] = $this->_addStatus($result['read_msg']);
            $save['isread'] = 0;
            D('MessageUser')->where(array('message_id'=>$result['read_msg']['message_id'],'system_user_id'=>$where['system_user_id']))->save($save);
        }
        //获取当前未读总数
        $where['isread'] = 1;
        unset($where['readtype']);
        $result['unread_count'] = D('Message')->getCount($where,$join);
        if(!empty($result['data']) && $result['unread_count']>0){
            $result['unread_count'] = $result['unread_count']-1;
        }
        return array('code'=>0, 'data'=>$result);
    }

    /*
    |--------------------------------------------------------------------------
    | 获取消息详情
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function getMsgInfo($param)
    {
        if(empty($param['message_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $param = array_filter($param);
         //获取弹窗信息
         $where['system_user_id'] = $this->system_user_id;
         $where[$this->DB_PREFIX.'message.message_id'] = $param['message_id'];
         $join = '__MESSAGE_USER__ ON __MESSAGE_USER__.message_id = __MESSAGE__.message_id';
         $resuif = D('Message')->getFind($where, null, $join);
         // 补上转换
         if(!empty($resuif)){
             if($resuif['isread']==1){
                 $save['isread'] = 0;
                 D('MessageUser')->where(array('message_id'=>$resuif['message_id'],'system_user_id'=>$where['system_user_id']))->save($save);
             }
             $resuif = $this->_addStatus($resuif);
         }
        return array('code'=>0, 'data'=>$resuif);
    }
    /*
    |--------------------------------------------------------------------------
    | 获取当前消息提示
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delMsg($param)
    {
        if(empty($param['message_id'])) return array('code'=>300, 'msg'=>'参数异常');
        $param = array_filter($param);
        //获取弹窗信息
        $where['system_user_id'] = $this->system_user_id;
        $where['message_id'] = $param['message_id'];
        $save['status'] = 0;
        $result['read_msg'] = D('MessageUser')->where($where)->save($save);
        if($result!==false){
            return array('code'=>0, 'msg'=>'删除成功');
        }
        return array('code'=>100, 'msg'=>'删除失败');
    }

    /*
    |--------------------------------------------------------------------------
    | 添加消息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addMsg($param)
    {
        $param['createtime'] = time();
        $reid = D('Message')->addData($param);
        if($reid['code']==0){
            return array('code'=>0, 'data'=>$reid['data']);
        }else{
            return array('code'=>$reid['code'], 'msg'=>$reid['msg']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 添加消息
    |--------------------------------------------------------------------------
    | @author nxx
    */
    public function addMsgUser($param)
    {
        $reid = D('MessageUser')->addData($param);
        if($reid['code']==0){
            return array('code'=>0, 'msg'=>'添加成功');
        }else{
            return array('code'=>$reid['code'], 'msg'=>$reid['msg']);
        }
    }

    /**
     * 状态处理
     */
    protected function _addStatus($array=null)
    {
        if (empty($array[0])) {
            $arrStr[0] = $array;
        } else {
            $arrStr = $array;
        }
        $msg_type = C('FIELD_STATUS.MSG_TYPE');
        foreach($arrStr as $k=>$v){
            $arrStr[$k]['msgtype_name'] = $msg_type[$v['msgtype']];
            $arrStr[$k]['create_time'] = date('Y-m-d H:i', $v['createtime']);
            $arrStr[$k]['content'] = htmlspecialchars_decode($v['content']);
            if(!empty($v['system_user_id'])){
                $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
                $arrStr[$k]['system_realname'] = $systemUser['data']['realname'];
                $arrStr[$k]['system_sex'] = $systemUser['data']['sex'];
                $arrStr[$k]['system_face'] = $systemUser['data']['face'];
            }
            if(!empty($v['senduser_id']) && $v['senduser_id']!=0){
                $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['senduser_id']));
                $arrStr[$k]['senduser_realname'] = $systemUser['data']['realname'];
                $arrStr[$k]['senduser_sex'] = $systemUser['data']['sex'];
                $arrStr[$k]['senduser_face'] = $systemUser['data']['face'];
                $arrStr[$k]['senduser_role_names'] = $systemUser['data']['role_names'];
            }elseif($v['senduser_id']==0){
                $arrStr[$k]['senduser_realname'] = '系统消息';
            }
        }
        if(empty($array[0])){
            return $arrStr[0];
        }else{
            return $arrStr;
        }
    }

    /*
   |--------------------------------------------------------------------------
   | 发送消息
   |--------------------------------------------------------------------------
   | @author nxx
   */
    public function sendMsgs($request)
    {
        $request['senduser_id'] = $this->system_user_id;
        if ($request['msgtype'] == 20) {
            $request['readtype'] = 1;
        }else{
            $request['readtype'] = 0;
        }
        if (!$request['system_user_id']) {
            $where['role_id'] = array("IN", $request['role_id']);
            $systemUsers = D("Role", 'Service')->getRoleUser($where);
        }else{
            $systemUsers['data'] = explode(',', $request['system_user_id']);
            unset($request['system_user_id']);
        }
        $msgBackInfo = $this->addMsg($request);
        if ($msgBackInfo['code'] != 0) {
            return array('code'=>201, 'msg'=>"发送消息失败");
        }
        foreach ($systemUsers['data'] as $key => $systemUser) {
            $msgUser['system_user_id'] = $systemUser;
            $msgUser['message_id'] = $msgBackInfo['data'];
            $result = $this->addMsgUser($msgUser);
            if ($result['code'] != 0) {
                return array('code'=>$result['code'], 'msg'=>$result['msg']);
            }
        }
        return array('code'=>0, 'msg'=>'发送成功');
    }

}