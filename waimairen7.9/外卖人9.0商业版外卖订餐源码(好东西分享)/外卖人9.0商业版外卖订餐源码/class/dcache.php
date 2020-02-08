<?php  
/*** 
*    管理员日志
**/
class DCache  
{  
	private $control;
	private $act;
	private $extendname;
	private $backdata;
	private $checktime;
	private $lockfalg;//锁定标志
	function __construct(){   
	}
	//设置c
	public function C($control){
		 $this->control = $control;
		 return $this;
	}
	//设置act
	public function ACT($act){ 
		$this->act = $act;
		return $this;
	}
	//设置名称
	public function NAME($extendname){
		$this->extendname = $extendname;
		return $this;
	}
	
	public function SetTime($time){
		$this->checktime = $time;
		return $this;
		
	}
	  
	/* 
	返回所有查询结果
	*/
	public function Savedata($data){ 
	 
			$filename = $this->control.$this->act.$this->extendname;
			if(empty($filename)){
				return false;
			}
			if(empty($data)){
				return false;
			} 
			$filepath = hopedir.'/data_cache/'.md5($filename).'.php'; 
			$fp = fopen($filepath , 'w');    
			if(flock($fp , LOCK_EX|LOCK_NB)){    
				fwrite($fp , serialize($data));   
				flock($fp , LOCK_UN);    
			}    
			fclose($fp);  
	}
	function Check(){
		 
			if(empty($this->checktime)){
				return false;
			}
			$filename = $this->control.$this->act.$this->extendname;
			if(empty($filename)){
				return false;
			}
			$filepath = hopedir.'/data_cache/'.md5($filename).'.php';
			if(!file_exists($filepath)){
				return false;
			}  
			$checktime = filectime($filepath); 
			if($checktime > time()-$this->checktime){  
				$fp = fopen($filepath , 'r');    
				if(flock($fp , LOCK_SH)){    
					$backstr = fread($fp , filesize($filepath));    
					flock($fp , LOCK_UN);  
					fclose($fp); 
					$this->backdata = unserialize($backstr);
					return true;
				}else{   
					fclose($fp); 
					return false;
				}  
			} 
		    return false; 	
	}
	function del(){
		 
		$filename = $this->control.$this->act.$this->extendname;
		if(empty($filename)){
				return false;
		}
		$filepath = hopedir.'/data_cache/'.md5($filename).'.php';
		if(!file_exists($filepath)){
				IFile::unlink($filepath);
		} 
			 
	}
	function getdata(){
		return $this->backdata;
	}
	 
	 
	
}