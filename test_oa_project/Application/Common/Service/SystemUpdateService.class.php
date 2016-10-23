<?php
/*
* 系统更新接口
* @author zgt
*
*/
namespace Common\Service;
use Common\Service\BaseService;

class SystemUpdateService extends BaseService
{
    //初始化
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   |--------------------------------------------------------------------------
   | 获取系统更新信息
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateList($param)
    {
        $param = array_filter($param);
        $param['page'] = !empty($param['page'])?$param['page']:null;
        if( F('Cache/systemUpdate') ) {
            $list = F('Cache/systemUpdate');
        }else{
            $list = $this->_getSystemUpdateList();
            F('Cache/systemUpdate', $list);
        }
        $list = $this->disposeArray($list,  $param['order'], $param['page'],  $param['where']);
        return array('code'=>'0', 'data'=>$list);
    }

    /*
    |--------------------------------------------------------------------------
    | SystemUpdate 系统更新添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function addSystemUpdate($param)
    {
        //必须参数
        $param = array_filter($param);
        $param['system_user_id'] = $this->system_user_id;
        $param['createtime'] = time();
        if(empty($param['uptitle'])) return array('code'=>300,'msg'=>'缺少系统更新名称');
        $result = D('SystemUpdate')->addData($param);
        //插入数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/systemUpdate')) {
                $new_info = D('SystemUpdate')->getFind(array("system_update_id"=> $result['data']));
                $new_info = $this->_addStatus($new_info);
                $cahce_all = F('Cache/systemUpdate');
                $cahce_all['data'][] = $new_info;
                $cahce_all['count'] =  $cahce_all['count']+1;
                F('Cache/systemUpdate', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | SystemUpdate 系统更新修改
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function editSystemUpdate($param)
    {
        //必须参数
        $param = array_filter($param);
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('SystemUpdate')->editData($param,$param['system_update_id']);
        //更新数据成功执行清除缓存
        if ($result['code']==0){
            if (F('Cache/systemUpdate')) {
                $new_info = D('SystemUpdate')->getFind(array("system_update_id"=>$param['system_update_id']));
                $new_info = $this->_addStatus($new_info);
                $cahce_all = F('Cache/systemUpdate');
                foreach($cahce_all['data'] as $k=>$v){
                    if($v['system_update_id'] == $param['system_update_id']){
                        $cahce_all['data'][$k] = $new_info;
                    }
                }
                F('Cache/systemUpdate', $cahce_all);
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | 删除系统更新详情---更新文件缓存
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function delSystemUpdate($param)
    {
        //必须参数
        $param = array_filter($param);
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'参数异常');
        $result = D('SystemUpdate')->delData($param['system_update_id']);
        //更新数据成功执行清除缓存
        if ($result!==false) {
            if (F('Cache/systemUpdate')) {
                $cahce_all = F('Cache/systemUpdate');
                if(!empty($cahce_all)){
                    foreach($cahce_all['data'] as $k=>$v){
                        if($v['system_update_id'] == $param['system_update_id']){
                            unset($cahce_all['data'][$k]);
                            $cahce_all['count'] =  $cahce_all['count']-1;
                        }
                    }
                }
                F('Cache/systemUpdate', $cahce_all);
            }
            return array('code'=>0, 'msg'=>'删除成功');
        }
        return array('code'=>100, 'msg'=>'删除失败');
    }

    /*
   |--------------------------------------------------------------------------
   | systemUpdate 获取系统更新详情
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function getSystemUpdateInfo($param)
    {
        //必须参数
        if(empty($param['system_update_id'])) return array('code'=>300,'msg'=>'参数异常');
        if( F('Cache/systemUpdate') ) {
            $channe_list = F('Cache/systemUpdate');
        }else{
            $channe_list = $this->_getSystemUpdateList();
            F('Cache/systemUpdate', $channe_list);
        }
        foreach($channe_list['data'] as $k=>$v){
            if($v['system_update_id']==$param['system_update_id']){
                $channel_info = $v;
            }
        }
        return array('code'=>'0', 'data'=>$channel_info);
    }

    /**
     * 获取更新列表 + 状态转换
     * @return array
     */
    protected function _getSystemUpdateList()
    {
        $list['data'] = D('SystemUpdate')->getList();
        $list['count'] = D('SystemUpdate')->getCount();
        if(!empty($list['data'])){
            $list['data'] = $this->_addStatus($list['data']);
        }
        return $list;
    }

    /**
     * 添加状态
     * @return array
     */
    protected function _addStatus($array=null){
        //添加多职位
        if(!empty($array)) {
            if ((count($array) == count($array, 1))) {
                $_array[] = $array;
            } else {
                $_array = $array;
            }
            foreach($_array as $k=>$v){
                if(!empty($v['system_user_id'])){
                    $systemUser = D('SystemUser','Service')->getSystemUsersInfo(array('system_user_id'=>$v['system_user_id']));
                    $_array[$k]['system_realname'] = $systemUser['data']['realname'];
                }
                $_array[$k]['create_time'] = date('Y-m-d H:i', $v['createtime']);
            }
        }
        //原格式返回
        if ((count($array) == count($array, 1))) {
            return $_array[0];
        } else {
            return $_array;
        }
    }
}