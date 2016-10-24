<?php
/*
* 阿里接口
* @author luoyu
*
*/
namespace Common\Service;
use Common\Service\BaseService;
use Extend\Alyun\Postoss;

class AliOssService extends BaseService
{

    //初始化
    public function _initialize()
    {
        parent::_initialize();
    }

    /*
   | --------------------------------------------------------
   |  阿里云OOS 直传签名获取
   | --------------------------------------------------------
   */
    public function osspolicy($request)
    {
        //1-头像  2-专题页
        $bid = isset($request['bid'])?$request['bid']:1;
        $name = isset($request['name'])?$request['name']:null;
        $size = isset($request['size'])?$request['size']:'2000000';

        $fileType = strtolower( substr( $name,strrpos( $name,'.' ) + 1 ) );
        //获取资源文件夹
        $alioss_user_dir = C('ALIOSS_CONFIG.ALIOSS_USER_DIR');
        $bucketDir = $alioss_user_dir[$bid];
        //获取key名称
        $newkey = date('Ymdhis').'-'.rand(0,999);
        $policy = $this->policy($newkey.'.'.$fileType, $bucketDir, $size);

        return array('code'=>'0', 'data'=>$policy);
    }

    protected function policy($filename, $filebucket, $filesize='1048576000')
    {
        $ossid = C('ALIOSS_CONFIG.OSS_ACCESS_ID');
        $osskey = C('ALIOSS_CONFIG.OSS_ACCESS_KEY');
        $osshost = C('ALIOSS_CONFIG.OSS_DOMAIN');
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        //文件夹
        $dir = $filebucket;

        //最大文件大小.用户可以自己设置
        $condition = array(0=>'content-length-range', 1=>0, 2=>(int)$filesize);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;

        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);

        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $osskey, true));

        $response['keyid'] = $ossid;
        $response['host'] = $osshost;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        //$response['callback'] = '$base64_callback_body';
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        $response['name'] = $filename;

        return $response;
    }

    protected function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        //$mydatetime = new DateTime($dtStr);
        //$expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($dtStr, '+');
        $expiration = substr($dtStr, 0, $pos);
        return $expiration."Z";
    }
}