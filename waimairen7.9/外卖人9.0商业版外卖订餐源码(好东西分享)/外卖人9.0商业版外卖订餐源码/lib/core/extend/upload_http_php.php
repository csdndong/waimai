<?php /*

*/
class upload
{
    var $saveName; // 保存名
    var $savePath; // 保存路径
    var $fileFormat = array('gif', 'jpg', 'png', 'application/octet-stream'); // 文件格式&MIME限定
    var $overwrite = 0; // 覆盖模式
    var $maxSize = 1048576; // 文件最大字节
    var $ext; // 文件扩展名
    var $thumb = 1; // 是否生成缩略图
    var $thumbWidth = 40; // 缩略图宽
    var $thumbHeight = 40; // 缩略图高
    var $thumbPrefix = "thumb_"; // 缩略图前缀
    var $errno; // 错误代号
    var $returnArray = array(); // 所有文件的返回信息
    var $returninfo = array(); // 每个文件返回信息
	var $Siteconfig = array();


    //构造函数
    // @param savePath 文件保存路径
    // @param fileFormat 文件格式限制数组
    // @param maxSize 文件最大尺寸
    // @param overwriet 是否覆盖 1 允许覆盖 0 禁止覆盖
   
    function upload($savePath, $fileFormat = '',$thumb=0, $maxSize = 0, $overwrite = 0 , $fileInput = 'imgFile' , $changeName=1){ 
		$info = webCfg::init(); 
		$this->Siteconfig = $info->getInfo();
		$this->thumb = $thumb;
        $this->setSavepath($savePath);
		$this->makeDirectory($savePath);//创建上传目录
        $this->setFileformat($fileFormat);
        $this->setMaxsize($maxSize);
        $this->setOverwrite($overwrite);
        $this->setThumb($this->thumb, $this->thumbWidth, $this->thumbHeight);
        $this->errno = 0;
		$this->savePath = $savePath;
		
		$this->run($fileInput,$changeName);
    }
	function upload_file($url,$filename,$path,$type,$input_name){
		try{
			if(!$this->imgCheck($path)){ 
				$this->del($path);
				throw new Exception("img fomate error");
			}
			/* php 5.5及以上
			$ch = curl_init($url); 
			$cfile = curl_file_create($path,$type,$filename);  
			$data = array( 
				$input_name=>$cfile,
				'img_large_height'=>isset($this->Siteconfig['img_large_height'])?$this->Siteconfig['img_large_height']:150,
				'img_large_width'=>isset($this->Siteconfig['img_large_width'])?$this->Siteconfig['img_large_width']:150,
				'img_middle_height'=>isset($this->Siteconfig['img_middle_height'])?$this->Siteconfig['img_middle_height']:150,
				'img_middle_width'=>isset($this->Siteconfig['img_middle_width'])?$this->Siteconfig['img_middle_width']:150,
				'img_small_height'=>isset($this->Siteconfig['img_small_height'])?$this->Siteconfig['img_small_height']:150,
				'img_small_width'=>isset($this->Siteconfig['img_small_width'])?$this->Siteconfig['img_small_width']:150,
				'savePath'=>$this->savePath,
				'input_name'=>$input_name
			);  
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_INFILESIZE,filesize($path)); //这句非常重要，告诉远程服务器，文件大小，查到的是前辈的文章
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			*/
			 
			 
		    //php 5.4 以及以下
			$data = array(
				$input_name=>'@'.realpath($path).";type=".$type.";filename=".$filename, 
				'img_large_height'=>isset($this->Siteconfig['img_large_height'])?$this->Siteconfig['img_large_height']:150,
				'img_large_width'=>isset($this->Siteconfig['img_large_width'])?$this->Siteconfig['img_large_width']:150,
				'img_middle_height'=>isset($this->Siteconfig['img_middle_height'])?$this->Siteconfig['img_middle_height']:150,
				'img_middle_width'=>isset($this->Siteconfig['img_middle_width'])?$this->Siteconfig['img_middle_width']:150,
				'img_small_height'=>isset($this->Siteconfig['img_small_height'])?$this->Siteconfig['img_small_height']:150,
				'img_small_width'=>isset($this->Siteconfig['img_small_width'])?$this->Siteconfig['img_small_width']:150,
				'savePath'=>$this->savePath,
				'input_name'=>$input_name
			);  
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			 
			
			$return_data = curl_exec($ch); 
			curl_close($ch);
			return $return_data; 
		}catch (Exception $e) { 
			$newdata = array();
			$newdata['error'] = true;
			$newdata['msg'] = 30;
			return json_encode($newdata);
		} 	
   }
   function imgCheck($filepath){ 
		if(in_array($this->ext,array('jpg','jpeg', 'png', 'gif'))){
			$imgtype = array('FFD8FF','89504E47','47494638','0xFFD8FF','0x89504E470D0A1A0A','GIF8');
			$checkinfo = file_get_contents($filepath); 
			$checkdata = strtoupper(bin2hex($checkinfo));
			if(strlen($checkdata) < 18){
					return false;
			} 
			foreach($imgtype as $key=>$value){
				$checkstr = substr($checkdata,0,strlen($value)); 
				if($checkstr == $value){
					return true;
				}
				
			} 
			return false; 
		}
		return true;
	}
    // 上传
    function run($fileInput, $changeName = 1)
    {
		 
	    $url =  webCfg::init()->img_url.'/testupload.php'; 
		if (isset($_FILES[$fileInput])) {
			$fileArr = $_FILES[$fileInput];
			if (is_array($fileArr['name'])) { ////上传同文件域名称多个文件 
			 
                for ($i = 0; $i < count($fileArr['name']); $i++) {
					$this->getExt($fileArr['name'][$i]);
					 $ar['tmp_name'] = $fileArr['tmp_name'][$i];
                    $ar['name'] = $fileArr['name'][$i];
                    $ar['type'] = $fileArr['type'][$i];
                    $ar['size'] = $fileArr['size'][$i];
                    $ar['error'] = $fileArr['error'][$i];
					
					$tmpname = $_FILES[$fileInput]['name'];
					$tmpfile = $_FILES[$fileInput]['tmp_name'];
					$tmpType = $_FILES[$fileInput]['type'];
					
					$backdata = array();
					$backdata['name'] = $ar['name'];
					$backdata['saveName'] = '';
					$backdata['size'] = number_format(($ar['size']) / 1024, 0, '.',' '); //以KB为单位
					$backdata['type'] = $ar['type']; 
					
				 
					$tempinfo = $this->upload_file($url,$ar['name'],$ar['tmp_name'],$ar['type'],$fileInput);
					$info = json_decode($tempinfo,true);
				    if($info['error'] == false){
						$backdata['saveName']  = $info['msg'];
						 $this->returnArray[] = $backdata;
					}else{
						  $backdata['error'] =  $info['msg'];
						  $this->errno =  $info['msg'];
					 	  $this->returnArray[] = $backdata;
					}
					
					
				}
				return $this->errno ? false : true;				
			}else{//非数组
					$this->getExt($_FILES[$fileInput]['name']);
				    $tmpname = $_FILES[$fileInput]['name'];
					$tmpfile = $_FILES[$fileInput]['tmp_name'];
					$tmpType = $_FILES[$fileInput]['type'];
					
					$backdata = array();
					$backdata['name'] = $_FILES[$fileInput]['name'];
					$backdata['saveName'] = '';
					$backdata['size'] = number_format(($_FILES[$fileInput]['size']) / 1024, 0, '.',' '); //以KB为单位
					$backdata['type'] = $_FILES[$fileInput]['type']; 
					$backdata['error'] = 0;
					$tempinfo = $this->upload_file($url,$tmpname,$tmpfile,$tmpType,$fileInput); 
					 $info = json_decode($tempinfo,true);
				  
					if($info['error'] == false){
						 $backdata['saveName']  = $info['msg']; 
						 $this->returnArray[] = $backdata;
					}else{
						  $backdata['error'] =  $info['msg'];
					 	  $this->returnArray[] = $backdata;
						   $this->errno =  $info['msg'];
					}
					 return $this->errno ? false : true;
			} 
		}  else {
            $this->errno = 10;
            return false;
        }
    }
	function getExt($fileName)
    {
        $ext = explode(".", $fileName);
        $ext = $ext[count($ext) - 1];
		$ext = $ext=="jpeg" ? "jpg" : $ext;
        $this->ext = strtolower($ext);
    }

