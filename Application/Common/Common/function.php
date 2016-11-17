<?php
/*
|--------------------------------------------------------------------------
| 公共函数库
|--------------------------------------------------------------------------
| createtime：2016-04-11
| updatetime：
| updatename：
*/


/**
 * 生成用户密码
 * @author echo
 * @return string
 */
function passwd($string)
{
    return md5(substr(md5($string) . md5('这是一个固定的字符串'), 16, 48));
}

/**
 * mysql存储html代码时 对html代码进行整理
 * @author Echo
 * @return string
 */
function regular_html($html)
{
    return base64_encode(str_replace(array(chr(8), chr(9), chr(10), chr(11), chr(12), chr(13), '  '), '', $html));
}

/**
 * 过滤逗号分割的数字字符串为逗号分割的纯数字字符串
 * param  $num_str 例：filter_num_str('1,2，3,a5，b0') == '1,2,3,0'
 */
function filter_num_str($num_str)
{
    return implode(',', array_unique(array_map('intval', explode(',', str_replace('，', ',', $num_str)))));
}

/**
 * 隐藏字符串的中间部分
 * 例：hideCenterStr('刘备'); // 刘*
 */
function hideCenterStr($str)
{
    preg_match_all("/./u", $str, $arr);
    $count = count($arr[0]);
    $hideNum = ceil($count / 3);
    foreach ($arr[0] as $key => $value) {
        if ($key >= ($count / 2 - $hideNum / 2) && $key < ($count / 2 + $hideNum / 2)) $arr[0][$key] = '*';
    }
    return implode('', $arr[0]);
}

/**
 * 获取客户端浏览器类型
 * @author Sunles
 * @param  string $glue 浏览器类型和版本号之间的连接符
 * @return string|array 传递连接符则连接浏览器类型和版本号返回字符串否则直接返回数组 false为未知浏览器类型
 */
function get_client_browser($glue = null)
{
    $browser = array();
    $agent = $_SERVER['HTTP_USER_AGENT']; //获取客户端信息
    /* 定义浏览器特性正则表达式 */
    $regex = array(
        'ie' => '/(MSIE) (\d+\.\d)/',
        'chrome' => '/(Chrome)\/(\d+\.\d+)/',
        'firefox' => '/(Firefox)\/(\d+\.\d+)/',
        'opera' => '/(Opera)\/(\d+\.\d+)/',
        'safari' => '/Version\/(\d+\.\d+\.\d) (Safari)/',
    );
    foreach ($regex as $type => $reg) {
        preg_match($reg, $agent, $data);
        if (!empty($data) && is_array($data)) {
            $browser = $type === 'safari' ? array($data[2], $data[1]) : array($data[1], $data[2]);
            break;
        }
    }
    return empty($browser) ? false : (is_null($glue) ? $browser : implode($glue, $browser));
}

/**
 * 加密用户手机号码
 * @author cq
 * @return string
 * @parameters  $data  要加密的数据
 * @parameters  $key   密钥
 */

function encryptPhone($data, $key)
{
    $key = md5($key);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}

/**
 * 解密用户手机号码
 * @author cq
 * @return string
 * @parameters  $data 加密数据
 * @parameters  $key  密钥
 */
