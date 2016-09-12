<?php
namespace Org\Form;
/**
 * 表单验证类
 * @author Sunles
 * 
 */
class Checkform{
    /**
     * 验证手机号码
     * @access public
     * @param   string $mobilephone 手机号码
     * @author  Sunles
     * @return mixed
     */
    public function checkMobile($mobilephone){
        $mobilephone = trim($mobilephone);
        if(strlen($mobilephone)==11){
            if(preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|^17[0-9]{1}[0-9]{8}$/",$mobilephone)){
                return  $mobilephone;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 验证固定电话
     * @access public
     * @param   string $tel 固定电话
     * @author  zgt
     * @return mixed
     */
    public function checkTel($tel){
        $tel = trim($tel);
        if(preg_match("/^[0-9]*+\-{1}+[0-9]*$/",$tel)){
            return  $tel;
        }else{
            return false;
        }
    }

    /**
     * 验证数字
     * @access public
     * @param   string $int
     * @author  zgt
     * @return mixed
     */
    public function checkInt($int){
        $int = trim($int);
        if(preg_match("/^[0-9]*$/",$int)){
            return  $int;
        }else{
            return false;
        }
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

    public function checkLandline($mobilephone){
        $mobilephone = trim($mobilephone);
        if(preg_match("/0\d{2,3}-\d{5,9}|0\d{2,3}/",$mobilephone)){
            return  $mobilephone;
        }else{
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
     * 字符串相等验证
     * @author Sunles
     * @param string $str1 对比字符串1
     * @param string $str2 对比字符串2
     * @return boolean
     */
    public function isEquality($str1,$str2){
        if($str1 === $str2) return true;
        return false;
    }
    
    /**
     * 验证公司邮箱地址
     * @author Sunles
     * @return boolean
     */
    public function isCompanyEmail($email){
        $RegExp='/^[a-z0-9][a-z\.0-9-_]+@zelinonline.com$/i';
        return preg_match($RegExp,$email)?true:false;
    }
    
    /**
     * 验证邮箱格式
     * @author Sunles
     * @return boolean
     */
    public function isEmail($mail){
        $RegExp='/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
        return preg_match($RegExp,$mail)?true:false;
    }
    
    /**
     * 验证身份证
     * $num为身份证号码，$checkSex：1为男，2为女，不输入为不验证
     * @param
     */
    public function checkIdcard($id_card)
    {
        if(strlen($id_card) == 18)
        {
            return $this->idcard_checksum18($id_card);
        }
        elseif((strlen($id_card) == 15))
        {
            $id_card = $this->idcard_15to18($id_card);
            return $this->idcard_checksum18($id_card);
        }
        else
        {
            return false;
        }
    }
    // 计算身份证校验码，根据国家标准GB 11643-1999
    public function idcard_verify_number($idcard_base)
    {
        if(strlen($idcard_base) != 17)
        {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++)
        {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    // 将15位身份证升级到18位
    public function idcard_15to18($idcard){
        if (strlen($idcard) != 15){
            return false;
        }else{
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false){
                $idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9);
            }else{
                $idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . $this->idcard_verify_number($idcard);
        return $idcard;
    }
    // 18位身份证校验码有效性检查
    public function idcard_checksum18($idcard){
        if (strlen($idcard) != 18){ return false; }
        $idcard_base = substr($idcard, 0, 17);
        if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))){
            return false;
        }else{
            return true;
        }
    }
}