    //设置上传文件的最大字节限制
    // @param $maxSize 文件大小(bytes) 0:表示无限制
    function setMaxsize($maxSize)
    {
        $this->maxSize = $maxSize;
    }
    //设置文件格式限定
    // @param $fileFormat 文件格式数组
    function setFileformat($fileFormat)
    {
        if (is_array($fileFormat)) {
            $this->fileFormat = $fileFormat;
        }
    }

    //设置覆盖模式
    // @param overwrite 覆盖模式 1:允许覆盖 0:禁止覆盖
    function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }


    //设置保存路径
    // @param $savePath 文件保存路径：以 "/" 结尾，若没有 "/"，则补上
    function setSavepath($savePath)
    {
        $this->savePath = substr(str_replace("\\", "/", $savePath), -1) == "/" ? $savePath :
            $savePath . "/";
    }

    //设置缩略图
    // @param $thumb = 1 产生缩略图 $thumbWidth,$thumbHeight 是缩略图的宽和高
    function setThumb($thumb, $thumbPrefix, $thumbWidth = 0, $thumbHeight = 0)
    {
        $this->thumb = $thumb;
        $this->thumbPrefix = $thumbPrefix;
        if ($thumbWidth)
            $this->thumbWidth = $thumbWidth;
        if ($thumbHeight)
            $this->thumbHeight = $thumbHeight;
    }

    //设置文件保存名
    // @saveName 保存名，如果为空，则系统自动生成一个随机的文件名
    function setSavename($saveName)
    {
        if ($saveName == '') { // 如果未设置文件名，则生成一个随机文件名
            $name = date('YmdHis') . rand(100, 999) . '.' . $this->ext;
        } else {
            $name = $saveName;
        }
        $this->saveName = $name;
    }

    //删除文件
    // @param $file 所要删除的文件名
    function del($fileName)
    {
        if (!@unlink($fileName)) {
            $this->errno = 15;
            return false;
        }
        return true;
    }

    // 返回单个上传文件的信息
    function getInfo()
    {
        return $this->returninfo;
    }


    //返回多个上传文件信息

    function getfile()
    {
        return $this->returnArray;
    }


    // 得到错误信息
    function errmsg()
    {
        $errormsg = array(
							0 => '1',
							1 => '上传的文件过大!',
							2 => '上传的文件过大!',
							3 => '文件只有部分被上传!', 
							4 => '没有提交任何上传信息!', 
							6 => '创建缩略图失败，你的PHP版本过低!', 
							7 => '创建缩略图失败，你的PHP版本过低!', 
							10 => '表单文件域不存在!', 
							11 => '不允许上传该格式文件!', 
							12 => '上传目录不存在或不可写!', 
							13 => '该文件已上传!', 
							14 => '上传的文件过大!',
							15 => '1', 
							16 => 'Your version of PHP does not appear to have GIF thumbnailing support.',
							17 => 'Your version of PHP does not appear to have JPEG thumbnailing support.',
							18 => 'Your version of PHP does not appear to have pictures thumbnailing support.',
							19 => 'An error occurred while attempting to copy the source image . Your version of php (' . phpversion() . ') may not have this image type support.',
							20 =>'An error occurred while attempting to create a new image.', 
							21 =>'An error occurred while copying the source image to the thumbnail image.', 
							22 =>'An error occurred while saving the thumbnail image to the filesystem.Are you sure that PHP has been configured with both read and write access on this folder?',
							23=>'网站未设置图片最大上传大小',
							24=>'网站未设置大图高度',
							25=>'网站未设置大图宽度',
							26=>'网站未设置中图高度',
							27=>'网站未设置中图宽度',
							28=>'网站未设置小图高度',
							29=>'网站未设置小图宽度',
							30=>'跨平台提交php命令不支持curl',
					);
        if ($this->errno == 0)
            return false;
        else
            return $errormsg[$this->errno];
    }

    //创建目录

    function makeDirectory($directoryName)
    {

        $temp = $directoryName;

        if (!is_dir($temp)) {
            $oldmask = umask(0);
            if (!mkdir($temp, 0777))
                exit("不能建立目录 $temp");
            umask($oldmask);
        }

        return $temp;
    } 
    function imageWaterMark($groundImage,$waterPos=0,$waterImage="",$waterText="",$fontSize=12,$textColor="#CCCCCC", $fontfile='font.ttf',$xOffset=0,$yOffset=0)
   {
   	global $config; 
   	 if($config['is_water'] != 1){
   	  return 0;
   	 }
   	 $waterPos = intval($config['water_pos']);
   	  $waterImage = empty($config['logo_water'])?'':hopedir.$config['logo_water'];
   	   $waterText = $config['text_water'];
   	  $fontSize = intval($config['size_water']);
   	  $textColor = $config['color_water'];
      $isWaterImage = FALSE;
     //读取水印文件
     if(!empty($waterImage) && file_exists($waterImage)) {
         $isWaterImage = TRUE;
         $water_info = getimagesize($waterImage);
         $water_w     = $water_info[0];//取得水印图片的宽
         $water_h     = $water_info[1];//取得水印图片的高

         switch($water_info[2])   {    //取得水印图片的格式  
             case 1:$water_im = imagecreatefromgif($waterImage);break;
             case 2:$water_im = imagecreatefromjpeg($waterImage);break;
             case 3:$water_im = imagecreatefrompng($waterImage);break;
             default:return 1;
         }
     }

     //读取背景图片
     if(!empty($groundImage) && file_exists($groundImage)) {
         $ground_info = getimagesize($groundImage);
         $ground_w     = $ground_info[0];//取得背景图片的宽
         $ground_h     = $ground_info[1];//取得背景图片的高

         switch($ground_info[2]) {    //取得背景图片的格式  
             case 1:$ground_im = imagecreatefromgif($groundImage);break;
             case 2:$ground_im = imagecreatefromjpeg($groundImage);break;
             case 3:$ground_im = imagecreatefrompng($groundImage);break;
             default:return 1;
         }
     } else { 
         return 2;
     }

     //水印位置
     if($isWaterImage) { //图片水印  
         $w = $water_w;
         $h = $water_h;
         $label = "图片的";
         } else {  
     //文字水印
        $fontfile =dirname(__FILE__).'/'.$fontfile;
        if(!file_exists($fontfile))return 4;
         $temp = imagettfbbox($fontSize,0,$fontfile,$waterText);//取得使用 TrueType 字体的文本的范围
         $w = $temp[2] - $temp[6];
         $h = $temp[3] - $temp[7];
         unset($temp);
     }
     if( ($ground_w < $w) || ($ground_h < $h) ) {
         return 3;
     }
     switch($waterPos) {
         case 0://随机
             $posX = rand(0,($ground_w - $w));
             $posY = rand(0,($ground_h - $h));
             break;
         case 1://1为顶端居左
             $posX = 0;
             $posY = 0;
             break;
         case 2://2为顶端居中
             $posX = ($ground_w - $w) / 2;
             $posY = 0;
             break;
         case 3://3为顶端居右
             $posX = $ground_w - $w;
             $posY = 0;
             break;
         case 4://4为中部居左
             $posX = 0;
             $posY = ($ground_h - $h) / 2;
             break;
         case 5://5为中部居中
             $posX = ($ground_w - $w) / 2;
             $posY = ($ground_h - $h) / 2;
             break;
         case 6://6为中部居右
             $posX = $ground_w - $w;
             $posY = ($ground_h - $h) / 2;
             break;
         case 7://7为底端居左
             $posX = 0;
             $posY = $ground_h - $h;
             break;
         case 8://8为底端居中
             $posX = ($ground_w - $w) / 2;
             $posY = $ground_h - $h;
             break;
         case 9://9为底端居右
             $posX = $ground_w - $w;
             $posY = $ground_h - $h;
             break;
         default://随机
             $posX = rand(0,($ground_w - $w));
             $posY = rand(0,($ground_h - $h));
             break;     
     }

     //设定图像的混色模式
     imagealphablending($ground_im, true);

     if($isWaterImage) { //图片水印
         imagecopy($ground_im, $water_im, $posX + $xOffset, $posY + $yOffset, 0, 0, $water_w,$water_h);//拷贝水印到目标文件         
     } else {//文字水印
         if( !empty($textColor) && (strlen($textColor)==7) ) {
             $R = hexdec(substr($textColor,1,2));
             $G = hexdec(substr($textColor,3,2));
             $B = hexdec(substr($textColor,5));
         } else {
           return 5;
         }
         imagettftext ( $ground_im, $fontSize, 0, $posX + $xOffset, $posY + $h + $yOffset, imagecolorallocate($ground_im, $R, $G, $B), $fontfile, $waterText);
     }

     //生成水印后的图片
     @unlink($groundImage);
     switch($ground_info[2]) {//取得背景图片的格式
         case 1:imagegif($ground_im,$groundImage);break;
         case 2:imagejpeg($ground_im,$groundImage);break;
         case 3:imagepng($ground_im,$groundImage);break;
         default: return 6;
     }

     //释放内存
     if(isset($water_info)) unset($water_info);
     if(isset($water_im)) imagedestroy($water_im);
     unset($ground_info);
     imagedestroy($ground_im);
     //
     return 0;
  }

}
?>