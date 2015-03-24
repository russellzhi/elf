<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
class MemcacheModel {
	private $mc = null;

	function __construct($servers){
		$mc = new \Memcache;

		if(is_array($servers[0])){
			foreach ($servers as $server){
				call_user_func_array(array($mc, 'addServer'), $server);
			}
		} else {
			call_user_func_array(array($mc, 'addServer'), $servers);
		
		}
		$this->mc=$mc;
	}

	function getMem(){
		return $this->mc;
	}

	function mem_connect_error(){
		$stats=$this->mc->getStats();
		if(empty($stats)){
			return false;
		}else{
			return true;
		}
	}

	private function addKey($tabName, $key){
		
		$keys=$this->mc->get($tabName);
		if(empty($keys)){
			$keys=array();
		}
		
		if(!in_array($key, $keys)) {
			$keys[]=$key;
			$this->mc->set($tabName, $keys, MEMCACHE_COMPRESSED, 0);
			return true;
		}else{
			return false;
		}
	}

	function addCache($tabName, $sql, $data){
	
		$key=md5($sql);
		
		if($this->addKey($tabName, $key)){
			$this->mc->set($key, $data, MEMCACHE_COMPRESSED, 0);
		}
	}

	function getCache($sql){
		$key=md5($sql);
		return $this->mc->get($key);
	}

	function delCache($tabName){
		$keys=$this->mc->get($tabName);
	
		if(!empty($keys)){
			foreach($keys as $key){
				$this->mc->delete($key, 0);
			}
		}
		$this->mc->delete($tabName, 0); 
	}

	function delone($sql){
		$key=md5($sql);
		$this->mc->delete($key, 0);
	}
}