function decryptPhone($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr
                ($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}

/**
 * 手机号码归属地
 * @param $phone 手机号码
 * @return json
 * @author zgt
 */
function phoneVest($phone)
{
    $str ='http://apis.juhe.cn/mobile/get?key=192a474a94f5f0176cd5cf1c5d43c34f&phone='.$phone;
    $output = file_get_contents($str);
    $output = json_decode($output,true);
    if($output['resultcode']==200){
        $result = $output['result'];
    }else{
        $result = '';
    }
    return $result;
//    $ch = curl_init();
//    $url = 'http://apis.baidu.com/apistore/mobilenumber/mobilenumber?phone='.$phone;
//    $header = array(
//        'apikey: '.C('API_STORE_KEY'),
//    );
//    // 添加apikey到header
//    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    // 执行HTTP请求
//    curl_setopt($ch , CURLOPT_URL , $url);
//    $res = curl_exec($ch);
//    $res = json_decode($res);print_r($res);
//    if($res->errNum==0){
//        $result = (array) $res->retData;
//    }else{
//        $result = '';
//    }
//    return $result;
}

/*配置文件写入*/
function insertConfig($filename, $config, $desc = '说明：')
{
    $config_file = CONF_PATH . $filename . '.php';
    $result = file_put_contents(
        $config_file,
        "<?php \nif(!defined('THINK_PATH')) exit('非法调用');\n//{$desc}\nreturn " . stripslashes(var_export($config, true)) . ";\n?>"
        , LOCK_EX
    );
    if ($result === false) return $result;
    return addConfig($filename);
}

/**
 * 配置文件更新写入
 * @param unknown $data 写入的数据
 * @param unknown $fileName 文件名
 * @return number
 * @author Sunles
 */
function updateConfig($fileName, $data , $path = CONF_PATH)
{
    $fileName = $path . $fileName . '.php';
    return file_put_contents($fileName, "<?php \nreturn " . stripslashes(var_export($data, true)) . ";", LOCK_EX);
}

/**
 * 加载配置文件 支持格式转换 仅支持一级配置
 * @param string $file 配置文件名
 * @param string $parse 配置解析方法 有些格式需要用户自己解析
 * @return array
 */
function loadconfig($file, $path = CONF_PATH, $parse = CONF_PARSE)
{
    $file = $path . $file . '.php';
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    switch ($ext) {
        case 'php':
            return include $file;
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml':
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if (function_exists($parse)) {
                return $parse($file);
            } else {
                E(L('_NOT_SUPPORT_') . ':' . $ext);
            }
    }
}

/**
 * 给系统配置文件添加配置文件扩展
 */
function addConfig($filename)
{
    //读取 config.php
    $config = file_get_contents(CONF_PATH . 'config.php');
    preg_match_all("~LOAD_EXT_CONFIG'.*?'(.*?)'~", $config, $matches);
    if (!isset($matches[1][0])) return false;
    if (stripos($matches[1][0], $filename) === false) {
        $config = str_replace($matches[1][0], $matches[1][0] . ',' . $filename, $config);
        $result = file_put_contents(CONF_PATH . 'config.php', $config, LOCK_EX);
        if ($result === false) return $result;
    }
    //更新配置到 大C函数
    C(load_config(CONF_PATH . $filename . '.php'));
    return true;
}


/* 
发送邮件
@author Nixx
*/
function SendMail($email, $psw, $address, $subject, $content)
{
    import("Vendor.PHPMailer.class#phpmailer", '', ".php");
    import("Vendor.PHPMailer.class#smtp", '', ".php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Port = 25;
    $mail->SMTPAuth = C('MAIL_AUTH');
    if ($email && $psw) {
        $mail->Username = $email;
        $mail->Password = $psw;
        $mail->From = $email;
        $mail->Host = C('MAIL_SMTP_B');//exmail.
        $mail->Port = 465;//smtp服务器的名称
        $mail->SMTPSecure = 'ssl';
    } else {
        $mail->Username = C('MAIL_LOGINNAME');
        $mail->Password = C('MAIL_PASSWORD');
        $mail->From = C('MAIL_ADDRESS');
        $mail->Host = C('MAIL_SMTP_A');
    }
    $mail->FromName = C('MAIL_NAME');
    $mail->CharSet = C('MAIL_CHARSET');
    $mail->IsHTML(true);

    $mail->AddAddress($address);
    $mail->Subject = $subject;
    $mail->Body = $content;

    if (!$mail->Send()) {
        return fail;
    } else {
        return success;
    }
}


/**
 * 导出Excel
 * @param  $filename 文件名
 * @param  $headarr  一维的数组
 * @param  $data     二维的数组
 * @param  $letter   列数
 * @return   导出到浏览器 弹出下载框
 * @author   Nxx
 */
function outExecl($filename, $headarr, $data, $letter, $cache_type = 0)
{
    $ret = ini_set("memory_limit", "320M");
    if (!$ret) $this->error('配置错误！');
    import("Org.Util.PHPExcel");
    import("Org.Util.PHPExcel.Writer.Excel2007");
    import("Org.Util.PHPExcel.Style.Alignment.php");
    import("Org.Util.PHPExcel.IOFactory.php");
    //对数据进行检验
    if (!is_array($headarr) || empty($headarr)) die("headarr must be a array");
    if (!is_array($data) || !is_array($data[0])) die("data must be a two dimension array");
    if (count($headarr) > 26) die('Data item is more than 26');
    if (count($headarr) != count($data[0])) die('Data item shortage');
    //检查转码函数
    if (!function_exists('mb_convert_encoding')) die('mb_convert_encoding empty');
    $from_encoding = array('UTF-8', 'GBK');
    $to_encoding = 'UTF-8';
    //检查文件名
    if (empty($filename)) die("file name must be a string");
    $filename = mb_convert_encoding($filename, $to_encoding, $from_encoding);
    $filename .= '_' . date('Y-m-d-H-i-s') . '.xls';
    //创建PHPExcel对象
    $objPHPExcel = new \PHPExcel();
    import('Org.Util.PHPExcel.CachedObjectStorageFactory.php');
    import('Org.Util.PHPExcel.Settings.php');
    $cache_type_arr = array(
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory,
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized,
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip,
        \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM,
        \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp,
        \PHPExcel_CachedObjectStorageFactory::cache_to_memcache
    );
    $ret = \PHPExcel_Settings::setCacheStorageMethod($cache_type_arr[$cache_type]);
    if (!$ret) $this->error('PHPExcel 缓存方式无效！');
    //设置excel属性
    $objPHPExcel->getProperties()->setCreator(mb_convert_encoding(session('realname'), $to_encoding, $from_encoding))->setTitle($filename);
    //设置默认字体和大小
    $objPHPExcel->getDefaultStyle()->getFont()->setName(mb_convert_encoding('宋体', $to_encoding, $from_encoding));
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
    //设置活动 sheet
    $objPHPExcel->setActiveSheetIndex(0);
    //得到活动 sheet
    $objActSheet = $objPHPExcel->getActiveSheet();
    //设置表头
    $key = ord('A');
    foreach ($headarr as $v) {
        $colum = chr($key) . '1';
        $objActSheet->setCellValue($colum, mb_convert_encoding($v, $to_encoding, $from_encoding));
        $objActSheet->getStyle($colum)->getFont()->setBold(true);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        ++$key;
    }

    //写入数据
    $column = 2;

    foreach ($data as $key => $rows) { //行写入
        $span = ord("A");
        foreach ($rows as $keyName => $v) {// 列写入
            $j = chr($span) . $column;
            $objActSheet->setCellValue($j, mb_convert_encoding($v, $to_encoding, $from_encoding));
            $objActSheet->getStyle($j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $span++;
        }
        $column++;
    }
    // 设置 header 头
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Type: application/vnd.ms-excel");
    if (preg_match("/msie/", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
        header('Content-Disposition: attachment; filename=' . rawurlencode($filename) . ';');
    } else if (preg_match("/firefox/", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
        header('Content-Disposition: attachment; filename*=' . $filename . ';');
    } else {
        header('Content-Disposition: attachment; filename=' . $filename . ';');
    }
    header("Content-Transfer-Encoding: binary");
    // 输出 数据
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // Excel2007 不兼容
    $objWriter->save('php://output');
    exit;
}


/**
 * 导出Excel
 * @param  $filename 文件名
 * @param  $headarr  一维的数组
 * @param  $data     二维的数组
 * @param  $letter   列数
 * @return   打包下载excel
 * @author   Nxx
 */
function outExecls($filename, $headarr, $data, $letter, $cache_type = 0)
{
    $ret = ini_set("memory_limit", "1024M");
    if (!$ret) $this->error('配置错误！');
    import("Org.Util.PHPExcel");
    import("Org.Util.PHPExcel.Writer.Excel2007");
    import("Org.Util.PHPExcel.Style.Alignment.php");
    import("Org.Util.PHPExcel.IOFactory.php");
    import("Org.Util.PHPExcel.Shared.ZipArchive.php");

    //对数据进行检验
    if (!is_array($headarr) || empty($headarr)) die("headarr must be a array");
    if (!is_array($data) || !is_array($data[0])) die("data must be not null");
    if (count($headarr) > 26) die('Data item is more than 26');
    if (count($headarr) != count($data[0])) die('Data item shortage');

    //检查转码函数
    if (!function_exists('mb_convert_encoding')) die('mb_convert_encoding empty');
    $from_encoding = array('UTF-8', 'GBK');
    $to_encoding = 'UTF-8';

    //检查文件名
    if (empty($filename)) die("file name must be a string");
    $filename = mb_convert_encoding($filename, $to_encoding, $from_encoding);
    $filename = "{$filename}" . '_' . date('YmdHis') . '_' . rand(100000, 999999) . '.xls';

    //创建PHPExcel对象
    $objPHPExcel = new \PHPExcel();
    import('Org.Util.PHPExcel.CachedObjectStorageFactory.php');
    import('Org.Util.PHPExcel.Settings.php');
    $cache_type_arr = array(
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory,
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized,
        \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip,
        \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM,
        \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp,
        \PHPExcel_CachedObjectStorageFactory::cache_to_memcache
    );
    $ret = \PHPExcel_Settings::setCacheStorageMethod($cache_type_arr[$cache_type]);
    if (!$ret) $this->error('PHPExcel 缓存方式无效！');
    //设置excel属性
    $objPHPExcel->getProperties()->setCreator(mb_convert_encoding(session('realname'), $to_encoding, $from_encoding))->setTitle($filename);
    //设置默认字体和大小
    $objPHPExcel->getDefaultStyle()->getFont()->setName(mb_convert_encoding('宋体', $to_encoding, $from_encoding));
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
    //设置活动 sheet
    $objPHPExcel->setActiveSheetIndex(0);
    //得到活动 sheet
    $objActSheet = $objPHPExcel->getActiveSheet();
    //设置表头
    $key = ord('A');
    foreach ($headarr as $v) {
        $colum = chr($key) . '1';
        $objActSheet->setCellValue($colum, mb_convert_encoding($v, $to_encoding, $from_encoding));
        $objActSheet->getStyle($colum)->getFont()->setBold(true);
        $objActSheet->getStyle($colum)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        ++$key;
    }
    //写入数据
    $column = 2;
    foreach ($data as $key => $rows) { //行写入
        $span = ord("A");
        foreach ($rows as $keyName => $v) {// 列写入
            $j = chr($span) . $column;
            $objActSheet->setCellValue($j, mb_convert_encoding($v, $to_encoding, $from_encoding));
            $objActSheet->getStyle($j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $span++;
        }
        $column++;
    }
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); // Excel2007 不兼容
    $objWriter->save(BASE_PATH . "/Uploads/excel/{$filename}");
    return $filename;


}

function create_zip($files = array(), $destination = '', $overwrite = false)
{

    if (file_exists($destination) && !$overwrite) {
        return false;
    }

    $valid_files = array();
    if (is_array($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }

    if (count($valid_files)) {
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        foreach ($valid_files as $k => $file) {
            $orderfile = ($k + 1) . substr($file, strrpos($file, '.'));
            $zip->addFile($file, $orderfile);
        }
        $zip->close();
        return file_exists($destination);
    } else {
        return false;
    }
}

function importExecl($file)
{
    if (!function_exists('mb_convert_encoding')) return array('error' => 1, 'message' => 'mb_convert_encoding empty');
    if (!file_exists($file)) return array('error' => 2, 'message' => '找不到Execl文件');
    $pathinfo_file = pathinfo($file);
    import("Org.Util.PHPExcel");
    $PHPExcel = new \PHPExcel();
    if ($pathinfo_file['extension'] == 'xls') {
        import("Org.Util.PHPExcel.Reader.Excel5");
        $PHPReader = new \PHPExcel_Reader_Excel5();
    } elseif ($pathinfo_file['extension'] == 'xlsx') {
        import("Org.Util.PHPExcel.Reader.Excel2007");
        $PHPReader = new \PHPExcel_Reader_Excel2007();
    } else {
        return array('error' => 3, 'message' => '文件类型不正确!');
    }
    if (!$PHPReader->canRead($file)) return array('error' => 4, 'message' => '文件无法读取!');
    $PHPExcel = $PHPReader->load($file);//载入文件
    $currentSheet = $PHPExcel->getSheet(0);//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
    $allColumn = $currentSheet->getHighestColumn();//获取总列数
    $allRow = $currentSheet->getHighestRow();//获取总行数
    $from_encoding = array('UTF-8', 'GBK');
    $to_encoding = 'UTF-8';
    $result = array();
    for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
        //从哪列开始，A表示第一列
        for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
            //数据坐标
            $address = $currentColumn . $currentRow;
            //读取到的数据
            $tmp = $currentSheet->getCell($address)->getValue();
            //富文本转换字符串
            if ($tmp instanceof PHPExcel_RichText) $tmp = $tmp->__toString();
            //转码
            $tmp = mb_convert_encoding($tmp, $to_encoding, $from_encoding);
            //去除空格
            $tmp = trim($tmp);
            //判断是否为空
            // if($currentColumn == 'A' && empty($tmp)) continue 2; 
            $result[$currentRow][$currentColumn] = $tmp;
        }
    }
    return $result;
}

/**
 * 转码函数
 */
function auto_iconv($str, $to_encoding = 'UTF-8')
{
    $from_encoding = mb_detect_encoding($str); //源编码
    $from_encoding = strtoupper($from_encoding);
    $to_encoding = strtoupper($to_encoding); //目标编码
    if ($from_encoding == $to_encoding) return $str;
    if (function_exists('mb_convert_encoding')) return mb_convert_encoding($str, $to_encoding, $from_encoding);
    if ($to_encoding == 'utf-8') $to_encoding = 'utf-8//IGNORE';
    return iconv($from_encoding, $to_encoding, $str);
}

/*
* 输出文件流
*/
function  output_file_stream($filepath)
{
    $filepath = BASE_PATH . $filepath;
    if (!file_exists($filepath)) {
        echo '文件不存在！';
    } else {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    }
    exit;
}

/**
 * 从字符串中获取数字
 * @param  $str  包含数字的字符串
 * @author cq
 */
function getNumberFromString($str)
{
    $str = trim($str);
    $patterns = "/\d+/"; //第一种
    preg_match_all($patterns, $str, $arr);
    return $arr[0];
}
