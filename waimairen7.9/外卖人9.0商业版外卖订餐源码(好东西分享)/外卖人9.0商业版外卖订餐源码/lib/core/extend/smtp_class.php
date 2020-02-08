<?php
 
class ISmtp
{
	private $smtp_port;  //端口号
	private $time_out;   //超时时间
	private $host_name;  //主机名
	private $log_file;   //日志名
	private $relay_host; //响应主机ip
	private $auth;       //认证
	private $user;       //用户名
	private $pass;       //端口
	private $sock;
	private $debug;
	private $sendType;
	private $emailnew;

	 
	function __construct($relay_host = "", $smtp_port = 25, $user = false, $pass = false, $debug = true)
	{
		
		  
		
		 
		 $this->emailnew = new MySendMail();
		 $smtp_port = 465;
		 if($smtp_port == 25){
			$this->emailnew->setServer($relay_host, $user, $pass); //设置smtp服务器，普通连接方式
		 }else{ 
			$this->emailnew->setServer($relay_host, $user, $pass, $smtp_port, true); //设置smtp服务器，到服务器的SSL连接
		 }
		 
		
		/*
		$this->relay_host = $relay_host;
		$this->smtp_port = $smtp_port;
		$this->user = $user;
		$this->pass = $pass;

		$this->debug = $debug;
		$this->time_out = 40;
		$this->host_name = "localhost"; //测试本地socket

		$this->auth = false;
		if($this->user || $this->pass)
		{
			$this->auth = true;
		}

		if(!$this->relay_host)
		{
			$this->sendType = "mail";
		}

		//记录日志文件路径
		$this->log_file = "";

		$this->sock = FALSE;
		*/
	}

 
	public function send($to, $from = "" , $subject = "", $body = "", $additional_headers = "", $mailtype = "HTML", $cc = "", $bcc = "")
	{
		
		// $smtp = new ISmtp(Mysite::$app->config['smpt'],25,Mysite::$app->config['emailname'],Mysite::$app->config['emailpwd'],true); 
	      // $title = '测试发送';
		  // $tempcontent = '发送邮件';
		  // $smtp->send('zhaojq5099@foxmail.com', Mysite::$app->config['emailname'],$title,$tempcontent, "" , "HTML" , "" , "");  
           
		$this->emailnew->setFrom($from); //设置发件人
		$this->emailnew->setReceiver($to); //设置收件人，多个收件人，调用多次
		//$mail->setCc("XXXX"); //设置抄送，多个抄送，调用多次
		// $mail->setBcc("XXXXX"); //设置秘密抄送，多个秘密抄送，调用多次
		// $mail->addAttachment("XXXX"); //添加附件，多个附件，调用多次
		$this->emailnew->setMail($subject, $body); //设置邮件主题、内容
		$this->emailnew->sendMail(); //发送
		 
		 /*
		 
		$mail_from = $this->get_address($this->strip_comment($from));
		$body = preg_replace("/(^|(\r\n))(\\.)/i", "\\1.\\3", $body);
		$header="";
		$header .= "MIME-Version:1.0\r\n";
		if($mailtype=="HTML")
		{
			$header .= "Content-Type:text/html\r\n";
		}
		$header .= "To: ".$to."\r\n";
		if ($cc != "")
		{
			$header .= "Cc: ".$cc."\r\n";
		}
		$subject = "=?UTF-8?B?".base64_encode($subject)."?="; 
		$header .= "From: $from<".$from.">\r\n";
		$header .= "Subject: ".$subject."\r\n";
		$header .= $additional_headers;
		$header .= "Date: ".date("r")."\r\n";
		
		$header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n";
		list($msec, $sec) = explode(" ", microtime());
		$header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mail_from.">\r\n";
		$TO = explode(",", $this->strip_comment($to));
		
	
		if ($cc != "")
		{
			$TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
		}

		if ($bcc != "") {
			$TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
		}

		$sent = TRUE;
		foreach ($TO as $rcpt_to)
		{
			//php内置mail发送
			if($this->sendType=="mail")
			{
				return mail($rcpt_to,'',$body,$header);
			}

			//socket发送方式
			$rcpt_to = $this->get_address($rcpt_to);
			if (!$this->smtp_sockopen($rcpt_to))
			{
				$this->log_write("Error: Cannot send email to ".$rcpt_to."\n");
				$sent = FALSE;
				continue;
			}
			if ($this->smtpSend($this->host_name, $mail_from, $rcpt_to, $header, $body))
			{
				$this->log_write("E-mail has been sent to <".$rcpt_to.">\n");
			} else
			{
				$this->log_write("Error: Cannot send email to <".$rcpt_to.">\n");
				$sent = FALSE;
			}
			fclose($this->sock);
			$this->log_write("Disconnected from remote host\n");
		}
		return $sent;
		*/
	}

	 
	private function smtpSend($helo, $from, $to, $header, $body = "")
	{

		if (!$this->smtp_putcmd("HELO", $helo))
		{
			return $this->smtp_error("sending HELO command");
		}

		if($this->auth)
		{
			if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user)))
			{
				return $this->smtp_error("sending HELO command");
			}

			if (!$this->smtp_putcmd("", base64_encode($this->pass)))
			{
				return $this->smtp_error("sending HELO command");
			}
		}
		if (!$this->smtp_putcmd("MAIL", "FROM:<".$from.">"))
		{
			return $this->smtp_error("sending MAIL FROM command");
		}

		if (!$this->smtp_putcmd("RCPT", "TO:<".$to.">"))
		{
			return $this->smtp_error("sending RCPT TO command");
		}

		if (!$this->smtp_putcmd("DATA"))
		{
			return $this->smtp_error("sending DATA command");
		}

		if (!$this->smtp_message($header, $body))
		{
			return $this->smtp_error("sending message");
		}

		if (!$this->smtp_eom())
		{
			return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
		}

		if (!$this->smtp_putcmd("QUIT"))
		{
			return $this->smtp_error("sending QUIT command");
		}

		return TRUE;
	}

 
	private function smtp_sockopen($address)
	{
		if ($this->relay_host == "")
		{
			return $this->smtp_sockopen_mx($address);
		} else
		{
			return $this->smtp_sockopen_relay();
		}
	}

	 
	private function smtp_sockopen_relay()
	{
		$this->log_write("Trying to ".$this->relay_host.":".$this->smtp_port."\n");
		$this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
		if (!($this->sock && $this->smtp_ok()))
		{
			$this->log_write("Error: Cannot connenct to relay host ".$this->relay_host."\n");
			$this->log_write("Error: ".$errstr." (".$errno.")\n");
			return FALSE;
		}
		$this->log_write("Connected to relay host ".$this->relay_host."\n");
		return TRUE;;
	}

	 
	private function smtp_sockopen_mx($address)
	{
		$domain = preg_replace("/^.+@([^@]+)$/i", "\\1", $address);
		if (!@getmxrr($domain, $MXHOSTS))
		{
			$this->log_write("Error: Cannot resolve MX \"".$domain."\"\n");
			return FALSE;
		}
		foreach ($MXHOSTS as $host)
		{
			$this->log_write("Trying to ".$host.":".$this->smtp_port."\n");
			$this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
			if (!($this->sock && $this->smtp_ok())) {
				$this->log_write("Warning: Cannot connect to mx host ".$host."\n");
				$this->log_write("Error: ".$errstr." (".$errno.")\n");
				continue;
			}
			$this->log_write("Connected to mx host ".$host."\n");
			return TRUE;
		}
		$this->log_write("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");
		return FALSE;
	}

	 
	private function smtp_message($header, $body)
	{
		fwrite($this->sock, $header."\r\n".$body);
		$this->smtp_debug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));

		return TRUE;
	}

	private function smtp_eom()
	{
		fwrite($this->sock, "\r\n.\r\n");
		$this->smtp_debug(". [EOM]\n");

		return $this->smtp_ok();
	}

	private function smtp_ok()
	{
		$response = str_replace("\r\n", "", fgets($this->sock, 512));
		$this->smtp_debug($response."\n");

		if (!preg_match("/^[23]/i", $response))
		{
			fputs($this->sock, "QUIT\r\n");
			fgets($this->sock, 512);
			$this->log_write("Error: Remote host returned \"".$response."\"\n");
			return FALSE;
		}
		return TRUE;
	}

 
	private function smtp_putcmd($cmd, $arg = "")
	{

		if ($arg != "")
		{
			if($cmd=="") $cmd = $arg;
			else $cmd = $cmd." ".$arg;
		}

		fwrite($this->sock, $cmd."\r\n");
		$this->smtp_debug("> ".$cmd."\n");

		return $this->smtp_ok();
	}

	private function smtp_error($string)
	{
		$this->log_write("Error: Error occurred while ".$string.".\n");
		return FALSE;
	}

	 
	private function log_write($message)
	{
		$this->smtp_debug($message);

		if ($this->log_file == "")
		{
			return TRUE;
		}

		$message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
		if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a")))
		{
			$this->smtp_debug("Warning: Cannot open log file \"".$this->log_file."\"\n");
			return FALSE;
		}
		flock($fp, LOCK_EX);
		fputs($fp, $message);
		fclose($fp);

		return TRUE;
	}

	private function strip_comment($address)
	{
		$comment = "\\([^()]*\\)";
		while (preg_match('/'.$comment.'/i', $address))
		{
			$address = preg_replace('/'.$comment.'/i', "", $address);
		}

		return $address;
	}

	 
	private function get_address($address)
	{
		$address = preg_replace("/([ \t\r\n])+/i", "", $address);
		$address = preg_replace("/^.*<(.+)>.*$/i", "\\1", $address);

		return $address;
	}

	 
	private function smtp_debug($message)
	{
		if ($this->debug)
		{
		//	echo $message."<br>";
		}
	}

	 
	public function get_attach_type($image_tag)
	{
		$filedata = array();
		$img_file_con=fopen($image_tag,"r");
		unset($image_data);
		while ($tem_buffer=AddSlashes(fread($img_file_con,filesize($image_tag))))
			$image_data.=$tem_buffer;
		fclose($img_file_con);
		$filedata['context'] = $image_data;
		$filedata['filename']= basename($image_tag);
		$extension=substr($image_tag,strrpos($image_tag,"."),strlen($image_tag)-strrpos($image_tag,"."));
		switch($extension){
		case ".gif":
			$filedata['type'] = "image/gif";
			break;
		case ".gz":
			$filedata['type'] = "application/x-gzip";
			break;
		case ".htm":
			$filedata['type'] = "text/html";
			break;
		case ".html":
			$filedata['type'] = "text/html";
			break;
		case ".jpg":
			$filedata['type'] = "image/jpeg";
			break;
		case ".tar":
			$filedata['type'] = "application/x-tar";
			break;
		case ".txt":
			$filedata['type'] = "text/plain";
			break;
		case ".zip":
			$filedata['type'] = "application/zip";
			break;
		default:
			$filedata['type'] = "application/octet-stream";
			break;
		}
		return $filedata;
	}
}
class MySendMail {
    /**
    * @var string 邮件传输代理用户名
    * @access protected
    */
    protected $_userName;
    /**
    * @var string 邮件传输代理密码
    * @access protected
    */
    protected $_password;
    /**
    * @var string 邮件传输代理服务器地址
    * @access protected
    */
    protected $_sendServer;
    /**
    * @var int 邮件传输代理服务器端口
    * @access protected
    */
    protected $_port;
    /**
    * @var string 发件人
    * @access protected
    */
    protected $_from;
    /**
    * @var array 收件人
    * @access protected
    */
    protected $_to = array();
    /**
    * @var array 抄送
    * @access protected
    */
    protected $_cc = array();
    /**
    * @var array 秘密抄送
    * @access protected
    */
    protected $_bcc = array();
    /**
    * @var string 主题
    * @access protected
    */
    protected $_subject;
    /**
    * @var string 邮件正文
    * @access protected
    */
    protected $_body;
    /**
    * @var array 附件
    * @access protected
    */
    protected $_attachment = array();
    /**
    * @var reource socket资源
    * @access protected
    */
    protected $_socket;
    /**
    * @var reource 是否是安全连接
    * @access protected
    */
    protected $_isSecurity;
    /**
    * @var string 错误信息
    * @access protected
    */
    protected $_errorMessage;
    /**
    * 设置邮件传输代理，如果是可以匿名发送有邮件的服务器，只需传递代理服务器地址就行
    * @access public
    * @param string $server 代理服务器的ip或者域名
    * @param string $username 认证账号
    * @param string $password 认证密码
    * @param int $port 代理服务器的端口，smtp默认25号端口
    * @param boolean $isSecurity 到服务器的连接是否为安全连接，默认false
    * @return boolean
    */
    public function setServer($server, $username="", $password="", $port=25, $isSecurity=false) {
        $this->_sendServer = $server;
        $this->_port = $port;
        $this->_isSecurity = $isSecurity;
        $this->_userName = empty($username) ? "" : base64_encode($username);
        $this->_password = empty($password) ? "" : base64_encode($password);
        return true;
    }
    /**
    * 设置发件人
    * @access public
    * @param string $from 发件人地址
    * @return boolean
    */
    public function setFrom($from) {
        $this->_from = $from;
        return true;
    }
    /**
    * 设置收件人，多个收件人，调用多次.
    * @access public
    * @param string $to 收件人地址
    * @return boolean
    */
    public function setReceiver($to) {
        $this->_to[] = $to;
        return true;
    }
    /**
    * 设置抄送，多个抄送，调用多次.
    * @access public
    * @param string $cc 抄送地址
    * @return boolean
    */
    public function setCc($cc) {
        $this->_cc[] = $cc;
        return true;
    }
    /**
    * 设置秘密抄送，多个秘密抄送，调用多次
    * @access public
    * @param string $bcc 秘密抄送地址
    * @return boolean
    */
    public function setBcc($bcc) {
        $this->_bcc[] = $bcc;
        return true;
    }
    /**
    * 设置邮件附件，多个附件，调用多次
    * @access public
    * @param string $file 文件地址
    * @return boolean
    */
    public function addAttachment($file) {
        if(!file_exists($file)) {
            $this->_errorMessage = "file " . $file . " does not exist.";
            return false;
        }
        $this->_attachment[] = $file;
        return true;
    }
    /**
    * 设置邮件信息
    * @access public
    * @param string $body 邮件主题
    * @param string $subject 邮件主体内容，可以是纯文本，也可是是HTML文本
    * @return boolean
    */
    public function setMail($subject, $body) {
        $this->_subject = base64_encode($subject);
        $this->_body = base64_encode($body);
        return true;
    }
    /**
    * 发送邮件
    * @access public
    * @return boolean
    */
    public function sendMail() {
        $command = $this->getCommand();
        $this->_isSecurity ? $this->socketSecurity() : $this->socket();
        foreach ($command as $value) {
            $result = $this->_isSecurity ? $this->sendCommandSecurity($value[0], $value[1]) : $this->sendCommand($value[0], $value[1]);
            if($result) {
                continue;
            }
            else{
                return false;
            }
        }
        //其实这里也没必要关闭，smtp命令：QUIT发出之后，服务器就关闭了连接，本地的socket资源会自动释放
        $this->_isSecurity ? $this->closeSecutity() : $this->close();
        return true;
    }
    /**
    * 返回错误信息
    * @return string
    */
    public function error(){
        if(!isset($this->_errorMessage)) {
            $this->_errorMessage = "";
        }
        return $this->_errorMessage;
    }
    /**
    * 返回mail命令
    * @access protected
    * @return array
    */
    protected function getCommand() {
        $separator = "----=_Part_" . md5($this->_from . time()) . uniqid(); //分隔符
        $command = array(
                array("HELO sendmail\r\n", 250)
            );
        if(!empty($this->_userName)){
            $command[] = array("AUTH LOGIN\r\n", 334);
            $command[] = array($this->_userName . "\r\n", 334);
            $command[] = array($this->_password . "\r\n", 235);
        }
        //设置发件人
        $command[] = array("MAIL FROM: <" . $this->_from . ">\r\n", 250);
        $header = "FROM: <" . $this->_from . ">\r\n";
        //设置收件人
        if(!empty($this->_to)) {
            $count = count($this->_to);
            if($count == 1){
                $command[] = array("RCPT TO: <" . $this->_to[0] . ">\r\n", 250);
                $header .= "TO: <" . $this->_to[0] .">\r\n";
            }
            else{
                for($i=0; $i<$count; $i++){
                    $command[] = array("RCPT TO: <" . $this->_to[$i] . ">\r\n", 250);
                    if($i == 0){
                        $header .= "TO: <" . $this->_to[$i] .">";
                    }
                    elseif($i + 1 == $count){
                        $header .= ",<" . $this->_to[$i] .">\r\n";
                    }
                    else{
                        $header .= ",<" . $this->_to[$i] .">";
                    }
                }
            }
        }
        //设置抄送
        if(!empty($this->_cc)) {
            $count = count($this->_cc);
            if($count == 1){
                $command[] = array("RCPT TO: <" . $this->_cc[0] . ">\r\n", 250);
                $header .= "CC: <" . $this->_cc[0] .">\r\n";
            }
            else{
                for($i=0; $i<$count; $i++){
                    $command[] = array("RCPT TO: <" . $this->_cc[$i] . ">\r\n", 250);
                    if($i == 0){
                    $header .= "CC: <" . $this->_cc[$i] .">";
                    }
                    elseif($i + 1 == $count){
                        $header .= ",<" . $this->_cc[$i] .">\r\n";
                    }
                    else{
                        $header .= ",<" . $this->_cc[$i] .">";
                    }
                }
            }
        }
        //设置秘密抄送
        if(!empty($this->_bcc)) {
            $count = count($this->_bcc);
            if($count == 1) {
                $command[] = array("RCPT TO: <" . $this->_bcc[0] . ">\r\n", 250);
                $header .= "BCC: <" . $this->_bcc[0] .">\r\n";
            }
            else{
                for($i=0; $i<$count; $i++){
                    $command[] = array("RCPT TO: <" . $this->_bcc[$i] . ">\r\n", 250);
                    if($i == 0){
                    $header .= "BCC: <" . $this->_bcc[$i] .">";
                    }
                    elseif($i + 1 == $count){
                        $header .= ",<" . $this->_bcc[$i] .">\r\n";
                    }
                    else{
                        $header .= ",<" . $this->_bcc[$i] .">";
                    }
                }
            }
        }
        //主题
        $header .= "Subject: =?UTF-8?B?" . $this->_subject ."?=\r\n";
        if(isset($this->_attachment)) {
            //含有附件的邮件头需要声明成这个
            $header .= "Content-Type: multipart/mixed;\r\n";
        }
        elseif(false){
            //邮件体含有图片资源的,且包含的图片在邮件内部时声明成这个，如果是引用的远程图片，就不需要了
            $header .= "Content-Type: multipart/related;\r\n";
        }
        else{
            //html或者纯文本的邮件声明成这个
            $header .= "Content-Type: multipart/alternative;\r\n";
        }
        //邮件头分隔符
        $header .= "\t" . 'boundary="' . $separator . '"';
        $header .= "\r\nMIME-Version: 1.0\r\n";
        //这里开始是邮件的body部分，body部分分成几段发送
        $header .= "\r\n--" . $separator . "\r\n";
        $header .= "Content-Type:text/html; charset=utf-8\r\n";
        $header .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $header .= $this->_body . "\r\n";
        $header .= "--" . $separator . "\r\n";
        //加入附件
        if(!empty($this->_attachment)){
            $count = count($this->_attachment);
            for($i=0; $i<$count; $i++){
                $header .= "\r\n--" . $separator . "\r\n";
                $header .= "Content-Type: " . $this->getMIMEType($this->_attachment[$i]) . '; name="=?UTF-8?B?' . base64_encode( basename($this->_attachment[$i]) ) . '?="' . "\r\n";
                $header .= "Content-Transfer-Encoding: base64\r\n";
                $header .= 'Content-Disposition: attachment; filename="=?UTF-8?B?' . base64_encode( basename($this->_attachment[$i]) ) . '?="' . "\r\n";
                $header .= "\r\n";
                $header .= $this->readFile($this->_attachment[$i]);
                $header .= "\r\n--" . $separator . "\r\n";
            }
        }
        //结束邮件数据发送
        $header .= "\r\n.\r\n";
 
        $command[] = array("DATA\r\n", 354);
        $command[] = array($header, 250);
        $command[] = array("QUIT\r\n", 221);
        return $command;
    }
    /**
    * 发送命令
    * @access protected
    * @param string $command 发送到服务器的smtp命令
    * @param int $code 期望服务器返回的响应吗
    * @return boolean
    */
    protected function sendCommand($command, $code) {
      //  echo 'Send command:' . $command . ',expected code:' . $code . '<br />';
        //发送命令给服务器
        try{
            if(socket_write($this->_socket, $command, strlen($command))){
                //当邮件内容分多次发送时，没有$code，服务器没有返回
                if(empty($code))  {
                    return true;
                }
                //读取服务器返回
                $data = trim(socket_read($this->_socket, 1024));
            //    echo 'response:' . $data . '<br /><br />';
                if($data) {
                    $pattern = "/^".$code."+?/";
                    if(preg_match($pattern, $data)) {
                        return true;
                    }
                    else{
                        $this->_errorMessage = "Error:" . $data . "|**| command:";
                        return false;
                    }
                }
                else{
                    $this->_errorMessage = "Error:" . socket_strerror(socket_last_error());
                    return false;
                }
            }
            else{
                $this->_errorMessage = "Error:" . socket_strerror(socket_last_error());
                return false;
            }
        }catch(Exception $e) {
            $this->_errorMessage = "Error:" . $e->getMessage();
        }
    }
    /**
    * 安全连接发送命令
    * @access protected
    * @param string $command 发送到服务器的smtp命令
    * @param int $code 期望服务器返回的响应吗
    * @return boolean
    */
    protected function sendCommandSecurity($command, $code) {
       // echo 'Send command:' . $command . ',expected code:' . $code . '<br />';
        try {
            if(fwrite($this->_socket, $command)){
                //当邮件内容分多次发送时，没有$code，服务器没有返回
                if(empty($code))  {
                    return true;
                }
                //读取服务器返回
                $data = trim(fread($this->_socket, 1024));
             //   echo 'response:' . $data . '<br /><br />';
                if($data) {
                    $pattern = "/^".$code."+?/";
                    if(preg_match($pattern, $data)) {
                        return true;
                    }
                    else{
                        $this->_errorMessage = "Error:" . $data . "|**| command:";
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            else{
                $this->_errorMessage = "Error: " . $command . " send failed";
                return false;
            }
        }catch(Exception $e) {
            $this->_errorMessage = "Error:" . $e->getMessage();
        }
    } 
    /**
    * 读取附件文件内容，返回base64编码后的文件内容
    * @access protected
    * @param string $file 文件
    * @return mixed
    */
    protected function readFile($file) {
        if(file_exists($file)) {
            $file_obj = file_get_contents($file);
            return base64_encode($file_obj);
        }
        else {
            $this->_errorMessage = "file " . $file . " dose not exist";
            return false;
        }
    }
    /**
    * 获取附件MIME类型
    * @access protected
    * @param string $file 文件
    * @return mixed
    */
    protected function getMIMEType($file) {
        if(file_exists($file)) {
            $mime = mime_content_type($file);
            /*if(! preg_match("/gif|jpg|png|jpeg/", $mime)){
                $mime = "application/octet-stream";
            }*/
            return $mime;
        }
        else {
            return false;
        }
    }
    /**
    * 建立到服务器的网络连接
    * @access protected
    * @return boolean
    */
    protected function socket() {
        //创建socket资源
        $this->_socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
        if(!$this->_socket) {
            $this->_errorMessage = socket_strerror(socket_last_error());
            return false;
        }
        socket_set_block($this->_socket);//设置阻塞模式
        //连接服务器
        if(!socket_connect($this->_socket, $this->_sendServer, $this->_port)) {
            $this->_errorMessage = socket_strerror(socket_last_error());
            return false;
        }
        $str = socket_read($this->_socket, 1024);
        if(!preg_match("/220+?/", $str)){
            $this->_errorMessage = $str;
            return false;
        }
        return true;
    }
    /**
    * 建立到服务器的SSL网络连接
    * @access protected
    * @return boolean
    */
    protected function socketSecurity() {
        $remoteAddr = "tcp://" . $this->_sendServer . ":" . $this->_port;
        $this->_socket = stream_socket_client($remoteAddr, $errno, $errstr, 30);
        if(!$this->_socket){
            $this->_errorMessage = $errstr;
            return false;
        }
        //设置加密连接，默认是ssl，如果需要tls连接，可以查看php手册stream_socket_enable_crypto函数的解释
        stream_socket_enable_crypto($this->_socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
        stream_set_blocking($this->_socket, 1); //设置阻塞模式
        $str = fread($this->_socket, 1024);
        if(!preg_match("/220+?/", $str)){
            $this->_errorMessage = $str;
            return false;
        }
        return true;
    }
    /**
    * 关闭socket
    * @access protected
    * @return boolean
    */
    protected function close() {
        if(isset($this->_socket) && is_object($this->_socket)) {
            $this->_socket->close();
            return true;
        }
        $this->_errorMessage = "No resource can to be close";
        return false;
    }
    /**
    * 关闭安全socket
    * @access protected
    * @return boolean
    */
    protected function closeSecutity() {
        if(isset($this->_socket) && is_object($this->_socket)) {
            stream_socket_shutdown($this->_socket, STREAM_SHUT_WR);
            return true;
        }
        $this->_errorMessage = "No resource can to be close";
        return false;
    }
}
