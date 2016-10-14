<?php
/*
* Session服务接口
* @author luoyu
*
*/
namespace Common\Service;

use Common\Service\BaseService;

class RdsService extends BaseService {
	
	//不检查数据库，虚拟表
    protected $autoCheckFields = false;
	
    //初始化
    public function _initialize() {
     
    }
	private function createQueryString($data)
	{
		$param=array(
			"TimeStamp"=>gmdate("Y-m-d", time()).'T'.gmdate("H:i:s", time()).'Z',
			"Format"=>"JSON",
			"AccessKeyId"=> C('ALIRDS_CONFIG.RDS_ACCESS_ID'),
			"SignatureMethod"=>"HMAC-SHA1",
			"SignatureNonce"=>random(16,"0123456789zxcvbnmasdfghjklqwertyuiopZXCVBNMASDFGHJKLQWERTYUIOP"),
			"Version"=>"2014-08-15",
			"SignatureVersion"=>"1.0",	
			"DBInstanceId"=>C('ALIRDS_CONFIG.RDS_DBINSTANCEID')
		);
		$param=array_merge($param,$data);
		ksort($param);
		$query="";
		foreach($param as $k=>$v)
		{
		   $query.='&'.urlencode($k).'='.urlencode($v);	
		}
		$query=trim($query,"&");
		$query=str_replace('+','%20', $query);
		$query=str_replace('*','%2A', $query);
		$query=str_replace('%7E','~', $query);
		
		$string_to_sign="GET&%2F&".urlencode($query);
		return $query."&Signature=".urlencode(base64_encode(hash_hmac('sha1', $string_to_sign, C('ALIRDS_CONFIG.RDS_ACCESS_KEY')."&", true)));
	}
    /*
	* 获取慢查询日志
	* @luoyu
	* @param db_name 数据库名称
	* @param start_time 开始日期，格式：yyyy-MM-ddZ
	* @param end_time 结束日期，格式：yyyy-MM-ddZ
	* 调用示例：
	  $ret=D('Rds','Service')->getSlowQueryLog(array('start_time'=>'2016-09-21T15:00:00Z','end_time'=>'2016-09-25T15:00:00Z','db_name'=>'didazp'));
	*/
	public function getSlowQueryLog($param)
	{
	   $data=array(
			
			"Action"=>"DescribeSlowLogs",
			"StartTime"=>$param['start_time'],//格式：yyyy-MM-ddZ
			"EndTime"=>$param['end_time'],//格式：yyyy-MM-ddZ
			"DBName"=>$param['db_name'],
			"SortKey"=>$param['sort']?$param['sort']:"TotalQueryTimes",//排序依据，取值：TotalExecutionCounts:总执行次数最多;TotalQueryTimes:总执行时间最多;TotalLogicalReads:总逻辑读最多;TotalPhysicalReads:总物理读最多;
			"PageSize"=>$param['pagesize']?$param['pagesize']:30,
			"PageNumber"=>$param['page_number']?$param['page_number']:1
			
		);
		
	    $url_query=$this->createQueryString($data);
		$response=file_get_contents(C('ALIRDS_CONFIG.RDS_URL')."?".$url_query);
		if($response)$response=json_decode($response);
		return array("code"=>0,"msg"=>"","data"=>$response);
	}
    /*
	* 获取慢查询日志
	* @luoyu
	* @param start_time 开始日期，示例：2011-05-30T12:10Z
	* @param end_time 结束日期,，示例：2011-05-31T12:10Z
	* 调用示例：
	  $ret=D('Rds','Service')->getErrorLog(array('start_time'=>'2016-09-21T15:00Z','end_time'=>'2016-09-25T15:00Z'));
	*/
	public function getErrorLog($param)
	{
	   $data=array(
			
			"Action"=>"DescribeErrorLogs",
			"StartTime"=>$param['start_time'],//格式：yyyy-MM-ddTH:iZ
			"EndTime"=>$param['end_time'],//格式：yyyy-MM-ddTH:iZ			
			"PageSize"=>$param['pagesize']?$param['pagesize']:30,
			"PageNumber"=>$param['page_number']?$param['page_number']:1
			
		);
		
	    $url_query=$this->createQueryString($data);
		$response=file_get_contents(C('ALIRDS_CONFIG.RDS_URL')."?".$url_query);
		if($response)$response=json_decode($response);
		return array("code"=>0,"msg"=>"","data"=>$response);
	}
}