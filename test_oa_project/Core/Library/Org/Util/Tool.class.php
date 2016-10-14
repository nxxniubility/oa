<?php
namespace Org\Util;
/**
 * 表单验证类
 * @author Sunles
 * 
 */
class Tool{
    /**
     * 验证手机号码
     * @access public
     * @param   string $mobilephone 手机号码
     * @author  Sunles
     * @return mixed
     */
    static public function checkMobile($mobilephone){
        $mobilephone = trim($mobilephone);
        if(preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|^17[0-9]{1}[0-9]{8}$/",$mobilephone)){
            return  $mobilephone;
        } else {
            return false;
        }
    }
    //（包括移动和固定电话）:(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14} 
    /**
     * 验证固定电话
     * @param  string $tel 固定电话
     * @return [type]      [description]
     * @author longguojun <[<email address>]>
     */
    static public function checkTel($tel){
        $tel = trim($tel);
        $regx = "/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/";
        $regx = "/^0\d{2,3}-?\d{7,8}$/";
        if(preg_match($regx,$tel)){
            return  $tel;
        } else {
            return false;
        } 
    }
    /**
     * 字符串长度验证
     * @author Sunles
     * @param string $str 验证字符串
     * @param int $len 验证的长度
     * @return boolean
     */
    static public function checkLen($str,$len){
        if(strlen($str) >= $len) return true;
        return false;
    }
    /**
     * 密码加密
     * @param unknown $pwd 原始密码
     * @return string
     */
    static public function md5PWD($pwd){
        return md5(substr(md5($pwd).md5('这是一个固定的字符串'),16,48));
    }
    /**
     * 字符串相等验证
     * @author Sunles
     * @param string $str1 对比字符串1
     * @param string $str2 对比字符串2
     * @return boolean
     */
    static public function isEquality($str1,$str2){
        if($str1 === $str2) return true;
        return false;
    }
    
    /**
     * 验证公司邮箱地址
     * @author Sunles
     * @return boolean
     */
    static public function isCompanyEmail($email){
        $RegExp='/^[a-z0-9][a-z\.0-9-_]+@zelinonline.com$/i';
        return preg_match($RegExp,$email)?true:false;
    }
    
    /**
     * 验证邮箱格式
     * @author Sunles
     * @return boolean
     */
    static public function isEmail($mail){
        $RegExp='/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
        return preg_match($RegExp,$mail)?true:false;
    }
    

     /**
     * 验证企业邮箱格式
     * @author Sunles
     * @return boolean
     */
    static public function isCompEmail($mail){
        $RegExp='/^[a-z0-9][a-z\.0-9-_]+@(?!163|qq)[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
        return preg_match($RegExp,$mail)?true:false;
    }
    

     /**
     * 验证公司网址
     * @author Sunles
     * @return boolean
     */
    static public function isUrl($url){
        // $RegExp='/http(s)?:\/\/([\w-]+\.)+[\w-]+(\.[\w-])+(\/[\w- .\/?%&=]*)?/';
       // $RegExp = '/^(http(s?):\/\/)?(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/';
	 
	   $RegExp = '/^(http(s)?:\/\/)?([\w-]+\.)+[\w-]+\.((com)|(cn)|(com\.cn)|(net)|(cc)|(xyz)|(org)|(org\.cn)|(site)|(pw)|(info)|(vip)|(xin)|(club)|(win)|(top)|(wang))(\/[\w- .\/?%&=]*)?$/';
        return preg_match($RegExp,$url)?true:false;
    }

     /**
     * 验证中文字符串
     * @author Sunles
     * @return boolean
     */
    static public function isChinese($str){
        $RegExp='/[^u4E00-u9FA5]/g/';
        return preg_match($RegExp,$str)?true:false;
    }

    /**
     * 验证身份证
     * @param unknown $idcard
     */
    static public function checkIdcard($idcard){
        // 只能是18位
        if(strlen($idcard)!=18){
            return false;
        }
        
        // 取出本体码
        $idcard_base = substr($idcard, 0, 17);
        
        // 取出校验码
        $verify_code = substr($idcard, 17, 1);
        
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        
        // 校验码对应值
        $verify_code_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        
        // 根据前17位计算校验码
        $total = 0;
        for($i=0; $i<17; $i++){
            $total += substr($idcard_base, $i, 1)*$factor[$i];
        }
        
        // 取模
        $mod = $total % 11;
        
        // 比较校验码
        if($verify_code == $verify_code_list[$mod]){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 短信发送函数
     */
    static public function sendSMS($mobile,$content){
        $http = C('SMS_HTTP');
        $data = array(
            'username' => C('SMS_USERNAME'),
            'password' => C('SMS_PASSWORD'),
            'mobile'   => $mobile,
            'content'  => $content
        );
        
        $row = parse_url($http);
        $host = $row['host'];
        $port = $row['port'] ? $row['port']:80;
        $file = $row['path'];
        while (list($k,$v) = each($data))
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return false;
        } else {
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            if(substr($receive['1'],0,2) == 'OK') return '';
            return iconv('gb2312','utf-8',$receive['1']);
        }
    }

    /**
     * 检测是否存在特殊字符
     */
    static public function checkSpecialCharacter($string){
        $RegExp = '/[\x{4e00}-\x{9fa5}\w[\(|\)|\/|\\]?]+$/u';
        return preg_match($RegExp,$string) ? true : false;
    }
	static public function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
}