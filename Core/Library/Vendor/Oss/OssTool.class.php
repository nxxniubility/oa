<?php

class OssTool {

    public static $alioss;
    public static $httpCode = array(
        200 => '操作成功',
        403 => '拒绝访问',
        404 => '资源不存在'
    );

    /**
     *
     * @var type 
     */
    public static $errorCode = array(
        'AccessDenied' => '拒绝访问'//403
        , 'BucketAlreadyExists' => 'Bucket已经存在'//409
        , 'BucketNotEmpty' => 'Bucket不为空'//409
        , 'EntityTooLarge' => '实体过大'//400
        , 'EntityTooSmall' => '实体过小'//400
        , 'FileGroupTooLarge' => '文件组过大'//400
        , 'InvalidLinkName' => 'Object Link与指向的Object同名'//400
        , 'LinkPartNotExist' => 'Object Link中指向的Object不存在'//400
        , 'ObjectLinkTooLarge' => 'Object Link中Object个数过多'//400
        , 'FieldItemTooLong' => 'Post请求中表单域过大'//400
        , 'FilePartInterity' => '文件Part已改变'//400
        , 'FilePartNotExist' => '文件Part不存在'//400
        , 'FilePartStale' => '文件Part过时'//400
        , 'IncorrectNumberOfFilesInPOSTRequest' => 'Post请求中文件个数非法'//400
        , 'InvalidArgument' => '参数格式错误'//400
        , 'InvalidAccessKeyId' => 'Access Key ID不存在'//403
        , 'InvalidBucketName' => '无效的Bucket名字'//400
        , 'InvalidDigest' => '无效的摘要'//400
        , 'InvalidEncryptionAlgorithmError' => '指定的熵编码加密算法错误'//400
        , 'InvalidObjectName' => '无效的Object名字'//400
        , 'InvalidPart' => '无效的Part'//400
        , 'InvalidPartOrder' => '无效的part顺序'//400
        , 'InvalidPolicyDocument' => '无效的Policy文档'//400
        , 'InvalidTargetBucketForLogging' => 'Logging操作中有无效的目标bucket'//400
        , 'InternalError' => 'OSS内部发生错误'//500
        , 'MalformedXML' => 'XML格式非法'//400
        , 'MalformedPOSTRequest' => 'Post请求的body格式非法'//400
        , 'MaxPOSTPreDataLengthExceededError' => 'Post请求上传文件内容之外的body过大'//400
        , 'MethodNotAllowed' => '不支持的方法'//405
        , 'MissingArgument' => '缺少参数'//411
        , 'MissingContentLength' => '缺少内容长度'//411
        , 'NoSuchBucket' => 'Bucket不存在'//404
        , 'NoSuchKey' => '文件不存在'//404
        , 'NoSuchUpload' => 'Multipart Upload ID不存在'//404
        , 'NotImplemented' => '无法处理的方法'//501
        , 'PreconditionFailed' => '预处理错误'//412
        , 'RequestTimeTooSkewed' => '发起请求的时间和服务器时间超出15分钟'//403
        , 'RequestTimeout' => '请求超时'//400
        , 'RequestIsNotMultiPartContent' => 'Post请求content-type非法'//400
        , 'SignatureDoesNotMatch' => '签名错误'//403
        , 'TooManyBuckets' => '用户的Bucket数目超过限制'//400
        , 'InvalidEncryptionAlgorithmError' => '指定的熵编码加密算法错误'//400
    );

 
    //OSS配置信息
	
    public static $config;
	public static $host='';

    public function __construct($config = array()) {
		define('ALI_LOG_PATH',LOG_PATH.'Oss/');		
        if (!self::$alioss) {
            self::$config = array_merge(C('ALIOSS_CONFIG'), $config);
            if (empty(self::$config)) {
                die('empty OSS config!');
            }
			
            foreach (self::$config as $key => $value) {
                define($key, $value);
            }
			self::$host = self::$config['OSS_DOMAIN'];
			
            Vendor('Oss.sdk',VENDOR_PATH,'.class.php');
            if (class_exists('ALIOSS')) {
                self::$alioss = new \ALIOSS(self::$config['OSS_ACCESS_ID'],self::$config['OSS_ACCESS_KEY'],self::$host);
				self::$alioss->set_enable_domain_style(true);
                self::$alioss->set_debug_mode(FALSE);
            } else {
                die('file sdk.class.php  not exist!');
            }
        }
    }
	/**
     * 生成签名，设置回调地址
     * @param data 传递参数：
	       oss_dir   上传目录
		   callbackUrl   回调地址
		   callbackBody  回调内容
     * @return array     
     */
    public function get_oss_signature($data)
	{
	    /***************************/
		if($data['is_callback'])
		{
			$callback_body = '{"callbackUrl":"'.$data['callbackUrl'].'","callbackHost":"'.$data['callbackHost'].'","callbackBody":"'.$data['callbackBody'].'","callbackBodyType":"application/x-www-form-urlencoded"}';
			$base64_callback_body = base64_encode($callback_body);
		}
		/***************************/
		$now = time();
        $expire = 30*60; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);
		
