<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
class Dpdo extends DB{
/*********************the previous one***************************
	static $pdo=null;
	static function connect(){
		if(is_null(self::$pdo)) {
			try{
				if(defined("DSN"))
					$dsn=DSN;
				else
					$dsn="mysql:host=".HOST.";dbname=".DBNAME;
				$pdo=new \PDO($dsn, USER, PASS, array(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
				$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				//set charset
				$pdo->query("set names utf8");
				self::$pdo=$pdo;
				return $pdo;
			}catch(\PDOException $e){
				echo "连接数库失败：".$e->getMessage();
			}
		}else{
			return self::$pdo;
		}
	}
*****************************************************************************************/	
	
	public function __construct($config){
		global $database;
		$this->databaseconf=$database;
		$this->config=$config;
		$this->linkName=join('',$config);
		return $this->connect();
	}
	private function connect(){

		if(empty($this->linkId[$this->linkName])) {
			try{
				$dsn="mysql:host=".$this->databaseconf[$this->config[0]][$this->config[1]]['host'].";dbname=".$this->databaseconf[$this->config[0]][$this->config[1]]['dbname'];
				//如果你打算用多重查询结果，那么使用mysql时设置PDO的缓冲查询是非常重要的。
				$pdo=new \PDO($dsn, $this->databaseconf[$this->config[0]][$this->config[1]]['user'], $this->databaseconf[$this->config[0]][$this->config[1]]['pass'], array(\PDO::ATTR_PERSISTENT=>true,\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
				$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				$pdo->query("set names utf8");
				$this->linkId[$this->linkName]=$pdo;
				return $pdo;
			}catch(\PDOException $e){
				echo "连接数库失败：".$e->getMessage();
			}
		}else{
			//echo 'shit<br>';
			return $this->linkId[$this->linkName];
		}
	}
	
	
	

	/**
	 * @param	string	$sql
	 * @param	string	$method		select,find,total,insert,update,other
	 * @param	array	$data
	 * @return	mixed
	 */
	function query($sql, $method,$data=array()){
		 $startTime = microtime(true); 
		 $this->setNull();
		
		 $value=$this->escape_string_array($data);
		 $marr=explode("::", $method);
		 $method=strtolower(array_pop($marr));
		 if(strtolower($method)==trim("total")){
			$sql=preg_replace('/select.*?from/i','SELECT count(*) as count FROM',$sql);
		 }
		 $addcache=false;
		 $memkey=$this->sql($sql, $value);
		 if(defined("USEMEM")){
			 global $mem;
			 if($method == "select" || $method == "find" || $method=="total"){
				$data=$mem->getCache($memkey);
				if($data){
					return $data;
				}else{
					$addcache=true;	
				}
			 }

		 }
	
	
		 try{
			$return=null;
			$pdo=$this->connect();
			$stmt=$pdo->prepare($sql);
				$result=$stmt->execute($value);
		
			if(isset($mem) && !$addcache){
				if($stmt->rowCount()>0){
					$mem->delCache($this->tabName);
					Debug::addmsg("清除表<b>{$this->tabName}</b>在Memcache中所有缓存!");
				}
			}
				 
			 switch($method){
				 case "select":
					 $data=$stmt->fetchAll(\PDO::FETCH_ASSOC);

					 if($addcache){
						$mem->addCache($this->tabName, $memkey, $data);
					 }
					 $return=$data;
					break;
				case "find":
					$data=$stmt->fetch(\PDO::FETCH_ASSOC);

					 if($addcache){
						$mem->addCache($this->tabName, $memkey, $data);
					 }
					 $return=$data;
					break;

				case "total":
					$row=$stmt->fetch(\PDO::FETCH_NUM);

					 if($addcache){
						$mem->addCache($this->tabName, $memkey, $row[0]);
					 }
				
					$return=$row[0];
					break;
				case "insert":
					if($this->auto=="yes")
						$return=$pdo->lastInsertId();
					else
						$return=$result;
					break;
				case "delete":
				case "update":
					$return=$stmt->rowCount();
					break;
				default:
					$return=$result;
			 }
			$stopTime= microtime(true);
			$ys=round(($stopTime - $startTime) , 4);
			Debug::addmsg('[用时<font color="red">'.$ys.'</font>秒] - '.$memkey,2);
			return $return;
		}catch(\PDOException $e){
			Debug::addmsg("<font color='red'>SQL error: ".$e->getMessage().'</font>');
			Debug::addmsg("请查看：<font color='#005500'>".$memkey.'</font>');
		}	
	}

	function setTable($tabName){
		$cachepath=PROJECT_PATH."runtime/data/mysql/".md5($this->databaseconf[$this->config[0]][$this->config[1]]['host']).'/'.md5($this->databaseconf[$this->config[0]][$this->config[1]]['dbname']).'/';
		if(!file_exists($cachepath)){
			mkdir($cachepath,0777,true);
		}
		$cachefile=$cachepath.$tabName.".php";
		$this->tabName=$this->databaseconf[$this->config[0]][$this->config[1]]['tableprefix'].$tabName;
			
		if(!file_exists($cachefile)){
			try{
				$pdo=$this->connect();
				$stmt=$pdo->prepare("desc {$this->tabName}");
				$stmt->execute();
				$auto="yno";
				$fields=array();
				while($row=$stmt->fetch(\PDO::FETCH_ASSOC)){
					if($row["Key"]=="PRI"){
						$fields["pri"]=strtolower($row["Field"]);
					}else{
						$fields[]=strtolower($row["Field"]);
					}
					if($row["Extra"]=="auto_increment")
						$auto="yes";
				}
				if(!array_key_exists("pri", $fields)){
					$fields["pri"]=array_shift($fields);		
				}
				if(!DEBUG)
					file_put_contents($cachefile, "<?php ".json_encode($fields).$auto);
				$this->fieldList=$fields;
				$this->auto=$auto;
			}catch(\PDOException $e){
				Debug::addmsg("<font color='red'>异常：".$e->getMessage().'</font>');
			}
		}else{
			$json=ltrim(file_get_contents($cachefile),"<?ph ");
			$this->auto=substr($json,-3);
			$json=substr($json, 0, -3);
			$this->fieldList=(array)json_decode($json, true);	
		}
		Debug::addmsg("表<b>{$this->tabName}</b>结构：".implode(",", $this->fieldList),2); //debug
	}
	public function beginTransaction() {
		$pdo=$this->connect();
		$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 0); 
		$pdo->beginTransaction();
	}

	public function commit() {
		$pdo=$this->connect();
		$pdo->commit();
		$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1); 
	}

	public function rollBack() {
		$pdo=$this->connect();
		$pdo->rollBack();
		$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1); 
	}

	public function dbSize() {
		$sql = "SHOW TABLE STATUS FROM " . $this->databaseconf[$this->config[0]][$this->config[1]]['dbname'];
		if(isset($this->databaseconf[$this->config[0]][$this->config[1]]['tableprefix'])) {
			$sql .= " LIKE '".$this->databaseconf[$this->config[0]][$this->config[1]]['tableprefix']."%'";
		}
		$pdo=$this->connect();
		$stmt=$pdo->prepare($sql);
			$stmt->execute();
		$size = 0;
		while($row=$stmt->fetch(\PDO::FETCH_ASSOC))
			$size += $row["Data_length"] + $row["Index_length"];
		return tosize($size);
	}

	function dbVersion() {
		$pdo=$this->connect();
		return $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
	}
}
