<?php
/*
|--------------------------------------------------------------------------
| 基础公共控制器
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/
namespace Common\Controller;
use Think\Controller;
class BaseController extends Controller {
    
    public function _initialize(){
    }
    
    /**
     * 操作错误跳转的快捷方法
     * @author Echo
     */
    protected function error($msg='error',$code=1,$data='') {
        if(IS_AJAX) {
            $this->ajaxReturn($code,$msg,$data);
        }
        parent::error($msg,$data);
    }
    /**
     * 操作成功跳转的快捷方法
     * @author Echo
     */
    protected function success($msg='success',$code=0,$data='') {
        if(IS_AJAX) {
            $this->ajaxReturn($code,$msg,$data);
        }
        parent::success($msg,$data);
    }
    
    /**
     * 重写Thinkphp ajaxReturn方法
     * (non-PHPdoc)
     * @see \Think\Controller::ajaxReturn()
     * code = 0成功
     */
    protected function ajaxReturn($code,$msg,$data=null,$sign=null){
        
        $tmp = array(
            'code'=> $code,
            'msg' => $msg,
            'data'=> $data,
            'sign'=> $sign
        );
        parent::ajaxReturn($tmp);
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
    邮件公共调用
    Input:
    email - 输入的邮箱，若输入，且密码输入正确，则以此邮箱发送邮件
    psw   - 上述邮箱的密码
    address - 收件人邮箱
    subject - 主题
    content - 邮件内容
    Output：
    success/fail
     * @author Sunles
     */
    protected function mail($email, $psw, $address, $subject, $content){
        return SendMail($email, $psw, $address, $subject, $content);
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


    //*************************************************************/
    //  以下是系列OSS操作方法  begin
    //**************************************************************/
    
    /**
     * 客户端获取签名公共调用地址
     * @author luoyu
     */
    public function getOssSign($define_oss_dir='')
    {
        
        Vendor('Oss.OssTool',VENDOR_PATH,'.class.php');     
        $osstool=new \OssTool();
        $dir=I('post.oss_dir','user_dir/');//这里设置客户端允许上传的目录
		$flat=false;
        if(isset($define_oss_dir)&&$define_oss_dir!='')
        {
			$dir=$define_oss_dir;					      	
        }else{			
        	$flat=true;			
        	if(!in_array($dir,C('ALIOSS_CONFIG.ALIOSS_USER_DIR')))$dir='user_dir/';   
        }
        
        /*
        if($is_callback)
        {
            $oss_data=array(
                'oss_dir'=>$dir,
                'is_callback'=>true,
                'callbackUrl'=>'http://oss-demo.aliyuncs.com:23450',//'/index.php?g=Home&c=Index&a=ossCallBack',//回调地址
                'callbackHost'=>'oss-demo.aliyuncs.com',//回调Host
                'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}'
            );
        }else{
        */
        $oss_data=array(
            'oss_dir'=>$dir,
            'is_callback'=>false
        );
        //}
        
        $response=$osstool->get_oss_signature($oss_data);
        if($flat)
        {	
            echo json_encode(array("code"=>0,"msg"=>"","data"=>$response));
            exit;
        }else{        	
        	return $response;
        }
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
    
    /**
     * 导入Excel
     * @param  $filename 路径文件名
     * @author   Nxx
     */
    public function inputExcel($filename,$encode='utf-8')
    {
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel2005");
        import("Org.Util.PHPExcel.Writer.Excel2007");
        import("Org.Util.PHPExcel.Style.Alignment.php");
        import("Org.Util.PHPExcel.IOFactory.php");

        $objReader = \PHPExcel_IOFactory::createReader('Excel5');

        $objReader->setReadDataOnly(true);   
        $objPHPExcel = $objReader->load($filename);
                                    //load('C:/Users/Administrator/Desktop/1231_2016-04-25-17-17-01.xls');
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    /**
     * 手机号码归属地
     * @param $phone 手机号码
     * @return json
     * @author zgt
     */
    public function phoneVest($phone)
    {
//        $str ='https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel='.$phone;
//        $output = file_get_contents($str);
//        $output = mb_convert_encoding( $output, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5' );
//        $output = str_replace('__GetZoneResult_ =','',$output);
//        $output = str_replace("'",'"',$output);
//        $output = str_replace('mts','"mts"',$output);
//        $output = str_replace('province','"province"',$output);
//        $output = str_replace('catName','"catName"',$output);
//        $output = str_replace('telString','"telString"',$output);
//        $output = str_replace('areaVid','"areaVid"',$output);
//        $output = str_replace('ispVid','"ispVid"',$output);
//        $output = str_replace('carrier','"carrier"',$output);

        $str ='http://apis.juhe.cn/mobile/get?key=192a474a94f5f0176cd5cf1c5d43c34f&phone='.$phone;
        $output = file_get_contents($str);
        $output = json_decode($output,true);
        if($output['resultcode']==200){
            $result = $output['result'];
        }else{
            $result = '';
        }
        return $result;
    }

    /*
     * 对缓存数据进行处理
     * $array =array('data'=>'', 'count'=>) $order="id asc"  $page="1,10" 页码,显示数 $where=array(''=>)
     * @author zgt
    */
    protected function disposeArray($array,$order=null,$page=null,$where=null){
        //对缓存数据进行排序
        if(!empty($order)){
            $order = explode(' ', $order);
            uasort($array['data'], function($a, $b) use($order) {
                $al = ($a[$order[0]]);
                $bl = ($b[$order[0]]);
                if($al==$bl)return 0;
                if($order[1]=='asc')return ($al<$bl)?-1:1;
                else return ($al>$bl)?-1:1;
            });
        }
        //对缓存条件筛选 %%XXXX 模糊搜索
        if(!empty($where)){
            foreach($where as $k=>$v){
                if(!empty($v)) $array['data'] = $this->disposeArray_where($array['data'] ,$k ,$v);
            }
        }
        $array['count'] = count($array['data']);
        //对缓存进行分页
        if(!empty($page)){
            //分页数据
            $page = explode(',', $page);
            foreach($array['data'] as $k=>$v){
                $department_new[] = $v;
            }
            $array['data'] = null;
            foreach($department_new as $k=>$v){
                if($k>=(($page[0]-1)*$page[1]) && $k<($page[0]*$page[1])){
                    $array['data'][] = $v;
                }
            }
        }

        return $array;
    }
    //对缓存条件筛选
    public function disposeArray_where($array, $key, $value){
        $value_link = null;
        if(strpos($value,'%%')!==false) {
            $value_link = explode('%%', $value);
        }
        foreach($array as $k=>$v){
            if(!empty($value_link[1])){
                if(strpos($v[$key], $value_link[1])!==false) $department_new[] = $v;
            }else{
                if($v[$key]==$value) $department_new[] = $v;
            }
        }
        return $department_new;
    }
}