<?php
/*
|--------------------------------------------------------------------------
| 客服代码表
|--------------------------------------------------------------------------
| createtime：2016-04-21
| updatetime：2016-04-21
| updatename：zgt
*/
namespace Common\Model;
use Common\Model\SystemModel;

class ServicecodeModel extends SystemModel
{

    protected $servicecodeDb;

    public function _initialize()
    {

    }


    /**
     * 获取客服代码
     */
    public function getOwnServicecode($where)
    {
        $servicecodeList = M("servicecode")->where($where)->select();
        foreach ($servicecodeList as $key => $servicecode) {
            $terminal = M("terminal")->where("terminal_id = $servicecode[terminal_id]")->find();
            $servicecode['terminalname'] = $terminal['terminalname'];
            $servicecodeList[$key] = $servicecode;
        }
        return $servicecodeList;

    }


    /**
     * 获取所有客服代码
     * @author zgt
     * @return array
     */
    public function getAllServicecode($order='servicecode_id desc',$page='1,10',$where=array('status'=>1)){

        if (F('Cache/Promote/servicecode')) {
            $servicecodeAll = F('Cache/Promote/servicecode');
        } else {
            //表前缀
            $DB_PREFIX = C('DB_PREFIX');
            //参数整理
            $servicecodeAll['data'] = $this
                ->field(array(
                    "{$DB_PREFIX}servicecode.servicecode_id",
                    "{$DB_PREFIX}servicecode.title",
                    "{$DB_PREFIX}servicecode.system_user_id",
                    "{$DB_PREFIX}servicecode.url",
                    "{$DB_PREFIX}servicecode.servicecode",
                    "{$DB_PREFIX}servicecode.remark",
                    "{$DB_PREFIX}servicecode.status",
                    "{$DB_PREFIX}servicecode.createtime",
                    "{$DB_PREFIX}terminal.terminal_id",
                    "{$DB_PREFIX}terminal.terminalname",
                    "{$DB_PREFIX}system_user.realname",
                    "{$DB_PREFIX}system_user.username",
                    "{$DB_PREFIX}system_user.email"
                ))
                ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__SERVICECODE__.system_user_id')
                ->join('LEFT JOIN __TERMINAL__ ON __SERVICECODE__.terminal_id=__TERMINAL__.terminal_id')
                ->select();
            $servicecodeAll['count'] = $this->count();
            F('Cache/Promote/servicecode',$servicecodeAll);
        }

        $redata = $this->disposeArray($servicecodeAll, $order, $page, $where);
        return $redata;
    }

    /**
     * 添加客服代码
     * @author zgt
     * @return array
     */
    public function addServicecode($data){
        //表前缀
        $DB_PREFIX = C('DB_PREFIX');
        $fixation_data['createtime'] = time();
        $fixation_data = array_merge_recursive($fixation_data,$data);
        $result = $this->data($fixation_data)->add();
        if ($result!==false){
            // if (F('Cache/Promote/servicecode')) {
            //     $cacheAll = F('Cache/Promote/servicecode');
            //     $cacheAll['data'][] = $this
            //         ->field(array(
            //             "{$DB_PREFIX}servicecode.servicecode_id",
            //             "{$DB_PREFIX}servicecode.title",
            //             "{$DB_PREFIX}servicecode.system_user_id",
            //             "{$DB_PREFIX}servicecode.url",
            //             "{$DB_PREFIX}servicecode.servicecode",
            //             "{$DB_PREFIX}servicecode.remark",
            //             "{$DB_PREFIX}servicecode.status",
            //             "{$DB_PREFIX}servicecode.createtime",
            //             "{$DB_PREFIX}terminal.terminal_id",
            //             "{$DB_PREFIX}terminal.terminalname",
            //             "{$DB_PREFIX}system_user.realname",
            //             "{$DB_PREFIX}system_user.username",
            //             "{$DB_PREFIX}system_user.email"
            //         ))
            //         ->where(array('servicecode_id'=>$result))
            //         ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__SERVICECODE__.system_user_id')
            //         ->join('LEFT JOIN __TERMINAL__ ON __SERVICECODE__.terminal_id=__TERMINAL__.terminal_id')
            //         ->find();
            //     $cacheAll['count'] = ($cacheAll['count']+1);
            //     F('Cache/Promote/servicecode', $cacheAll);
            // }
            return $result;
        }
        return false;
    }
    /**
     * 修改客服代码
     * @author zgt
     * @return array
     */
    public function editServicecode($data,$servicecode_id){
        //表前缀
        //$DB_PREFIX = C('DB_PREFIX');
        $result = $this->where("servicecode_id={$servicecode_id}")->save($data);
        if ($result!==false) {
            return true;
        }
        // if ($result!==false){
        //     if (F('Cache/Promote/servicecode')) {
        //         $cacheAll = F('Cache/Promote/pages');
        //         $newInfo = $this
        //             ->field(array(
        //                 "{$DB_PREFIX}servicecode.servicecode_id",
        //                 "{$DB_PREFIX}servicecode.title",
        //                 "{$DB_PREFIX}servicecode.system_user_id",
        //                 "{$DB_PREFIX}servicecode.url",
        //                 "{$DB_PREFIX}servicecode.servicecode",
        //                 "{$DB_PREFIX}servicecode.remark",
        //                 "{$DB_PREFIX}servicecode.status",
        //                 "{$DB_PREFIX}servicecode.createtime",
        //                 "{$DB_PREFIX}terminal.terminal_id",
        //                 "{$DB_PREFIX}terminal.terminalname",
        //                 "{$DB_PREFIX}system_user.realname",
        //                 "{$DB_PREFIX}system_user.username",
        //                 "{$DB_PREFIX}system_user.email"
        //             ))
        //             ->where(array('servicecode_id'=>$result))
        //             ->join('LEFT JOIN __SYSTEM_USER__ ON __SYSTEM_USER__.system_user_id=__SERVICECODE__.system_user_id')
        //             ->join('LEFT JOIN __TERMINAL__ ON __SERVICECODE__.terminal_id=__TERMINAL__.terminal_id')
        //             ->find();
        //         foreach($cacheAll['data'] as $k=>$v){
        //             if($v['servicecode_id'] == $result){
        //                 $cacheAll['data'][$k] = $newInfo;
        //             }
        //         }
        //         F('Cache/Promote/servicecode', $cacheAll);
        //     }
        //     return true;
        // }
        return false;
    }
    /**
     * 客服代码详情
     * @author zgt
     * @return array
     */
    public function detailServicecode($servicecode_id){
        $servicecodeInfo = $this->where("servicecode_id={$servicecode_id}")->find();
        return $servicecodeInfo;
    }
}
