<?php

/**
 * 系统父级Controller
 * @author luoyu
 */

namespace Common\Controller;
use Think\Controller;
use \Exception;
use Org\Util\Tool;

class BaseController extends Controller {
    public function _initialize() {
    }

    /**
     * 判断是否登录
     * @author xanxus
     */
    protected function isLogin($name) {
        $zp_user_id = D("Session","Service")->get($name);
        if (empty($zp_user_id))
            return false;
        return true;
    }

   
    /**
     * 开启事务
     * @author Echo
     */
    protected function startTrans() {
        M()->startTrans();
    }

    /**
     * 提交事务
     * @author Echo
     */
    protected function commit() {
        M()->commit();
    }

    /**
     * 回滚事务
     * @author Echo
     */
    protected function rollback() {
        M()->rollback();
    }

    /**
     * 操作错误跳转的快捷方法
     * @author Echo
     */
    protected function error($msg = 'error', $code = 1, $data = '') {
        if (IS_AJAX) {
            $tmp = $this->ajaxData($code, $msg, $data);
            $this->ajaxReturn($tmp);
        }
        parent::error($msg, $data);
    }

    /**
     * 操作成功跳转的快捷方法
     * @author Echo
     */
    protected function success($msg = 'success', $code = 0, $data = '') {
        if (IS_AJAX) {
            $tmp = $this->ajaxData($code, $msg, $data);
            $this->ajaxReturn($tmp);
        }
        parent::success($msg, $data);
    }

    /**
     * 封装ajax返回格式
     * @author Echo
     */
    protected function ajaxData($code, $msg, $data = "") {
        return array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        );
    }

    /**
     * 重写Thinkphp ajaxReturn方法
     * (non-PHPdoc)
     * @see \Think\Controller::ajaxReturn()
     * code = 0成功
     */
    protected function ajaxReturn($code,$msg,$data=null){

        $tmp = array(
            'code'=> $code,
            'msg' => $msg,
            'data'=> $data,
        );
        parent::ajaxReturn($tmp);
    }

    /**
     * 配置文件更新写入
     * @param unknown $data 写入的数据
     * @param unknown $fileName 文件名
     * @return number
     * @author Sunles
     */
    protected function updateConfig($data, $fileName) {
        $fileName = CONF_PATH . $fileName;
        return file_put_contents($fileName, "<?php \nreturn " . stripslashes(var_export($data, true)) . ";", LOCK_EX);
    }


	/*
	* 异步获取数据网关
	* @author  luoyu
	*
	*/
    public function getAjaxData(){
    	$type=I('post.type');	
		if(!IS_AJAX)$this->error('非法请求！',0);
		switch ($type)
		{
		  case 'area':
		  
			  $area_id=I('request.area_id');
			  if(!$area_id)$this->ajaxReturn(array('code'=>1,'msg'=>'非法参数！'));
			  $mod=D('ZpArea');
			  $zp_area=$mod->getCity();
			  if($zp_area['s'][$area_id])
			  {
				  $this->ajaxReturn(array('code'=>0,'msg'=>'','data'=>$zp_area['s'][$area_id]));
							  
			  }else{
				  $this->ajaxReturn(array('code'=>1,'msg'=>'没有相关数据！'));
				 
			  }
			  break;
		   default:
		      
		}
    }
    
    /**
     * 文件转换pdf
     * @author  luoyu
	 * @param  $fname 要转换的文件的绝对路径
	 * @param  $totype 要转换成文件类型
     */     
    protected function   docConv($fname,$totype='pdf'){
		
		$upload_map=C('UPLOAD_MAP');
		if(!$upload_map[$totype])$totype='file';
		$upload_path=C('UPLOAD_PATH');
		$output_dir=$upload_path.$upload_map[$totype];
        $sub_dir=date('Ymd');
		$output_dir=$output_dir.$sub_dir."/";
        if(!is_dir($output_dir)) {
            mkdir($output_dir, 0777, true);
        }
        $filename=uniqid().time().".".$totype;
        $output_file = $output_dir.$filename;
	
	    $return_code=1;
		//调用linux组件进行转换
		system("sudo ".BASE_PATH."/../Shell/unoconv/unoconv  -f  ".$totype."  -o  ".$output_file."  ".$fname,$return_code);		
		
		if(!file_exists($output_file))
		{
		    return  array('code'=>10,'msg'=>'无法生成文件，转换失败！');
		}
		if($return_code==0)
		{
	        return  array('code'=>0,'msg'=>'转换成功！','filepath'=>C('UPLOAD_DIR').$upload_map[$totype].$sub_dir."/".$filename);
		}else{
		    return  array('code'=>10,'msg'=>'转换发生错误，转换失败！');
		}
    
    }
   

   
    /**
     * 判断静态文件是否存在
     *  @param 文件名 $statis_file
     * 
     */
    protected function staticHtml($statis_file=''){
        $html_dir =RUNTIME_PATH."Statichtml/";//生成静态文件路径
        
        if(!is_dir($html_dir)) {
            mkdir(RUNTIME_PATH."Statichtml", 0777, true);
        }
        $file_path=$html_dir.$statis_file;//生成静态文件路径
        //当静态文件存在的时候
        if(is_file($file_path))
        {
            return $this->fetch($file_path);
        }else{
            return array('status'=>false,'file_path'=>$file_path,'html_dir'=>$html_dir) ;
        }
    }

    
    /**
     * 短信公共接口
     * @author Sunles
     * phone_num：电话号码
     * sign:标识
     * signName:签名(注册验证)
     * smsdata:短信内容"{\"code\":\"$smscode\",\"product\":\"$alidayu\"}"
     * smsdata_id：模板ID（SMS_5260550）
     */
    protected function sms($phone_num,$sign,$smsdata,$smsdata_id,$signName)
    {
        import('Vendor.Alidayu.TopSdk');
        $client = new \TopClient;
        $request = new \AlibabaAliqinFcSmsNumSendRequest;
        $request->setExtend($sign);
        $request->setSmsType("normal");
        $request->setSmsFreeSignName($signName);
        $request->setSmsParam(json_encode($smsdata));
        $request->setRecNum($phone_num);
        $request->setSmsTemplateCode($smsdata_id);
        return $client->execute($request);
    }

    /**
     * @author nxx
     * @param unknown $maxSize 文件上传大小
     * @param unknown $exts 文件上传类型
     * @param unknown $rootPath 附件根目录
     * @param unknown $savePath 附件二级目录
     * @param unknown $subName 附件三级目录
     */
    public function uploadFile($exts,$rootPath,$savePath,$maxSize=10567840,$subName=array('date','Ymd')){
        $upload = new \Think\Upload();
        $upload->maxSize = $maxSize;// 设置附件上传大小
        $upload->exts = $exts;// 设置附件上传类型
        $upload->rootPath  = $rootPath;// 设置附件上传根目录
        $upload->savePath  = $savePath;
        $upload->subName = $subName;
        $upLoadInfo = $upload->upload();

        if(!$upLoadInfo) $this->error($upload->getError());
        return $upLoadInfo;
    }

}
