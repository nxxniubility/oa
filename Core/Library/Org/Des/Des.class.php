<?php
namespace Org\Des;
/**
* des 对称加解密
*/
class Des {
    private $key = '';
    private $cipher = MCRYPT_DES; //加解密算法
    private $modes = MCRYPT_MODE_ECB; //算法模式
    private $iv = ''; //初始化向量
    private $plus = '@plus@'; // + 号 替换符号
    /**
     * 密钥
     */
    public function __construct($key) {
        $this->key = $key;
        $this->iv = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher,$this->modes),MCRYPT_RAND);
    }
    /**
     * 加密
     */
    public function encrypt($input) {
        return $this->en_plus(base64_encode(mcrypt_encrypt($this->cipher,$this->key,$input,$this->modes,$this->iv)));
    }
    /**
     * 解密
     */
    public function decrypt($input) {
        return mcrypt_decrypt($this->cipher,$this->key,base64_decode($this->de_plus($input)),$this->modes,$this->iv);
    }
    public function de_plus($str) {
        return str_replace($this->plus, '+', $str);
    }
    public function en_plus($str) {
        return str_replace('+', $this->plus, $str);
    }
}
 
