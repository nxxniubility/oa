<?php
namespace System\Controller;

use Common\Controller\SystemController;
use Common\Controller\SystemUserController;
use Common\Controller\OrderController as OrderMainController;

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

        $con = file_get_contents("file:///C:/Users/Administrator/AppData/Roaming/Foxmail7/Temp-6824-20161008173633/resume.html");
        $pattern= "~<strong.*?>(.*)?</strong>~";
        preg_match_all($pattern,$con,$match);
        print_r($match);exit();
        //获取配置-config
        $data = loadconfig('config',APP_PATH.'System/Conf/');
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


    /*
    |--------------------------------------------------------------------------
    | 缓存管理
    |--------------------------------------------------------------------------
    | @author zgt
    */
    public function cahceList()
    {
        $data['path_Cache'] = $this->getpath('Cache');
        $data['path_Data'] = $this->getpath('Data');

        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 获取文件目录
     * @author zgt
     */
    protected function getpath($path,$data=null){
        if($data===null){
            $data = array();
        }
        $fileArr = scandir(RUNTIME_PATH.$path);
        foreach($fileArr as $k=>$v){
            if($v!='.' && $v!='..'){
                if(is_dir(RUNTIME_PATH.$path.'/'.$v)){
                    $dirArr = scandir($path.'/'.$v);
                    $dirArr = $this->getpath($path.'/'.$v,$dirArr);
                    $data[$k] = array('name'=>$v, 'type'=>'dir', 'path'=>$path.'/'.$v,'children'=>$dirArr);
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
            $reflag = $this->deldir(RUNTIME_PATH.$data['path']);
        }else{
            $reflag = unlink(RUNTIME_PATH.$data['path']);
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