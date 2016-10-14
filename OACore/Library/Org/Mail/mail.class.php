<?php
class receiveMail
{
	var $server='';
	var $username='';
	var $password='';
	
	var $marubox='';					
	
	var $email='';			
	
	function receiveMail($username,$password,$EmailAddress,$mailserver='localhost',$servertype='pop',$port='110',$ssl = false) //Constructure
	{
		if($servertype=='imap')
		{
			if($port=='') $port='143'; 
			$strConnect='{'.$mailserver.'/imap:'.$port.'}INBOX';
		}
		else
		{
			if($port=='') $port='110'; 
			$strConnect='{'.$mailserver.':'.$port. '/pop3'.($ssl ? "/ssl" : "").'}INBOX'; 
		}
		$this->server			=	$strConnect;
		$this->username			=	$username;
		$this->password			=	$password;
		$this->email			=	$EmailAddress;
	}
	
	function connect() //Connect To the Mail Box
	{
		$this->marubox=imap_open($this->server,$this->username,$this->password,0);
		
		if(!$this->marubox)
		{
			echo "Error: Connecting to mail server";
			exit;
		}
	}
	/**
     * 格式化头部信息 $headerinfo get_imap_header 的返回值
     */
    public function get_header_info($mail_header) {
        $sender=$mail_header->from[0];
        $sender_replyto=$mail_header->reply_to[0];
        if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster') {
            $mail_details=array(
                'from'=>strtolower($sender->mailbox).'@'.$sender->host,
                'fromName'=>$this->_decode_mime_str($sender->personal),
                'toOth'=>strtolower($sender_replyto->mailbox).'@'.$sender_replyto->host,
                'toNameOth'=>$this->_decode_mime_str($sender_replyto->personal),
                'subject'=>$this->_decode_mime_str($mail_header->subject),
                'date'=>strtotime($this->_decode_mime_str($mail_header->Date)),
                'to'=>strtolower($this->_decode_mime_str($mail_header->toaddress))
            );
        }
        return $mail_details;
    }
    private function _decode_mime_str($string, $charset="UTF-8" ) { 
        $newString = ''; 
        $elements=imap_mime_header_decode($string); 
        for($i=0;$i<count($elements);$i++) { 
            if($elements[$i]->charset == 'default') $elements[$i]->charset = 'iso-8859-1'; 
            $newString .= iconv($elements[$i]->charset, $charset, $elements[$i]->text); 
        } 
        return $newString; 
    }
	function get_mime_type(&$structure) //Get Mime type Internal Private Use
	{ 
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"); 
		
		if($structure->subtype) { 
			return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype; 
		} 
		return "TEXT/PLAIN"; 
	} 
	
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) //Get Part Of Message Internal Private Use
	{ 
		if(!$structure) { 
			$structure = imap_fetchstructure($stream, $msg_number); 
		} 
		if($structure) { 
			if($mime_type == $this->get_mime_type($structure))
			{ 
				if(!$part_number) 
				{ 
					$part_number = "1"; 
				} 
				$text = imap_fetchbody($stream, $msg_number, $part_number); 
				//file_put_contents('D:/project/www/b/'.$msg_number.'.txt', $text);
				if($structure->encoding == 3) 
				{ 
					return imap_base64($text); 
				} 
				else if($structure->encoding == 4) 
				{ 
					return imap_qprint($text); 
				} 
				else
				{ 
					return $text; 
				} 
			} 
			if($structure->type == 1) /* multipart */ 
			{ 
				while(list($index, $sub_structure) = each($structure->parts))
				{ 
					if($part_number)
					{ 
						$prefix = $part_number . '.'; 
					} 
					$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1)); 
					if($data)
					{ 
						return $data; 
					} 
				} 
			} 
		} 
		return false; 
	}
	function getTotalMails() //Get Total Number off Unread Email In Mailbox
	{
		if(!$this->marubox)
			return false;
		$tmp = imap_num_msg($this->marubox); // 取得信件数
		return is_numeric($tmp) ? $tmp : false;
	}
	public function get_imap_header($mid) {
		return imap_headerinfo($this->marubox,$mid);
	}
	/**
	 * 标记邮件成已读
	 */
	public function mail_mark_read($mid) {
        return imap_setflag_full($this->marubox, $mid, '\\Seen');
    }
    /**
     * 标记邮件成未读
     */
    public function mail_mark_unread($mid) {
        return imap_clearflag_full($this->marubox, $mid, '\\Seen');
    }
	/**
	 * 判断是否阅读了邮件
	 * @param   $headerinfo get_imap_header 的返回值
	 */
	public function isUnread($headerinfo) {
        if (($headerinfo->Unseen == 'U') || ($headerinfo->Recent == 'N')) {
            return true;
        }
        return false;
    }
    /**
     * 删除邮件
     */
    public function delete($mid) {
    	if(!$this->marubox)
			return false;
        return imap_delete($this->marubox, $mid, 0);
    }
    /**
     * 获取附件
     */
	function GetAttach($mid,$path) // Get Atteced File from Mail
	{
		if(!$this->marubox)
		{
			return false;
		}

		$struckture = imap_fetchstructure($this->marubox,$mid);
		$ar="";
		if($struckture->parts)
        {
			foreach($struckture->parts as $key => $value)
			{
				$enc=$struckture->parts[$key]->encoding;
				if($struckture->parts[$key]->ifdparameters)
				{
					$name=$struckture->parts[$key]->dparameters[0]->value;
					$message = imap_fetchbody($this->marubox,$mid,$key+1);
					switch ($enc)
					{
						case 0:
							$message = imap_8bit($message);
							break;
						case 1:
							$message = imap_8bit ($message);
							break;
						case 2:
							$message = imap_binary ($message);
							break;
						case 3:
							$message = imap_base64 ($message); 
							break;
						case 4:
							$message = quoted_printable_decode($message);
							break;
						case 5:
							$message = $message;
							break;
					}
					$fp=fopen($path.$name,"w");
					fwrite($fp,$message);
					fclose($fp);
					$ar=$ar.$name.",";
				}
				// Support for embedded attachments starts here
				if($struckture->parts[$key]->parts)
				{
					foreach($struckture->parts[$key]->parts as $keyb => $valueb)
					{
						$enc=$struckture->parts[$key]->parts[$keyb]->encoding;
						if($struckture->parts[$key]->parts[$keyb]->ifdparameters)
						{
							$name=$struckture->parts[$key]->parts[$keyb]->dparameters[0]->value;
							$partnro = ($key+1).".".($keyb+1);
							$message = imap_fetchbody($this->marubox,$mid,$partnro);
							switch ($enc)
							{
								case 0:
								   $message = imap_8bit($message);
									break;
								case 1:
								   $message = imap_8bit ($message);
									break;
								case 2:
								   $message = imap_binary ($message);
									break;
								case 3:
								   $message = imap_base64 ($message);
									break;
								case 4:
								   $message = quoted_printable_decode($message);
									break;
								case 5:
								   $message = $message;
									break;
							}
							$fp=fopen($path.$name,"w");
							fwrite($fp,$message);
							fclose($fp);
							$ar=$ar.$name.",";
						}
					}
				}				
			}
		}
		$ar=substr($ar,0,(strlen($ar)-1));
		return $ar;
	}
	/**
	 * 读取邮件主体
	 */
	function getBody($mid) // Get Message Body
	{
		if(!$this->marubox)
		{
			return false;
		}
		$body = $this->get_part($this->marubox, $mid, "TEXT/HTML");
		if ($body == "")
		{
			$body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN");
		}
		if ($body == "") 
		{
			return "";
		}

		return $this->_iconv_utf8($body);
	}
	function close_mailbox() //Close Mail Box
	{
		if(!$this->marubox)
			return false;

		imap_close($this->marubox,CL_EXPUNGE);
	}
	function _iconv_utf8($text)
	{
		$s1 = iconv('gbk', 'utf-8', $text);
		$s0 = iconv('utf-8', 'gbk', $s1);
		if($s0 == $text)
		{
			return $s1;
		}
		else
		{
			return $text;
		}
	}
}
