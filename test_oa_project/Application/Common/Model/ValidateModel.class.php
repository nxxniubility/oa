<?php
namespace Common\Model;
use Think\Model;
/**
 * 基础模型
 * @author luoyu
 */
class ValidateModel extends Model {

	 public $regexArr=array(
         "mobile"=>"/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|^17[0-9]{1}[0-9]{8}$/",
         "tel"=>"/^0\d{2,3}-?\d{7,8}$/",
         "mtel"=>"/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/",
         "email"=>"/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i",
         "company_email"=>"/^[a-z0-9][a-z\.0-9-_]+@(?!163|qq)[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i",
         "url"=>"/^(http(s)?:\/\/)?([\w-]+\.)+[\w-]+\.((com)|(cn)|(com\.cn)|(net)|(cc)|(xyz)|(org)|(org\.cn)|(site)|(pw)|(info)|(vip)|(xin)|(club)|(win)|(top)|(wang))(\/[\w- .\/?%&=]*)?$/",
         "chinese"=>"/[^u4E00-u9FA5]/g/",
         "special_character"=>"/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-]+$/u",
         "float_int"=>"/^[0-9]*+.?+[0-9]*$/",
         "int"=>"/^[0-9]+$/u",
         "monogram"=>"/^[A-Za-z]+$/u"
	);
	 /**
     * 验证手机号码
     * @access public
     * @param   string $mobilephone 手机号码
     * @author  Sunlest
     * @return mixed
     */
    public function checkMobile($mobilephone){
        $mobilephone = trim($mobilephone);
        if(preg_match($this->regexArr["mobile"],$mobilephone)){
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
    public function checkTel($tel){
        $tel = trim($tel);        
        if(preg_match($this->regexArr["tel"],$tel)){
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
    public function checkLen($str,$len){
        if(strlen($str) >= $len) return true;
        return false;
    }
    /**
     * 字符串长度验证
     * @author Sunles
     * @param string $str 验证字符串
     * @param int $len 验证的长度
     * @return boolean
     */
    public function checkUtf8Len($str,$len){
        if(mb_strlen($str,'utf-8') >= $len) return true;
        return false;
    }
    /**
     * 字符串相等验证
     * @author Sunles
     * @param string $str1 对比字符串1
     * @param string $str2 对比字符串2
     * @return boolean
     */
    public function checkIsEquality($str1,$str2){
        if($str1 === $str2) return true;
        return false;
    }
    
    /**
     * 验证公司邮箱地址
     * @author Sunles
     * @return boolean
     */
    public function checkIsCompanyEmail($mail){
        return preg_match('/^[a-z0-9][a-z\.0-9-_]+@zelinonline.com$/i',$mail)?true:false;
    }
    
    /**
     * 验证邮箱格式
     * @author Sunles
     * @return boolean
     */
    public function checkIsEmail($mail){
        return preg_match($this->regexArr["email"],$mail)?true:false;
    }
    

     /**
     * 验证企业邮箱格式
     * @author Sunles
     * @return boolean
     */
    public function checkIsCompEmail($mail){
        return preg_match($this->regexArr['company_email'],$mail)?true:false;
    }
    

     /**
     * 验证公司网址
     * @author Sunles
     * @return boolean
     */
    public function checkIsUrl($url){
        return preg_match($this->regexArr['url'],$url)?true:false;
    }

     /**
     * 验证中文字符串
     * @author Sunles
     * @return boolean
     */
    public function checkIsChinese($str){
         return preg_match($this->regexArr['chinese'],$str)?true:false;
    }

    /**
     * 获取字符串中的数字
     * @access public
     * @param   string $str
     * @author  zgt
     * @return mixed
     */
    public function getStrInt($str){
        $str = trim($str);
        preg_match_all('/\d+/',$str,$arr);
        return $arr[0][0];
    }


    /**
     * 验证身份证
     * @param unknown $idcard
     */
    public function checkIdcard($idcard){
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
     * 验证特殊字符
     * author luoyu
     * @param $str 要验证的字符串
     */
    public function  checkSpecialCharacter($str)
    {
        return preg_match($this->regexArr['special_character'],$str) ? true : false;
    }


    /**
     * 只能为数字  xx.xx
     * author luoyu
     * @param $str 要验证的字符串
     */
    public function  checkFloatInt($str)
    {
        return preg_match($this->regexArr['float_int'],$str) ? true : false;
    }

    /**
     * 只能为正整数
     * author luoyu
     * @param $str 要验证的字符串
     */
    public function  checkInt($str)
    {
        return preg_match($this->regexArr['int'],$str) ? true : false;
    }

    /**
     * 只能为字母组合
     * @author  zgt
     * @return mixed
     */
    public function getStrMonogram($str){
        return preg_match($this->regexArr['monogram'],$str) ? true : false;
    }

    /**
     * 验证手机号或者固定电话
     * @param  string $str 要验证的字符串
     * @return bool      [description]
     * @author longguojun <[<email address>]>
     */
    public function checkMobileOrTel($str)
    {
        return $this->checkMobile($str) || $this->checkTel($str) ? true : false;
    }

}
