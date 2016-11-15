<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\SystemUserController;
use Common\Controller\OrderController as OrderMainController;
use Common\Service\UserService;

class ManagementController extends SystemController
{
    /*
    |--------------------------------------------------------------------------
    | 后台配置
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function configuration()
    {
//        $redis = new \Redis();
//        $redis->connect('localhost', '6379');
//        $data = D('User')->limit('0,5000')->select();

        //获取配置-config
        $data = loadconfig('rbac',APP_PATH.'Common/Conf/');
        //超级管理员
        $data['ADMIN_SUPER_ROLE'] = C('ADMIN_SUPER_ROLE');
        //短信告警额外接收人
        $data['SMSHINT_USER'] = C('SMSHINT_USER');
        //销售
        $data['ADMIN_MARKET_ROLE'] = C('ADMIN_MARKET_ROLE');
        //教务
        $data['ADMIN_EDUCATIONAL_ROLE'] = C('ADMIN_EDUCATIONAL_ROLE');
        //客户学费优惠金额最高限制
        $data['ADMIN_USER_MAX_DISCOUNT'] = C('ADMIN_USER_MAX_DISCOUNT');
        //模版赋值
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 修改配置
     * @author zgt
     */
    public function editConfig()
    {
        //获取数据
        $data = I("post.");
        $file = $data['type'];
        unset($data['type']);
        $arrData = loadconfig($file, APP_PATH.'System/Conf/');
        foreach($arrData as $k=>$v){
            if(!empty($data['keyid']) && $data['keyid']==$k){
                $arrData[$k] = $data['value'];
            }
        }
        $reflag = updateConfig($file, $arrData, APP_PATH.'System/Conf/');
        if($reflag!==false){
            $this->ajaxReturn('0', '配置修改成功');
        }
        $this->ajaxReturn(1, '配置修改失败');
    }

    protected  $data;

    /*
    |--------------------------------------------------------------------------
    | 缓存管理
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function cahceList()
    {
        $data['path_Data'] = $this->getpath('Runtime/Data');
        $data['path_Cache'] = $this->getpath('Runtime/Cache');
        $this->assign('data', $data);
        $this->display();
    }

    /*
   |--------------------------------------------------------------------------
   | 定时程序日志管理
   |--------------------------------------------------------------------------
   | @author zgt
   */
    public function cmdLogList()
    {
        if(IS_POST){
            $path = I('post.path');
            //过滤
            $path = str_replace('..','.',$path);
            $redata = file_get_contents('../'.$path);
            if($redata!==false){
                $this->ajaxReturn('0', '获取成功',$redata);
            }
            $this->ajaxReturn(1, '获取失败');
        }
//        print_r($this->getpath('Shell/log/allot'));exit;
        $data['path_log']['allot']= array(
            'name'=>'allot',
            'type'=>'dir',
            'path'=>'Shell/log/allot',
            'children'=>$this->getpath('Shell/log/allot')
        );
        uasort($data['path_log']['allot']['children'], function($a, $b) {
            $al = (str_replace('.log','',$a['name']));
            $bl = (str_replace('.log','',$b['name']));
            return ($al>$bl)?-1:1;
        });
        $data['path_log']['recover'] = array(
            'name'=>'recover',
            'type'=>'dir',
            'path'=>'Shell/log/recover',
            'children'=>$this->getpath('Shell/log/recover')
        );
        uasort($data['path_log']['recover']['children'], function($a, $b) {
            $al = (str_replace('.log','',$a['name']));
            $bl = (str_replace('.log','',$b['name']));
            return ($al>$bl)?-1:1;
        });
        $this->assign('data', $data);
        $this->display();
    }

    /*
    |--------------------------------------------------------------------------
    | 今日任务设置列表
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function taskList()
    {
        $this->display();
    }


    /*
    |--------------------------------------------------------------------------
    | 今日任务添加
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function taskAdd()
    {
        $this->display();
    }



    /**
     * 获取文件目录
     * @author zgt
     */
    protected function getpath($path,$data=null,$default_fath='../'){
        if($data===null){
            $data = array();
        }
        $path = str_replace('..','.',$path);
        $fileArr = scandir($default_fath.$path);
        foreach($fileArr as $k=>$v){
            if($v!='.' && $v!='..'){
                if(is_dir($default_fath.$path.'/'.$v)){
                    $dirArr = scandir($default_fath.$path.'/'.$v);
                    $dirArr = $this->getpath($path.'/'.$v,$dirArr);
                    foreach($data as $k2=>$v2){
                        if($v2==$v){
                            unset($data[$k2]);
                        }
                    }
                    $data[] = array('name'=>$v, 'type'=>'dir', 'path'=>$path.'/'.$v,'children'=>$dirArr);
                }else{
                    $data[] = array('name'=>$v, 'type'=>'file', 'path'=>$path.'/'.$v);
                    unset($data[$k]);
                }
            }else{
                unset($data[$k]);
            }
        }
        return $data;
    }

    /**
     * 删除缓存
     * @author zgt
     */
    public function delCahce()
    {
        //获取数据
        $data = I("post.");
        if($data['level']=='dir'){
            $reflag = $this->deldir('../'.$data['path']);
        }else{
            $reflag = unlink('../'.$data['path']);
        }
        if($reflag!==false){
            $this->ajaxReturn('0', '缓存删除成功');
        }
        $this->ajaxReturn(1, '缓存删除失败');
    }

    protected function deldir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

}