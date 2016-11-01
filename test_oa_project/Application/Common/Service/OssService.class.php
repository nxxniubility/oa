<?php
/*
* Session服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class OssService extends BaseService {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	private $ossTool=null;
    //初始化
    public function _initialize() {
        Vendor('Oss.OssTool', VENDOR_PATH, '.class.php');
        $this->ossTool = new \OssTool();
    }
	
    /**
     * 客户端获取签名公共调用地址
     * @author luoyu
     */
    public function createSign($dir,$callback_data=array()) {
		
        if (!empty($callback_data)) {
            $oss_data = array(
                'oss_dir' => $dir,
                'is_callback' => true,
                'callbackUrl' => 'http://oss-demo.aliyuncs.com:23450', //'/index.php?g=Home&c=Index&a=ossCallBack',//回调地址
                'callbackHost' => 'oss-demo.aliyuncs.com', //回调Host
                'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}'
            );
        } else {
            $oss_data = array(
                'oss_dir' => $dir,
                'is_callback' => false
            );
        }
        $response = $this->ossTool->get_oss_signature($oss_data);
        return array("code"=>0,"msg"=>"获取OSS上传签名成功！","data"=>$response);
    }
    
   

    /**
     * 服务器本地文件上传到OSS方法
     * @author luoyu
     * 上传文件
     * @param type $filepath  文件绝对路径
     *  Array
      (
      [object] => E:\www\xapple\demo_onethink\web\Uploads\test\2015-07-30\55b985f7b8531.jpg
      [config] => array()
      )
     *  @return type
     * Array
      (
      [status] => 200
      [header] => Array
      (
      [server] => AliyunOSS
      [date] => Thu, 21 Jan 2016 09:14:11 GMT
      [content-length] => 0
      [connection] => keep-alive
      [x-oss-request-id] => 56A0A15F96A201551E6EB019
      [etag] => "90C0B76472951FC56EBE448D2DC20B1B"
      [_info] => Array
      (
      [url] => http://zelin.oss-cn-shenzhen.aliyuncs.com/attachment/701M97GGH822.jpg
      [content_type] =>
      [http_code] => 200
      [header_size] => 229
      [request_size] => 418
      [filetime] => -1
      ......
      )
     */
    public function ossUploadFile($filepath, $newfile,$config = array()) {
       
		$filepath=str_replace("\\", "/", $filepath);       
        $file = array('path' => $filepath, "object" => $newfile);		
        $result = $this->ossTool->add($file);
        return $result;
		
    }

    /**
     * 上传整个目录到OSS方法
     * @author luoyu
     * 上传文件
     * @param type $dir_realpath  目录绝对路径
     *  Array
      (
      [object] => E:\www\xapple\demo_onethink\web\Uploads\test\2015-07-30\55b985f7b8531.jpg
      [config] => array()
      )
     *  @return type
     * Array
      (
      [status] => 200
      [header] => Array
      (
      [server] => AliyunOSS
      [date] => Thu, 21 Jan 2016 09:14:11 GMT
      [content-length] => 0
      [connection] => keep-alive
      [x-oss-request-id] => 56A0A15F96A201551E6EB019
      [etag] => "90C0B76472951FC56EBE448D2DC20B1B"
      [_info] => Array
      (
      [url] => http://zelin.oss-cn-shenzhen.aliyuncs.com/attachment/701M97GGH822.jpg
      [content_type] =>
      [http_code] => 200
      [header_size] => 229
      [request_size] => 418
      [filetime] => -1
      ......
      )
     */
    public function ossUploadDir($dir_realpath, $config = array()) {
       
        $$dir_realpath = str_replace("\\", "/", $dir_realpath);
        $result = $this->ossTool->upload_by_dir($dir_realpath);
        return $result;
    }

    /*
     * 获取OSS上对象列表
     * @author luoyu	 * 
     * @param type $filepath  文件在OSS上的目录    
     * @return type 
     * Array
      (
      [status] => 200
      [header] => Array

      .......

      )
     */

    public function ossListObject($options) {
      
        $filepath = str_replace("\\", "/", $filepath);
        $result = $this->ossTool->list_object($options);
        return $result;
    }

    /*
     * 获取OSS上的对象
     * @author luoyu	 * 
     * @param type $filepath  文件在OSS上的路径    
     * @return type 
     * Array
      (
      [status] => 200
      [header] => Array
      (
      [server] => AliyunOSS
      [date] => Thu, 21 Jan 2016 09:06:25 GMT
      [content-type] => image/jpeg
      [content-length] => 186036
      [connection] => keep-alive
      [x-oss-request-id] => 56A09F91884D1555736EF1A2
      [accept-ranges] => bytes
      [etag] => "90C0B76472951FC56EBE448D2DC20B1B"
      [last-modified] => Thu, 21 Jan 2016 09:06:25 GMT
      [x-oss-object-type] => Normal
      )
      .......

      )
     */

    public function ossGetObject($filepath, $config) {
       
        $filepath = str_replace("\\", "/", $filepath);
        $object = $this->ossTool->getInfo($filepath);
        return $object;
    }

    /*
     *  删除OSS上的对象
     * @author luoyu	 * 
     * @param type $filepath  文件在OSS上的相对路径    
     * @return type 
     * Array
      (
      [status] => 404  //不存在返回404状态码，存在则返回200
      [header] => Array
      (
      [server] => AliyunOSS
      [date] => Thu, 21 Jan 2016 09:35:42 GMT
      [content-type] => application/xml
      [content-length] => 281
      [connection] => keep-alive
      [x-oss-request-id] => 56A0A66E7885ED3D14702B42
      [_info] => Array

      )
      .......

      )
     */

    public function ossDeleteFile($filepath, $config) {
       
        $filepath = str_replace("\\", "/", $filepath);
        $object = $this->ossTool->delete_object($filepath);
        return $object;
    }

    /**
     * 判断OSS上的对象是否存在
     * @author luoyu	 * 
     * @param type $filepath  文件在OSS上的相对路径    
     * @return type 
     * Array
      (
      [status] => 200
      [header] => Array
      (
      [server] => AliyunOSS
      [date] => Thu, 21 Jan 2016 09:06:25 GMT

      )
      .......

      )
     */
    public function ossIsObjectExist($filepath, $config) {
       
        $filepath = str_replace("\\", "/", $filepath);
        $return = $this->ossTool->is_object_exist($filepath);
        return $return;
    }

}