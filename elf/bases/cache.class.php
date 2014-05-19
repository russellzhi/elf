<?php
namespace Elf;
class Cache {
	private $cache_path;
	private $cache_expire;
	public $caching;

	public function __construct(){
		$this->caching=CSTART;
		$this->cache_expire=CTIME;
		$m=str_replace('Controller','',$_GET['m']);
		$path=PROJECT_PATH."runtime/cache/".str_replace('./','',APP).'/'.TPLSTYLE."/".$m."/";
		if(!file_exists($path))
			mkdir($path,0777);
		$this->cache_path=$path;
	}

	private function fileName($key){
		return $this->cache_path.md5($key);
	}

	public function put($key, $data){
		$values = serialize($data);
		$filename = $this->fileName($key);
		$file = fopen($filename, 'w');
	    if ($file){
	        fwrite($file, $values);
	        fclose($file);
	    }
	    else return false;
	}

	public function get($key){
	if($this->caching==true){
		$filename = $this->fileName($key);
		if (!file_exists($filename) || !is_readable($filename)){
			return false;
		}
		if ( time() < (filemtime($filename) + $this->cache_expire) ) {
			$file = fopen($filename, "r");
	        if ($file){
	            $data = fread($file, filesize($filename));
	            fclose($file);
	            return unserialize($data);
	        }
	        else return false;
		}
		else return false;
	}else{
		return false;
	}
 	}
	
	public function clear($module,$app='Home'){
		$path=PROJECT_PATH."runtime/cache/".ucfirst(trim($app,'/')).'/'.TPLSTYLE."/".strtolower($module);
		if($module=="")
			$path=rtrim($path,'/');
		$handle=opendir($path);
		while(false!==($filename=readdir($handle))) {  
					$file=$path.'/'.$filename;
					if($filename!="." && $filename!="..") {   
						if(is_dir($file)) {
							\deldir($file);
						} else {
							unlink($file);
							echo "deleted $filename<br>";
						}           
					}
				}
				closedir($handle); 
	}	
		
	
}