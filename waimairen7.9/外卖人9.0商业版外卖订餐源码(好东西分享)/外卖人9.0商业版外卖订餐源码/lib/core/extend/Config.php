<?php
 
class Config
{
	  private $configFile;
    private $config;
    private $base;
   
    public function __construct($config,$baseurl)
    {
    	  $this->base = $baseurl;
        $this->initConfig($config);
        
    }

  
    public function setConfig($config)
    {
        $this->initConfig($config);
    }
 
    public function getInfo()
    {
    	return $this->config;
    }

  
    private function initConfig($config)
    {
    	   
        if(file_exists($this->base.'config/'.$config))
        {
        	$this->configFile = $this->base.'config/'.$config;
        	$this->config     = include($this->configFile); 
        }
        else
        	$this->config = null;  
    }

   
    public function __get($name)
    {
        if(isset($this->config[$name]))
        {
            return $this->config[$name];
        }
        else
        {
            $value = null;
            switch($name)
            {
                case 'list_thumb_width' :$value=100;break;
                case 'list_thumb_height':$value=100;break;
                case 'show_thumb_width' :$value=100;break;
                case 'show_thumb_height':$value=100;break;
            }
            return $value;
        }
        return '';
    }

   
    public function write($inputArray)
    {
    	
    	self::edit($this->configFile , $inputArray);
    }

 
	public static function edit($configFile,$inputArray)
	{
		//安全过滤要写入文件的内容 
		$configStr = "";

		//读取配置信息内容
		if(file_exists($configFile))
		{
			$configStr   = file_get_contents($configFile);
			$configArray = include($configFile);
		}

		if(trim($configStr)=="")
		{
			$configStr   = "<?php return array( \r\n);?>";
			$configArray = array();
		}

		//表单中存在但是不进行录用的键值
		$except = array('form_index');

		foreach($except as $value)
		{
			unset($inputArray[$value]);
		}

		$inputArray = array_merge($configArray,$inputArray);
		$configData = var_export($inputArray,true);
		$configStr = "<?php return {$configData}?>";
   
		//写入配置文件
		$fileObj   = new IFile($configFile,'w+');
		 
		$fileObj->write($configStr);
	}
}
?>
