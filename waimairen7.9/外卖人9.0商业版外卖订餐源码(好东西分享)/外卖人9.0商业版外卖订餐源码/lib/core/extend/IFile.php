<?php
 
class IFile
{
	private $resource = null; //文件资源句柄
	public static $except = array('.','..','.svn'); //无效文件或目录名

	 
	function __construct($fileName,$mode='r')
	{
		$dirName  = dirname($fileName);
		$baseName = basename($fileName);

		//检查并创建文件夹
		self::mkdir($dirName);

		$this->resource = fopen($fileName,$mode.'b');
		if($this->resource)
		{
			flock($this->resource,LOCK_EX);
		}
	}

	 
	public function read()
	{
		$content = null;
		while(!feof($this->resource))
		{
			$content.= fread($this->resource,1024);
		}
		return $content;
	}

	 
	public function write($content)
	{
		$worldsnum = fwrite($this->resource,$content);
		return is_bool($worldsnum) ? false : $worldsnum;
	}

	 
	public static function clearDir($dir)
	{
		if(!in_array($dir,self::$except) && is_dir($dir) && is_writable($dir))
		{
			$dirRes = opendir($dir);
			while($fileName = readdir($dirRes))
			{
				if(!in_array($fileName,self::$except))
				{
					$fullpath = $dir.'/'.$fileName;
					if(is_file($fullpath))
					{
						self::unlink($fullpath);
					}

					else
					{
						self::clearDir($fullpath);
						rmdir($fullpath);
					}
				}
			}
			closedir($dirRes);
			return true;
		}
		else
		{
			return false;
		}
	}
 
	public static function getInfo($fileName)
	{
		if(is_file($fileName))
			return stat($fileName);

		else
			return null;
	}

 
	public static function mkdir($path,$chmod=0777)
	{
		return is_dir($path) or (self::mkdir(dirname($path),$chmod) and mkdir($path,$chmod));
	}

	 
	public static function copy($from,$to,$mode = 'c')
	{
		if(is_file($from))
		{
			$dir = dirname($to);

			//创建目录
			self::mkdir($dir);

			copy($from,$to);

			if(is_file($to))
			{
				if($mode == 'x')
				{
					self::unlink($from);
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	}

	 
	public static function unlink($fileName)
	{
		if(is_file($fileName) && is_writable($fileName))
		{
			unlink($fileName);
		}
		else
			return false;
	}

	 
	public static function rmdir($dir,$recursive = false)
	{
		if(is_dir($dir) && is_writable($dir))
		{
			//强制删除
			if($recursive == true)
			{
				self::clearDir($dir);
				self::rmdir($fullpath,false);
			}

			//非强制删除
			else
			{
				if(rmdir($dir))
					return true;

				else
					return false;
			}
		}
	}

	 
	public static function getFileType($fileName)
	{
		$filetype = null;
		if(!is_file($fileName))
		{
			return false;
		}

		$fileRes = fopen($fileName,"rb");
	    if(!$fileRes)
		{
			return false;
		}
        $bin= fread($fileRes, 2);
        fclose($fileRes);

        if($bin != null)
        {
        	$strInfo  = unpack("C2chars", $bin);
	        $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
			$typelist = self::getTypeList();
			foreach($typelist as $val)
			{
				if(strtolower($val[0]) == strtolower($typeCode))
				{
					if($val[0] == 8075)
					{
						return array('zip','docx','xlsx');
					}
					else
					{
						return $val[1];
					}
				}
			}
        }
		return $filetype;
	}

	 
    public static function getTypeList()
    {
    	return array(
	    	array('255216','jpg'),
			array('13780','png'),
			array('7173','gif'),
			array('6677','bmp'),
			array('6063','xml'),
			array('60104','html'),
			array('208207','xls/doc'),
			array('8075','zip'),
			array('8075','docx'),
			array('8075','xlsx'),
			array("8297","rar"),
    	);
    }

	 
	public static function getFileSize($fileName)
	{
		return is_file($fileName) ? filesize($fileName):null;
	}

	 
	public static function isEmptyDir($dir)
	{
		if(is_dir($dir))
		{
			$isEmpty = true;
			$dirRes  = opendir($dir);
			while($fileName = readdir($dirRes))
			{
				if($fileName!='.' && $fileName!='..')
				{
					$isEmpty = false;
					break;
				}
			}
			closedir($dirRes);
			return $isEmpty;
		}
	}

	 
	public function save()
	{
		flock($this->resource,LOCK_UN);
	}

	 
	public static function getFileSuffix($fileName)
	{
		$fileInfoArray = pathinfo($fileName);
		return strtolower($fileInfoArray['extension']);
	}

	 
	function __destruct()
	{
		if(is_resource($this->resource))
		{
			fclose($this->resource);
		}
	}

	 
	public static function xcopy($source, $dest ,$oncemore = true)
	{
		if(!file_exists($source))
		{
			return "error: $source is not exist!";
		}
		if(is_dir($source))
		{
			if(file_exists($dest) && !is_dir($dest))
			{
				return "error: $dest is not a dir!";
			}
			if(!file_exists($dest))
			{
				mkdir($dest,0777);
			}
			$od = opendir($source);
			while($one = readdir($od))
			{
				if(in_array($one,self::$except))
				{
					continue;
				}
				$result = self::xcopy($source.DIRECTORY_SEPARATOR.$one, $dest.DIRECTORY_SEPARATOR.$one, $oncemore);
				if($result !== true)
				{
					return $result;
				}
			}
			closedir($od);
		}
		else
		{
			if(file_exists($dest) || is_dir($dest) )
			{
				if( func_num_args()>2 || $oncemore===true )
				{
					return "error: $dest is a dir!";
				}
				$result = self::xcopy($source, $dest.DIRECTORY_SEPARATOR.basename($source), $oncemore);
				if( $result !== true )
				{
					return $result;
				}
			}
			else
			{
				if(!file_exists(dirname($dest))) self::mkdir(dirname($dest));
				copy($source, $dest);
				chmod($dest,0777) and touch($dest, filemtime($source));
			}
		}
		return true;
	}

	public static function socket($url, $limit = 0, $post = '', $cookie = '', $ip = '', $timeout = 20, $block = TRUE)
	{
		$return = '';
		$matches = parse_url($url);
		!isset($matches['host']) && $matches['host'] = '';
		!isset($matches['path']) && $matches['path'] = '';
		!isset($matches['query']) && $matches['query'] = '';
		!isset($matches['port']) && $matches['port'] = '';
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		$port = !empty($matches['port']) ? $matches['port'] : 80;
		if($post)
		{
			$out = "POST $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= 'Content-Length: '.strlen($post)."\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cache-Control: no-cache\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
			$out .= $post;
		}
		else
		{
			$out = "GET $path HTTP/1.0\r\n";
			$out .= "Accept: */*\r\n";
			$out .= "Accept-Language: zh-cn\r\n";
			$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: $cookie\r\n\r\n";
		}
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
		if(!$fp)
		{
			return '';
		}
		else
		{
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if(!$status['timed_out'])
			{
				while (!feof($fp))
				{
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n"))break;
				}
				$stop = false;
				while(!feof($fp) && !$stop)
				{
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$return .= $data;
					if($limit)
					{
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			@fclose($fp);
			return $return;
		}
	}



}
?>
