<?php
namespace System\Controller;
use Common\Controller\BaseController;
use \Org\Util\Tool;

class UeditorController extends BaseController{
	public function _initialize()
	{
		parent::_initialize();
		if(!session(C('USER_AUTH_KEY'))){
			$this->redirect(C('USER_AUTH_GATEWAY'), '请重新登录');
		}
	}

	private $ueditor_config=array(
	
		/*Oss配置参数*/
		"useOss"=>0,
		"additionalParams"=>array(),
		"ossUploadPath"=>"",
		"ossImageBaseUrl"=>"",
		/* 上传图片配置项 */
		"imageActionName"=>"uploadimage", /* 执行上传图片的action名称 ,如果是http://开头则是绝对路径上传  */
		"imageFieldName"=>"upfile", /* 提交的图片表单名称 */
		"imageMaxSize"=>2048000, /* 上传大小限制，单位B */
		"imageAllowFiles"=>array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), /* 上传图片格式显示 */
		"imageCompressEnable"=>true, /* 是否压缩图片,默认是true */
		"imageCompressBorder"=>1600, /* 图片压缩最长边限制 */
		"imageInsertAlign"=>"none", /* 插入的图片浮动方式 */
		"imageUrlPrefix"=>"", /* 图片访问路径前缀 */
		"imagePathFormat"=>"/Uploads/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", 
		
		      /* 上传保存路径,可以自定义保存路径和文件名格式 */
			  /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
			  /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
			  /* {time} 会替换成时间戳 */
			  /* {yyyy} 会替换成四位年份 */
			  /* {yy} 会替换成两位年份 */
			  /* {mm} 会替换成两位月份 */
			  /* {dd} 会替换成两位日期 */
			  /* {hh} 会替换成两位小时 */
			  /* {ii} 会替换成两位分钟 */
			  /* {ss} 会替换成两位秒 */
			  /* 非法字符 \ : * ? " < > | */
			  /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */
	
		/* 涂鸦图片上传配置项 */
		"scrawlActionName"=>"uploadscrawl", /* 执行上传涂鸦的action名称 */
		"scrawlFieldName"=>"upfile", /* 提交的图片表单名称 */
		"scrawlPathFormat"=>"/Uploads/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
		"scrawlMaxSize"=>2048000, /* 上传大小限制，单位B */
		"scrawlUrlPrefix"=>"", /* 图片访问路径前缀 */
		"scrawlInsertAlign"=>"none",
	
		/* 截图工具上传 */
		"snapscreenActionName"=>"uploadimage", /* 执行上传截图的action名称 */
		"snapscreenPathFormat"=>"/Uploads/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
		"snapscreenUrlPrefix"=>"", /* 图片访问路径前缀 */
		"snapscreenInsertAlign"=>"none", /* 插入的图片浮动方式 */
	