		/***************************/
		$dir = $data['oss_dir'];
	
		//最大文件大小.用户可以自己设置
		$condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
		$conditions[] = $condition; 
	
		//表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
		$start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
		$conditions[] = $start; 
	
	
		$arr = array('expiration'=>$expiration,'conditions'=>$conditions);
		//echo json_encode($arr);
		//return;
		$policy = json_encode($arr);		
	
		$base64_policy = base64_encode($policy);
		$string_to_sign = $base64_policy;
		$signature = base64_encode(hash_hmac('sha1', $string_to_sign, self::$config['OSS_ACCESS_KEY'], true));
	
		$response = array();
		$response['accessid'] = self::$config['OSS_ACCESS_ID'];
		$response['host'] = self::$host;
		$response['policy'] = $base64_policy;
		$response['signature'] = $signature;
		$response['expire'] = $end;
		if($data['is_callback'])$response['callback'] = $base64_callback_body;
		//这个参数是设置用户上传指定的前缀
		$response['dir'] = $dir;
		return $response;		
		
	}
    /**
     * 上传文件
     * @param type $file
     * Array
      (
      [object] => E:\360yun\www\xapple\demo_onethink\web\Uploads\test\2015-07-30\55b985f7b8531.jpg
      [path] => Uploads/test/2015-07-30/55b985f7b8531.jpg
      )
     * @return type
     * Array
      (
      [status] => 200
      [url] => http://oss.aliyuncs.com/xapple/Uploads/test/2015-07-27/55b5781d0b836.jpg
    
      )
     */
    public function add($file, $config = array()) {
      
	    if(strpos($file['path'],'http://')>-1||strpos($file['path'],'https://')>-1||strpos($file['path'],'ftp://')>-1)
		{
			$result = $this->upload_by_remote(self::$alioss, $file['object'], $file['path']);	
		}else{
            $result = $this->upload_by_file(self::$alioss, $file['object'], $file['path']);	
		}
        $result['url'] = $result['header']['_info']['url'];
       
        return $result;
    }

    /**
     * 文件信息
     * @param type $object Uploads/test/2015-07-30/55b985f7b8531.jpg
     * @return type 
     * Array
      (
      [code] => 200
      [url] => http://oss.aliyuncs.com/xapple/Uploads/test/2015-07-27/55b5781d0b836.jpg
      [msg] => 操作成功
      )
     */
    public function getInfo($filepath) {

        $result = $this->get_object_meta(self::$alioss, $filepath);        
        $result['url'] = $result['header']['_info']['url'];		
        return $result;
    }

    //一些阿里云demo api 未修改
    //获取object列表
    public function list_object($options) {
       
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $options1 = array(
            'delimiter' => '/',
            'prefix' => '',
            'max-keys' => 10,
            //'marker' => 'myobject-1330850469.pdf',
        );
        $options=array_merge($options1,$options);
        $response = self::$alioss->list_object($bucket, $options);		
        return $this->_format($response);
    }

    //创建目录
    public function create_directory($obj, $dir) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');    
        $response = $obj->create_object_dir($bucket, $dir);
        $this->_format($response);
    }
    //通过内容上传文件
    public function upload_by_remote($obj,$object,$url) {
		$content=file_get_contents($url);
		$response = $this->upload_by_content($obj, $object, $content);
        return $response;
    }
    //通过内容上传文件
    public function upload_by_content($obj,$object,$content) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');    
		$upload_file_options = array(
			'content' => $content,
			'length' => strlen($content),
			ALIOSS::OSS_HEADERS => array(
				'Expires' => '2028-10-01 08:00:00',
			),
		);
		$response = $obj->upload_file_by_content($bucket, $object, $upload_file_options);
        return $this->_format($response);
    }

    //通过路径上传文件
    public function upload_by_file($obj, $object, $file_path) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');		
        $response = $obj->upload_file_by_file($bucket, $object, $file_path);		
        return $this->_format($response);
    }

    //拷贝object
    public function copy_object($obj) {
        //copy object
        $from_bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $from_object = '&#26;&#26;_100.txt';
        $to_bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $to_object = '&#26;&#26;_100.txt';
        $options = array(
            'content-type' => 'application/json',
        );

        $response = $obj->copy_object($from_bucket, $from_object, $to_bucket, $to_object, $options);
        return $this->_format($response);
    }

    //获取object meta
    public function get_object_meta($obj, $object) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $response = $obj->get_object_meta($bucket, $object);
        return $this->_format($response);
    }

    //删除object
    public function delete_object($filepath) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $response = self::$alioss->delete_object($bucket, $filepath);
        $response=$this->_format($response);
		return $response;
    }

    //删除objects
    public function delete_objects($filepath_array) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $options = array(
            'quiet' => false,
             //ALIOSS::OSS_CONTENT_TYPE => 'text/xml',
        );

        $response = self::$alioss->delete_objects($bucket, $filepath_array, $options);
		return $this->_format($response);
    }

    /**
     * download object
     * @param type $obj
     * @param type $object
     * @return type
     */
    public function get_object($filepath) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $options = array(
              //ALIOSS::OSS_FILE_DOWNLOAD => "d:\\cccccccccc.sh",
              //ALIOSS::OSS_CONTENT_TYPE => 'txt/html',
        );

        $response = self::$alioss->get_object($bucket, $filepath, $options);
        $this->_format($response);
        return $response;
    }

    //检测object是否存在
    public function is_object_exist($filepath) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $response = self::$alioss->is_object_exist($bucket,$filepath);		
        return $this->_format($response);
    }

    //通过multipart上传文件
    public  function upload_by_multi_part($obj) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $object = 'Mining.the.Social.Web-' . time() . '.pdf';  //英文
        $filepath = "D:\\Book\\Mining.the.Social.Web.pdf";  //英文

        $options = array(
            ALIOSS::OSS_FILE_UPLOAD => $filepath,
            'partSize' => 5242880,
        );

        $response = self::$alioss->create_mpu_object($bucket, $object, $options);
        $this->_format($response);
    }

    //通过multipart上传整个目录
    public function upload_by_dir($dir_realpath) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $recursive = false;
        $response =  self::$alioss->create_mtu_object_by_dir($bucket, $dir_realpath, $recursive);
        return  $this->_format($response);
    }

    //通过multi-part上传整个目录(新版)
    public function batch_upload_file($dir_realpath) {
        $options = array(
            'bucket' => C('ALIOSS_CONFIG.OSS_BUCKET'),
            'object' => 'picture',
            'directory' => $dir_realpath,
        );
        $response =  self::$alioss->batch_upload_file($options);
    }

    

    // 签名url 相关
    //生成签名url,主要用户私有权限下的访问控制
    public function get_sign_url($filepath) {
        $bucket = C('ALIOSS_CONFIG.OSS_BUCKET');
        $timeout = 3600*72;
        $response = self::$alioss->get_sign_url($bucket, $filepath, $timeout);
        return  $this->_format($response);
    }

   

	// 结果 相关
	//格式化返回结果
    public function _format($response) {

        $body = $this->xml_to_array($response->body);
        $result = array(
            'status' => $response->status,
            'header' => $response->header,
            'body' => $body
        );

        if ($result['status'] == 200) {
            return $result;
        } elseif (!empty($body) && key_exists($body['Error']['Code'], self::$errorCode)) {
            $result['error'] = self::$errorCode[$body['Error']['Code']];
        } elseif (key_exists($result['status'], self::$httpCode)) {
            $result['error'] = self::$httpCode[$result['status']];
        } else {
            $result['error'] = "操作失败,请联系管理员";
        }
		

        return $result;

    }

    public function xml_to_array($xml) {
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if (preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            $arr = array();
            for ($i = 0; $i < $count; $i++) {
                $key = $matches[1][$i];
                $val = $this->xml_to_array($matches[2][$i]);  // 递归
                if (array_key_exists($key, $arr)) {
                    if (is_array($arr[$key])) {
                        if (!array_key_exists(0, $arr[$key])) {
                            $arr[$key] = array($arr[$key]);
                        }
                    } else {
                        $arr[$key] = array($arr[$key]);
                    }
                    $arr[$key][] = $val;
                } else {
                    $arr[$key] = $val;
                }
            }
            return $arr;
        } else {
            return $xml;
        }
    }
	
    public function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
}
