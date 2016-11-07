<?php
namespace Cmd\Controller;
use Common\Controller\BaseController;
class MailController extends BaseController {
    var $UserDB;
    var $userinfoDB;  
    var $mail_obj;
   
    var $max_read;
    var $is_del;
    var $msg="";
    public function _initialize(){
        parent::_initialize();
		set_time_limit(0);
        $this->userinfoDB = M('user_info');
        $this->UserDB = D('User');      
       
        // 连接到邮件服务器
        import("Org.Mail.mail");
        $mail = C('mail');
        $this->mail_obj = new \receiveMail(
            $mail['username'],
            $mail['password'],
            $mail['emailAddress'],
            $mail['mailserver'],
            $mail['servertype'],
            $mail['port'],
            false
        );
        $this->mail_obj->connect();
        //设置每次启动脚本读取的邮件最大数量
        $this->max_read = $mail['maxread'];
        $this->is_del = $mail['is_del'];
        
        //header("Content-type: text/html; charset=utf-8");
    }
    public function index(){
		
        //判断邮箱中是否还有邮件
		$tot = $this->mail_obj->getTotalMails();
		
        //返回信息给 bash shell
        if($tot == 0) {
            echo 0;
            exit;
        }
		
		
        //判断本次需要读取的邮件数量
        //开始循环读取
		for($i=$tot; $i > 0 ; $i--) {
		
            if($this->max_read <= 0) {
                $this->logs();
                $this->mail_obj->close_mailbox();   //Close Mail Box
                echo 1;
                exit;
            }
            $this->max_read = $this->max_read -1;
            $this->msg .= "start {$i}\r\n";
            //获取邮件的头部信息
            $headerinfo = $this->mail_obj->get_imap_header($i);
            //删除已经阅读的邮件
            if(!($this->mail_obj->isUnread($headerinfo))) {
                $header_info = $this->mail_obj->get_header_info($headerinfo);
                $this->delete_mail($i);
                $this->msg .= '邮件已读'."\r\n";
                continue;
            }
            //读取邮件时间
            $header_info = $this->mail_obj->get_header_info($headerinfo);
          
			
            //读取邮件正文
			$subject = trim(auto_iconv($this->mail_obj->getBody($i)));
			
			if(empty($subject)) {
                $this->delete_mail($i);
                $this->msg .= '没有正文'."\r\n";
                continue;
            }
            //判断简历类型 获取匹配规则
            $pattern = false;
			$site="";
			if(stripos($subject,'58.com') !== false) {
				$pattern = $this->pattern('_58');
				$site="_58";
			}
            if(stripos($subject,'51job.com') !== false) {
				$pattern = $this->pattern('_51job');
				$site="_51job";
			}
            if(stripos($subject,'cjolimg.com') !== false) {
				$pattern = $this->pattern('_cjol');
				$site="_cjol";
			}
            if(stripos($subject,'zhaopin.com') !== false) {
				$pattern = $this->pattern('_zhaopin');
				$site="_zhaopin";
			}
            if(stripos($subject,'ganji.com') !== false) {
				$pattern = $this->pattern('_ganji');
				$site="_ganji";
			}
            if(stripos($subject,'chinahr.com') !== false) {
				$pattern = $this->pattern('_chinahr');
				$site="_chinahr";
			}
            if(!$pattern) {
                $this->delete_mail($i); 
                $this->msg .= '没有匹配规则'."\r\n";
                continue;
            }
            //根据匹配规则获取入库字段的信息
			
			
			$data = $this->get_data($subject,$pattern['nickname'],$site);		
			$this->get_other_data($data,$subject,$site);
		
            //既没手机号 又没 qq号 的略过
            if(empty($data['zl_user']['username']) && empty($data['zl_user']['qq'])) {
                $this->delete_mail($i);
                $this->msg .= '没手机号码 没QQ'."\r\n";
                continue;
            }
			$data['zl_user_info']['channel_id'] = $pattern['channel_id'];
            //判断简历是否存在
            $isSameUsername = $this->UserDB->getFind(array('username'=>$data['zl_user']['username']));
            $isSameUserqq = $this->UserDB->getFind(array('username'=>$data['zl_user']['qq']));
            $isSameUsertel = $this->UserDB->getFind(array('username'=>$data['zl_user']['tel']));
            $save['createupdatetime'] = time();
            $protectTime = C('FIELD_STATUS.UPDATE_PROTECT')*24*3600;
            if(!empty($isSameUsername)) {
                //如果创建时间+保护时间小于当前时间，则更新
                if (($isSameUsername['createtime'] + $protectTime) < $save['createupdatetime']){
                    $update = $this->UserDB->editData($save, $isSameUsername['user_id']);
                }
                $this->delete_mail($i);
                $this->msg .= '当前简历已经入库过'."\r\n";
                continue;
            }elseif (!empty($isSameUserqq)) {
                //如果创建时间+保护时间小于当前时间，则更新
                if (($isSameUserqq['createtime'] + $protectTime) < $save['createupdatetime']){
                    $update = $this->UserDB->editData($save, $isSameUserqq['user_id']);
                }
                $this->delete_mail($i);
                $this->msg .= '当前简历已经入库过'."\r\n";
                continue;
            }elseif (!empty($isSameUsertel)) {
                //如果创建时间+保护时间小于当前时间，则更新
                if (($isSameUsertel['createtime'] + $protectTime) < $save['createupdatetime']){
                    $update = $this->UserDB->editData($save, $isSameUsertel['user_id']);
                }
                $this->delete_mail($i);
                $this->msg .= '当前简历已经入库过'."\r\n";
                continue;
            }
            //加上邮件发送时间
            $data['zl_user']['jointime'] = $header_info['date'];
            //入库
            $this->UserDB->startTrans(); 
            if ($data['zl_user']['wantsalary']>=C('FIELD_STATUS.SET_ATTITUDE')) {
                $data['zl_user']['attitude_id'] = 13;
            }
            $user_id = $this->UserDB->add($data['zl_user']);
            if(empty($user_id)) {
                $this->UserDB->rollback(); //插入失败，回滚
                $this->msg .= '插入 zl_user 失败'."\r\n";
                continue;
            }
            $data['zl_user_info']['user_id'] = $user_id;
            $userinfo_id = $this->userinfoDB->add($data['zl_user_info']);
            if(empty($userinfo_id)) {
                $this->UserDB->rollback(); //插入失败，回滚
                $this->msg .= '插入 zl_user_info 失败'."\r\n";
                continue;
            }
			//添加转出人 出库量
			$dataLog['operattype'] = 1;
			$dataLog['operator_user_id'] = 0;
			$dataLog['user_id'] = $user_id;
			$dataLog['logtime'] = time();
			D('Data', 'Service')->addDataLogs($dataLog);
            $this->UserDB->commit();
            //读取完毕删除 
            $this->delete_mail($i);
		}
		
    }
    public function logs() {
        $date = date('Y-m-d');
        file_put_contents(BASE_PATH.'/../Shell/log/mail/'.$date.'_mail_log.txt',$this->msg,FILE_APPEND);
    }
    /**
     * 删除邮件
     */
    public function delete_mail($i) {
        if($this->is_del) {
            if(!$this->mail_obj->delete($i)) {
                //删除失败，标记成已读
                $this->mail_obj->mail_mark_read($i);
            }
        }
    }
    /**
     * 返回各个简历平台的匹配规则
     */
    public function pattern($key) {
        $array = array();
        $array['_58'] = array();
        $array['_58']['nickname'] = "~normal[\w\W]*?>([\w\W]*?)<span~";
        $array['_58']['channel_id'] = 26;

        $array['_51job'] = array();
        $array['_51job']['nickname'] = "~<strong.*?>(.*)?</strong>~";
        $array['_51job']['channel_id'] = 29;
		

        $array['_zhaopin'] = array();
        $array['_zhaopin']['nickname'] = "~line-height:50px.*?>(.*?)<~";
        $array['_zhaopin']['channel_id'] = 27;

        $array['_cjol'] = array();
        $array['_cjol']['nickname'] = "~rowspan.*?>(.*?)<~";
        $array['_cjol']['channel_id'] = 28;


        $array['_ganji'] = array();
        $array['_ganji']['nickname'] = "~来自[\w\W]*?>([\w\W]*?)</span~";
        $array['_ganji']['channel_id'] = 492;

        $array['_chinahr'] = array();
        $array['_chinahr']['nickname'] = "~class=\"td_name[\w\W]*?>([\w\W]*?)</td>~";
        $array['_chinahr']['channel_id'] = 491;

        return isset($array[$key]) ? $array[$key] : false;
    }
    /**
     * 根据匹配规则提取字段信息
     */
    private function get_data($subject,$nick_name_pattern,$site) {
    	$zl_user = $zl_user_info = array();
		$username=$this->get_mobile($subject);
    	$zl_user['username'] =encryptPhone($username,C('PHONE_CODE_KEY'));
        $zl_user['email'] = $this->get_email($subject);
        $tmp = str_replace('@qq.com','',$zl_user['email']);
        if(is_numeric($tmp)) $zl_user['qq'] = $tmp;
        preg_match_all($nick_name_pattern, $subject, $matches);
        $zl_user['nickname'] = $zl_user['realname'] = isset($matches[1][0]) ? $matches[1][0] : '';
		
        if(stripos($subject,'男') != false) {
            $zl_user_info['sex'] = 1;
        }elseif(stripos($subject,'女') != false) {
            $zl_user_info['sex'] = 2;
        }else{
            $zl_user_info['sex'] = 0;
        }		
        $zl_user['status'] = 160;//回库
        $zl_user['createtime'] = $zl_user['updatetime'] =$zl_user['allocationtime'] =$zl_user['lastvisit'] =time();
        $zl_user['createip'] = get_client_ip();
        $zl_user['channel_id'] = 3;
		$zl_user['zone_id'] = 6;
        $zl_user['createuser_id'] = 0;
		$zl_user['updateuser_id'] = 0;
		$zl_user['infoquality'] = 4 ;
		$zl_user['system_user_id'] = 0;
		$reApi = phoneVest($username);
        if(!empty($reApi)) {
                $zl_user['phonevest'] = $reApi['city'];
        }

		if($site=="_58") {
				 $zl_user['channel_id'] = 26;
				
		}
		if($site=="_51job") {
			$zl_user['channel_id'] = 29;
		}
		if($site=="_cjol") {
			$zl_user['channel_id'] = 28;
			
		}
		if($site=="_zhaopin") {
			$zl_user['channel_id'] =27;
		}
		if($site=="_ganji") {
			$zl_user['channel_id'] = 492;
		}
		 if($site=="_chinahr") {
			$zl_user['channel_id'] = 491;
		}
        return array('zl_user'=>$zl_user,'zl_user_info'=>$zl_user_info);
		
    }
	private function get_other_data(&$user_data,$subject,$site)
	{
	
		$subject_nohtml=strip_tags($subject);
		preg_replace('/(\r\r)|\r|\n/','',$subject);
		$array['_58'] = array();
        $array['_58']['wantposition'] =array(1,"zl_user_info","/期望职位：(.*)(期望薪资：){0,1}/",1);
        $array['_58']['age']=array(0,"zl_user_info","/>（(男|女)，([0-9]{1,3})岁）<\/span>/",2);
		$array['_58']['workyear']=array(1,"zl_user_info","/([0-9]{0,2}\-{0,1}[0-9]{0,2})年工作经验/",1);
		$array['_58']['education_id']=array(0,"zl_user_info","/white-space:nowrap;\">大专(小学)|(初中)|(高中)|(中专)|(大专)|(本科)|(硕士)|(博士)<\/li>/",0);
		$array['_58']['address']=array(0,"zl_user_info","/现居住(.*)<\/li>/",1);
        $array['_58']['wantsalary']=array(1,"zl_user_info","/期望薪资：\s*(.*)\s*期望地区：/",1);
        $array['_58']['exp_city']=array(0,"zl_user_info","/期望地区：<\/span>(.*)<\/li>/",1);
        $array['_58']['school']=array(0,"zl_user_info","/white-space:nowrap;\">((.*学院)|(.*大学)|(.*学校)|(.*分校))<\/li>/",1);
       
		
	    $array['_51job'] = array();
        $array['_51job']['wantposition'] = array(1,"zl_user_info","/应聘职位：(.*)应聘公司/",1);
        $array['_51job']['age']=array(1,"zl_user_info","/(男|女)&nbsp;\|&nbsp;([0-9]{2})岁/",2);
		$array['_51job']['birthday']=array(1,"zl_user_info","/岁\(([0-9]{4}年[0-9]{1,2}月)/",1);
		$array['_51job']['workyear']=array(0,"zl_user_info","/<span class=\"blue1\">\s*<b>(.*)&nbsp;\|&nbsp;(男|女&nbsp;)/",1);
		$array['_51job']['major']=array(1,"zl_user_info","/专　业：\s*(.*)\s*学　校：/",1);
		$array['_51job']['resume_deliver_time']=array(1,"zl_user_info","/投递时间：\s*([0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2})/",1);
        $array['_51job']['address']=array(1,"zl_user_info","/居住地：\s*(.*)\s*电　话：/",1);
        $array['_51job']['education_id']=array(1,"zl_user_info","/学　历：\s*(.*)\s*专　业：/",1);
        $array['_51job']['exp_city']=array(0,"zl_user_info","/目标地点：<\/td>\s*(.*)\s*<\/td>/",1);
		$array['_51job']['lastcompany'] = array(1,"zl_user_info","/公　司：(.*)行　业：/",1);
		$array['_51job']['lastposition'] = array(1,"zl_user_info","/职　位：(.*)学　历：/",1);
	    $array['_51job']['workstatus'] = array(1,"zl_user_info","/求职状态：(.*)工作经验/",1);
		
        $array['_zhaopin'] = array();
        $array['_zhaopin']['wantposition'] =array(1,"zl_user_info","/期望从事职业：(.*)期望月薪：/",1);
		$array['_zhaopin']['birthday']=array(0,"zl_user_info","/bold\">([0-9]{4}年[0-9]{1,2}月)/",1);
        $array['_zhaopin']['major']=array(1,"zl_user_info","/专　业：\s*(.*)\s*学　校：/",1);
        $array['_zhaopin']['workyear']=array(1,"zl_user_info","/(男|女)\|(.*)\|\s*[0-9]{4}/",2);
		$array['_zhaopin']['wantsalary']=array(0,"zl_user_info","/期望月薪：<\/td><td width=\"475\" valign=\"top\">(.*)\s*<\/td>/",1);
		$array['_zhaopin']['address']=array(0,"zl_user_info","/现居住于(.*)<small/",1);
		$array['_zhaopin']['education_id']=array(1,"zl_user_info","/\|(小学|初中|高中|中专|大专|本科|硕士|博士|其他)\|/",1);
		$array['_zhaopin']['school']=array(0,"zl_user_info","/12px;font\-weight:normal;\">((.*学院)|(.*大学)|(.*学校)|(.*分校))<\/span><span/",1);
		
		
		
        $array['_cjol'] = array();
        $array['_cjol']['wantposition'] = array(0,"zl_user_info","/style=\"font:12px\/22px Arial; color:#666666; padding:0 0 15px;\">(.*)<\/td>/",1);
        $array['_cjol']['age']=array(1,"zl_user_info","/年龄：\s*([0-9]{1,3})岁/",1);
		$array['_cjol']['school']=array(1,"zl_user_info","/毕业院校：\s*(.*)\s*年龄：/",1);
		$array['_cjol']['education_id']=array(1,"zl_user_info","/学历：\s*(.*)\s*性别：/",1);
		$array['_cjol']['major']=array(1,"zl_user_info","/专业：\s*(.*)\s*身高：/",1);
		$array['_cjol']['workyear']=array(0,"zl_user_info","/color:#333333;\">(((.*)工作经验)|(应届毕业生))/",1);
		$array['_cjol']['wantsalary']=array(0,"zl_user_info","/期望薪资：(.*)<\/td>/",1);
		$array['_cjol']['address']=array(0,"zl_user_info","/目前所在地：<\/td>\s*(.*)\s*<\/td>/",1);
		$array['_cjol']['exp_city']=array(0,"zl_user_info","/意向地区：<\/td>\s*(.*)\s*<\/td>/",1);
		
		
		
        $array['_ganji'] = array();
        $array['_ganji']['wantposition'] = array(0,"zl_user_info","/投递职位：<span style=\"font-weight:bold\">(.*)<\/span>/",1);
        $array['_ganji']['age']=array(0,"zl_user_info","/（(男|女) ([0-9]{1,3})岁）<\/p>/",2);
        $array['_ganji']['education_id']=array(1,"zl_user_info","/学历：(.*)\s*工作地点：/",1);
		$array['_ganji']['workyear']=array(1,"zl_user_info","/工作年限：([0-9]{0,2}\-{0,1}[0-9]{0,2})年\s*学历：/",1);
        $array['_ganji']['exp_city']=array(1,"zl_user_info","/工作地点：\s*(.*)\s*求职意向：/",1);
		
		
        $array['_chinahr'] = array();
       //$array['_chinahr']['wantposition'] = ;
        $result=M('education')->select();
		$education_array=array();
		foreach($result as $k=>$v)
		{
		   $education_array[$v['education_id']]=$v['educationname'];	
		}
		
		if($site=='_51job')
		{			
            foreach($array[$site]  as $k=>$pattern)
			{
				$matchs=array();
				$subject_str=$pattern[0]?$subject_nohtml:$subject;
			    $m=preg_match($pattern[2],$subject_str,$matchs);
				if($m)
				{					
				    if($k=='birthday')
				   {
				      $user_data[$pattern[1]][$k]=strtotime(str_replace(array('年','月'),array('-'),$matchs[$pattern[3]]));
				   }elseif($k=='education_id')
				   {						
					  foreach($education_array  as $kk=>$vv)
					  {							
						  if($vv==trim($matchs[$pattern[3]])) $user_data[$pattern[1]][$k]=$kk;
					  }
				   }elseif($k=='exp_city'){
					  $user_data[$pattern[1]][$k]=substr($matchs[$pattern[3]],strrpos($matchs[$pattern[3]],'>')+1);
				   }else{
					  $user_data[$pattern[1]][$k]=$matchs[$pattern[3]]; 
				   }
				}
			}
		}else if($site=='_cjol')
		{			
            foreach($array[$site]  as $k=>$pattern)
			{
				$matchs=array();
			    $subject_str=$pattern[0]?$subject_nohtml:$subject;
			    $m=preg_match($pattern[2],$subject_str,$matchs);		
				//if($k=='workyear')print_r($matchs);		
				if($m)
				{					
				    if($k=='birthday')
				   {
				      $user_data[$pattern[1]][$k]=strtotime(str_replace(array('年','月'),array('-'),$matchs[$pattern[3]]));
				   }elseif($k=='address'||$k=='exp_city'){
					  $user_data[$pattern[1]][$k]=str_replace('&nbsp;','',substr($matchs[$pattern[3]],strrpos($matchs[$pattern[3]],'>')+1));
				   }elseif($k=='education_id')
					{						
						foreach($education_array  as $kk=>$vv)
						{							
							if($vv==trim($matchs[$pattern[3]])) $user_data[$pattern[1]][$k]=$kk;
						}
					}else{
					  $user_data[$pattern[1]][$k]=$matchs[$pattern[3]]; 
				   }
				   
				}
			}
		}else if($site=='_zhaopin')
		{			
            foreach($array[$site]  as $k=>$pattern)
			{
				$matchs=array();
			    $subject_str=$pattern[0]?$subject_nohtml:$subject;
			    $m=preg_match($pattern[2],$subject_str,$matchs);			
				if($m)
				{
				  if($k=='birthday')
				   {
				        $user_data[$pattern[1]][$k]=strtotime(str_replace(array('年','月'),array('-'),$matchs[$pattern[3]]));
				   }elseif($k=='wantsalary'){
					    $user_data[$pattern[1]][$k]=substr($matchs[$pattern[3]],0,strpos($matchs[$pattern[3]],'<'));
					   
				   }elseif($k=='address'){
					    $user_data[$pattern[1]][$k]=substr($matchs[$pattern[3]],0,strpos($matchs[$pattern[3]],'<'));
					   
				   }elseif($k=='education_id')
				   {						
						foreach($education_array  as $kk=>$vv)
						{							
							if($vv==trim($matchs[$pattern[3]])) $user_data[$pattern[1]][$k]=$kk;
						}
				   }else{
					  $user_data[$pattern[1]][$k]=$matchs[$pattern[3]]; 
				   }
				}
			}			
		}else if($site=='_ganji')
		{			
            foreach($array[$site]  as $k=>$pattern)
			{
				$matchs=array();
			    $subject_str=$pattern[0]?$subject_nohtml:$subject;
			    $m=preg_match($pattern[2],$subject_str,$matchs);	
					
				if($m)
				{
					 if($k=="workyear")
				    {
					  $wy=explode('-',$matchs[$pattern[3]]);
					  if(count($wy)>1)$user_data[$pattern[1]][$k]=intval(($wy[0]+$wy[1])/2);
					  if(count($wy)==1)$user_data[$pattern[1]][$k]=$wy[0];
				    }elseif($k=='education_id')
					{						
						foreach($education_array  as $kk=>$vv)
						{							
							if($vv==trim($matchs[$pattern[3]])) $user_data[$pattern[1]][$k]=$kk;
						}
					}else{
				       $user_data[$pattern[1]][$k]=$matchs[$pattern[3]];
					}
				}
			}
		}else if($site=='_58')
		{			
            foreach($array[$site]  as $k=>$pattern)
			{
				
				$matchs=array();
			    $subject_str=$pattern[0]?$subject_nohtml:$subject;
			    $m=preg_match($pattern[2],$subject_str,$matchs);
				
				if($m)
				{
				   if($k=="workyear")
				   {
					  $wy=explode('-',$matchs[$pattern[3]]);
					  if(count($wy)>1)$user_data[$pattern[1]][$k]=intval(($wy[0]+$wy[1])/2);
					  if(count($wy)==1)$user_data[$pattern[1]][$k]=$wy[0];
				   }elseif($k=='education_id')
				   {	
					   foreach($education_array  as $kk=>$vv)
						{							
						   if(strpos($matchs[$pattern[3]],$vv)>-1)
						   {
							   $user_data[$pattern[1]][$k]=$kk;
						   }
						}					
				   }elseif($k=='school'){
					   $user_data[$pattern[1]][$k]=substr($matchs[$pattern[3]],strrpos($matchs[$pattern[3]],'>')+1);					   
				   }else{
				       $user_data[$pattern[1]][$k]=$matchs[$pattern[3]];		
				   }
				}
			}			
		}
		
		if($user_data['zl_user_info']['workyear']!="")
		{		
		    if($user_data['zl_user_info']['workyear']=="应届毕业生 ")
			{
				$user_data['zl_user_info']['workyear']=0;
			}else{
			    $user_data['zl_user_info']['workyear']=intval($user_data['zl_user_info']['workyear']);	
			}
		}
		if(!empty($user_data['zl_user_info']['birthday'])&&$user_data['zl_user_info']['age']&&is_numeric($user_data['zl_user_info']['age']))
		{
			$user_data['zl_user_info']['birthday']=strtotime("-".$user_data['zl_user_info']['age']." year");			
		}		
		//echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><pre>';
		//print_r($user_data);
		//echo '</pre>';	
	}
    /**
     * 提取邮箱
     */
    private function get_email($str) {
        $pattern='/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        preg_match_all($pattern,$str,$emailArr);
        unset($str);
        return $emailArr[0][0];
    }
    /**
     * 提取手机号
     */
    private function get_mobile($str) {
        $pattern = '~\b1[34578]{1}[0-9]{1}[ ]{0,1}[0-9]{4}[ ]{0,1}[0-9]{4}\b~i';
        preg_match_all($pattern,$str,$mobileArr);
        unset($str);
        return str_replace(' ','',$mobileArr[0][0]);
    }

}