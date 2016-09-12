<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace  Extend\TagLib;
use Think\Template\TagLib;
/*
 * 自定义标签
 * @author  luoyu
 * 
 */
class Zelin extends TagLib {

        protected $tags=array(
		
		    'js'=>array('attr'=>'src','close'=>0),
			'css'=>array('attr'=>'src','close'=>0)
			
		);
		
		//js自动压缩合并，生成缓存文件
		public function _js($attr,$content){
		    $src =$attr['src'];
		    if(APP_DEBUG){//调试模式
				$files=explode(",",$src);
				$str="";
		    	foreach($files as $k=>$file){
					$str.="<script  type=\"text/javascript\"  src=\"/Public/js/".$file.".js"."\"></script>\r\n";
				}
			}else{
				$dir=BASE_PATH."/Public/js_cache";
				if(!is_dir($dir)) mkdir($dir, 0777);
				$path=$dir."/".md5($src).".js";
				if(!file_exists($path)){
					$this->minifyJS($src,$dir);
				}
				$str="<script  type=\"text/javascript\"  src=\"/Public/js_cache/".md5($src).".js"."\"></script>";
			}
			return $str; 
		}
		
		//JS压缩
		private function minifyJS($src,$dir){
			$path=$dir."/".md5($src).".js";
		    Vendor('Minify.JSMin',VENDOR_PATH,'.php');	
			$files=explode(",",$src);
			$min_contents="";
			foreach($files as $k=>$file)
			{
				$filename = BASE_PATH.'/Public/js/'.$file.".js";
				if(file_exists($filename))
				{
					$read_handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'
					//通过filesize获得文件大小，将整个文件一下子读到一个字符串中
					$content =(fread($read_handle, filesize ($filename)));		
					$min_contents =$min_contents.";".\JSMin::minify($content);
					fclose($read_handle);
					
				}else{
					echo "the '".$filename.".js' file is not exist!";
					exit;
				}
			}
			$write_handle= fopen($path , "w");//读取二进制文件时，需要将第二个参数设置成'rb'
			fwrite($write_handle ,$min_contents);
			fclose($write_handle);
				
		}
		
		//css自动压缩合并，生成缓存文件
		public function _css($attr,$content){
	    	 $src =$attr['src'];
		     if(APP_DEBUG){//调试模式
				$files=explode(",",$src);
				$str="";
		    	foreach($files as $k=>$file){
					$str.="<link type=\"text/css\"  href=\"/Public/css/".$file.".css"."\" rel=\"stylesheet\" />";	
				}
			 }else{
				$dir=BASE_PATH."/Public/css_cache";
				if(!is_dir($dir)) mkdir($dir, 0777);
				$path=$dir."/".md5($src).".css";
				if(!file_exists($path)){
					$this->minifyCSS($src,$dir);
				}
				$str="<link type=\"text/css\"  href=\"/Public/css_cache/".md5($src).".css"."\" rel=\"stylesheet\" />";	
			 }
			return $str; 
		}
		
		//css压缩
		private function minifyCSS($src,$dir)
		{
			$path=$dir."/".md5($src).".css";
			Vendor('Minify.CSSmin',VENDOR_PATH,'.php');	
			$CSSmin=new \CSSmin();
			$files=explode(",",$src);
			$min_contents="";
			foreach($files as $k=>$file)
			{
				$filename = BASE_PATH.'/Public/css/'.$file.".css";
				if(file_exists($filename))
				{
					$read_handle = fopen($filename, "r");//读取二进制文件时，需要将第二个参数设置成'
					//通过filesize获得文件大小，将整个文件一下子读到一个字符串中
					$content =(fread($read_handle, filesize ($filename)));		
					$min_contents =$min_contents.$CSSmin->run($content);
					fclose($read_handle);
				}else{
					echo "the '".$filename.".css' file is not exist!";
					exit;
				}
			}
			$write_handle= fopen($path , "w");//读取二进制文件时，需要将第二个参数设置成'rb'
			fwrite($write_handle ,$min_contents);
			fclose($write_handle);		
		}
}