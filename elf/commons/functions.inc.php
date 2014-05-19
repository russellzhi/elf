<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/

	function p(){
		$args=func_get_args();
		if(count($args)<1){
			Debug::addmsg("<font color='red'>必须为p()函数提供参数!");
			return;
		}	

		echo '<div style="width:100%;text-align:left"><pre>';
		foreach($args as $arg){
			if(is_array($arg)){  
				print_r($arg);
				echo '<br>';
			}else if(is_string($arg)){
				echo $arg.'<br>';
			}else{
				var_dump($arg);
				echo '<br>';
			}
		}
		echo '</pre></div>';	
	}
	
	function D($className=null,$app=""){
		$db=null;	
		if(is_null($className)){
			$class="Elf\\D".DRIVER;

			$db=new $class;
		}else{
			$className=strtolower($className);
			if($app==""){
				$model_file=APP_PATH."models/".$className."model.class.php";
				global $appname;
				$space=ucfirst($appname)."\\Model";
			}else{
				$inpa=PROJECT_PATH.$app."/models/";//inpa means include path
				set_include_path(get_include_path() . PATH_SEPARATOR . $inpa);
				$model_file=$inpa.$className."model.class.php";				
				$space=ucfirst($app)."\\Model";
			}

			include_once $model_file;
			$model=$space.'\\'.ucfirst($className)."Model";
			$model=new $model();

			$tablename=$className;
			$model->setTable($tablename);		
		

			$db=$model;
		}
		if($app=="")
			$db->path=APP_PATH;
		else
			$db->path=PROJECT_PATH.strtolower($app).'/';
		return $db;
	}

	function tosize($bytes) {
		if ($bytes >= pow(2,40)) {
			$return = round($bytes / pow(1024,4), 2);
			$suffix = "TB";
		} elseif ($bytes >= pow(2,30)) {
			$return = round($bytes / pow(1024,3), 2);
			$suffix = "GB";
		} elseif ($bytes >= pow(2,20)) {
			$return = round($bytes / pow(1024,2), 2);
			$suffix = "MB";
		} elseif ($bytes >= pow(2,10)) {
			$return = round($bytes / pow(1024,1), 2);
			$suffix = "KB";
		} else {
			$return = $bytes;
			$suffix = "Byte";
		}
		return $return ." " . $suffix;
	}

	function debug($falg=0){
		$GLOBALS["debug"]=$falg;
	}
	
	function lang() {
		$args = func_get_args ();
		$lang = $GLOBALS ["lang"];
		if (count ( $args ) <= 0)
			return $lang;
		$_s = "lang";
		foreach ( $args as $i => $arg ) {
			$_s .= "['" . $args [$i] . "']";
			if (!is_array($lang) OR empty ( $lang [$arg] )) {
				Elf\Debug::addmsg ( "<font color='red'>要输出的语言项目".$_s."不存在!" );
				return;
			}
			$lang = $lang [$arg];
		}
		return $lang;
	}