		/* 抓取远程图片配置 */
		"catcherLocalDomain"=>array("127.0.0.1", "localhost", "img.baidu.com"),
		"catcherActionName"=>"catchimage", /* 执行抓取远程图片的action名称 */
		"catcherFieldName"=>"source", /* 提交的图片列表表单名称 */
		"catcherPathFormat"=>"/Uploads/Uploads/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
		"catcherUrlPrefix"=>"", /* 图片访问路径前缀 */
		"catcherMaxSize"=>2048000, /* 上传大小限制，单位B */
		"catcherAllowFiles"=>array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), /* 抓取图片格式显示 */
	
		/* 上传视频配置 */
		"videoActionName"=>"uploadvideo", /* 执行上传视频的action名称 */
		"videoFieldName"=>"upfile", /* 提交的视频表单名称 */
		"videoPathFormat"=>"/Uploads/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
		"videoUrlPrefix"=>"", /* 视频访问路径前缀 */
		"videoMaxSize"=>102400000, /* 上传大小限制，单位B，默认100MB */
		"videoAllowFiles"=>array(
			".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
			".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"), /* 上传视频格式显示 */
	
		/* 上传文件配置 */
		"fileActionName"=>"uploadfile", /* controller里,执行上传视频的action名称 */
		"fileFieldName"=>"upfile", /* 提交的文件表单名称 */
		"filePathFormat"=>"/Uploads/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
		"fileUrlPrefix"=>"", /* 文件访问路径前缀 */
		"fileMaxSize"=>51200000, /* 上传大小限制，单位B，默认50MB */
		"fileAllowFiles"=>array(
			".png", ".jpg", ".jpeg", ".gif", ".bmp",
			".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
			".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
			".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
			".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
		), /* 上传文件格式显示 */
	
		/* 列出指定目录下的图片 */
		"imageManagerActionName"=>"listimage", /* 执行图片管理的action名称 */
		"imageManagerListPath"=>"/Uploads/ueditor/php/upload/image/", /* 指定要列出图片的目录 */
		"imageManagerListSize"=>20, /* 每次列出文件数量 */
		"imageManagerUrlPrefix"=>"", /* 图片访问路径前缀 */
		"imageManagerInsertAlign"=>"none", /* 插入的图片浮动方式 */
		"imageManagerAllowFiles"=>array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), /* 列出的文件类型 */
	
		/* 列出指定目录下的文件 */
		"fileManagerActionName"=>"listfile", /* 执行文件管理的action名称 */
		"fileManagerListPath"=>"/Uploads/ueditor/php/upload/file/", /* 指定要列出文件的目录 */
		"fileManagerUrlPrefix"=>"", /* 文件访问路径前缀 */
		"fileManagerListSize"=>20, /* 每次列出文件数量 */
		"fileManagerAllowFiles"=>array(
			".png", ".jpg", ".jpeg", ".gif", ".bmp",
			".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
			".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
			".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
			".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
		) /* 列出的文件类型 */
	
	);
     /**
     * 员工管理
     */
    public function index(){
       
	    date_default_timezone_set("Asia/chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/html; charset=utf-8");
		$action = remove_xss($_GET['action']);
		$oss_policy=$this->getOssSign('system_dir/images/');
		$this->ueditor_config["useOss"]=1;		
		$this->ueditor_config["additionalParams"]=array(
		                'key' => $oss_policy['dir'] ,
						'policy'=>$oss_policy["policy"],
						'OSSAccessKeyId'=> $oss_policy["accessid"], 
						'success_action_status' => '200', //让服务端返回200,不然，默认会返回204						
						'signature'=>$oss_policy["signature"],
						'Filename'=>''
		
		);
		$this->ueditor_config["imageFieldName"]="file";//oss必须设置成file
	    $this->ueditor_config["ossUploadPath"]=$oss_policy["host"];
		$this->ueditor_config["ossImageBaseUrl"]='http://'.C('ALIOSS_CONFIG.OSS_BUCKET').'.'.C('ALIOSS_CONFIG.OSS_IMG_DOMAIN');
		//print_r($this->ueditor_config);
		switch ($action) {
			case 'config':
				$result =  json_encode($this->ueditor_config);
				break;		
		 /* 上传图片 */
			case 'uploadimage':
			/* 上传涂鸦 */
			case 'uploadscrawl':
			/* 上传视频 */
			case 'uploadvideo':
			/* 上传文件 */
			case 'uploadfile':
				 $result =  $this->upload_do($this->ueditor_config);
				break;

			/* 列出图片 */
			case 'listimage':
				$result = include("action_list.php");
				break;
			/* 列出文件 */
			case 'listfile':
				$result = include("action_list.php");
				break;
		
			/* 抓取远程文件 */
			case 'catchimage':
				$result = include("action_crawler.php");
				break;
		
			default:
				$result = json_encode(array(
					'state'=> '请求地址出错'.$action
				));
				break;
		}
		
		/* 输出结果 */
		if (isset($_GET["callback"])) {
			if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
				echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
			} else {
				echo json_encode(array(
					'state'=> 'callback参数不合法'
				));
			}
		} else {
			echo $result;
		}
    }
    private function upload_do($ue_config)
	{
	    /* 上传配置 */
		$base64 = "upload";
		switch (htmlspecialchars($_GET['action'])) {
			case 'uploadimage':
				$config = array(
					"pathFormat" => $ue_config['imagePathFormat'],
					"maxSize" => $ue_config['imageMaxSize'],
					"allowFiles" => $ue_config['imageAllowFiles']
				);
				$fieldName = $ue_config['imageFieldName'];
				break;
			case 'uploadscrawl':
				$config = array(
					"pathFormat" => $ue_config['scrawlPathFormat'],
					"maxSize" => $ue_config['scrawlMaxSize'],
					"allowFiles" => $ue_config['scrawlAllowFiles'],
					"oriName" => "scrawl.png"
				);
				$fieldName = $ue_config['scrawlFieldName'];
				$base64 = "base64";
				break;
			case 'uploadvideo':
				$config = array(
					"pathFormat" => $ue_config['videoPathFormat'],
					"maxSize" => $ue_config['videoMaxSize'],
					"allowFiles" => $ue_config['videoAllowFiles']
				);
				$fieldName = $ue_config['videoFieldName'];
				break;
			case 'uploadfile':
			default:
				$config = array(
					"pathFormat" => $ue_config['filePathFormat'],
					"maxSize" => $ue_config['fileMaxSize'],
					"allowFiles" => $ue_config['fileAllowFiles']
				);
				$fieldName = $ue_config['fileFieldName'];
				break;
		}
		
		/* 生成上传实例对象并完成上传 */
		$up = new \Org\Util\Uploader($fieldName, $config, $base64);
		
		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
		 *     "url" => "",            //返回的地址
		 *     "title" => "",          //新文件名
		 *     "original" => "",       //原始文件名
		 *     "type" => ""            //文件类型
		 *     "size" => "",           //文件大小
		 * )
		 */
		
		/* 返回数据 */
		return json_encode($up->getFileInfo());	
	}
    
}