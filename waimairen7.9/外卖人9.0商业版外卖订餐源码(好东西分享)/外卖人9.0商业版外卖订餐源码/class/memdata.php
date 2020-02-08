<?php 
class memdata
{ 
    private  $mem;
	private static $_instance; 
	 
    public function __construct()
    {   
		$this->mem  = new Memcached();
		$this->mem->setOption(Memcached::OPT_CONNECT_TIMEOUT, 10);
		$this->mem->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
		$this->mem->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, 2);
		$this->mem->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
		$this->mem->setOption(Memcached::OPT_RETRY_TIMEOUT, 1); 
		$this->mem->setOption(Memcached::OPT_COMPRESSION, false); 
		$servers = array(
			array('127.0.0.1', 11211, 100) 
		); 
		$this->mem->addServers($servers);  
    }  
	public static function init(){ 
			if(!(self::$_instance instanceof self)){  
				self::$_instance = new self();  
			} 
			return self::$_instance; 
	} 
     
	public  function getkey($name)
    {
		if(empty($name)){
			return '';
		}
		$memkey = $name;
		$domainlist = $this->mem->get($memkey, null, $cas); 
		if ($this->mem->getResultCode() == Memcached::RES_NOTFOUND) { 
			return '';
		}else{
			return $domainlist;
		} 
        return '';
    }  
	public function setkey($name,$datas,$settime=''){
		$memkey = $name; 
		$domainlist = $this->mem->get($memkey, null, $cas); 
		if ($this->mem->getResultCode() == Memcached::RES_NOTFOUND) { 
			if(empty($settime)){
				$this->mem->add($memkey,$datas); 
			}else{
				$this->mem->add($memkey,$datas,$settime); 
			}
		} else {  
			if(empty($settime)){		
				$this->mem->cas($cas, $memkey, $datas);
			}else{
				$this->mem->cas($cas, $memkey, $datas,$settime);
			}
		} 
	}
	public function delete($name){
		$memkey = $name;
		$this->mem->delete($memkey); 
	}
	//清除所有数据
	public function clear(){ 
		$this->mem->flush(10);
	}
	function disclose(){ 
		$this->mem->quit(); 
	}
}
?>
