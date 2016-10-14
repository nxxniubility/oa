<?php
/*
|--------------------------------------------------------------------------
| 终端表模型
|--------------------------------------------------------------------------
| createtime：2016-05-03
| updatetime：2016-05-03
| updatename：zgt
*/
namespace Common\Model;

use Common\Model\SystemModel;

class TerminalModel extends SystemModel
{
    protected $terminalDb;

    public function _initialize()
    {

    }

    /*
     * 获取所有终端表-缓存
     * @author zgt
     * @return array
     */
    public function getAllTerminal()
    {
        if (F('Cache/Promote/terminal')) {
            $terminalAll = F('Cache/Promote/terminal');
        } else {
            $terminalAll = $this->where(array('status'=>1))->select();
            F('Cache/Promote/terminal', $terminalAll);
        }

        return $terminalAll;
    }

    /**
     * 添加终端表
     * @author zgt
     */
    public function addTerminal($data)
    {
        $data['status'] = 1;
        $result = $this->data($data)->add();
        if ($result!==false){
            $data['terminal_id'] = $result;
            if (F('Cache/Promote/terminal')) {
                $cacheAll = F('Cache/Promote/terminal');
                $cacheAll[] = $data;
                F('Cache/Promote/terminal', $cacheAll);
            }
            return $result;
        }
        return false;
    }

    /**
     * 修改终端表
     * @author zgt
     */
    public function editTerminal($data, $terminal_id)
    {
        $result = $this->where(array('terminal_id'=>$terminal_id))->save($data);
        if ($result!==false){
            if (F('Cache/Promote/terminal')) {
                $newInfo = $this->where(array('terminal_id'=>$terminal_id))->find();
                $cacheAll = F('Cache/Promote/terminal');
                foreach($cacheAll['data'] as $k=>$v){
                    if($v['terminal_id'] == $terminal_id){
                        if(!empty($data['satus']) && $data['satus']==0){
                            unset($cacheAll['data'][$k]);
                        }else{
                            $cacheAll['data'][$k] = $newInfo;
                        }
                    }
                }
                F('Cache/Promote/terminal', $cacheAll);
            }
            return true;
        }
        return false;
    }
}