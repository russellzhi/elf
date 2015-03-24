<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
/**
 * print data with human-readable format
 * @param mixed
 * @return string html
 */
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

/**
 * initialize a model
 * @param string 	className 	model name or table name
 * @param string 	app 		if set,it will use the other application's model,e.g. Admin
 * @param array 	config 		set database type and config info
 * @return object
 */
function D($className=null,$app="",$config=array('mysql','one')){
	$db=null;
	if(is_null($className)){
		$class="Elf\\D".DRIVER;
		$db=new $class($config);
	}else{
		$className=strtolower($className);
		if($app==""){
		
		/***** start automatically create model *****/
			$model_path=APP_PATH."models/";
			$model_file=$model_path.$className."model.class.php";
			global $appname;
			$space=ucfirst($appname)."\\Model";
			$modelName=ucfirst($className);
			if(!file_exists($model_file)){
				$str=<<<str
<?php
namespace $appname\\Model;
class {$modelName}Model extends \\Elf\\Dpdo{

}
str;
				file_put_contents($model_file,$str);
			}
		/***** end automatically create model *****/

		}else{
			$inpa=PROJECT_PATH.$app."/models/";//inpa means include path
			set_include_path(get_include_path() . PATH_SEPARATOR . $inpa);
			$model_file=$inpa.$className."model.class.php";				
			$space=ucfirst($app)."\\Model";
		}

		include_once $model_file;
		$model=$space.'\\'.ucfirst($className)."Model";
		$model=new $model($config);
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

/**
 * D api - no creating file if not exists
 * initialize a model
 * @param string 	className 	model name or table name
 * @param string 	app 		if set,it will use the other application's model,e.g. Admin
 * @param array 	config 		set database type and config info
 * @return object
 */
function DA($className=null,$app="",$config=array('mysql','one')){
	$db=null;
	if(is_null($className)){
		$class="Elf\\D".DRIVER;
		$db=new $class($config);
	}else{
		$className=strtolower($className);
		if($app==""){
			$model_path=APP_PATH."models/";
			$model_file=$model_path.$className."model.class.php";
			global $appname;
			$space=ucfirst($appname)."\\Model";
			$modelName=ucfirst($className);
		}else{
			$inpa=PROJECT_PATH.$app."/models/";//inpa means include path
			set_include_path(get_include_path() . PATH_SEPARATOR . $inpa);
			$model_file=$inpa.$className."model.class.php";				
			$space=ucfirst($app)."\\Model";
		}
		if(!file_exists($model_file)){
			return false;
		}
		include_once $model_file;
		$model=$space.'\\'.ucfirst($className)."Model";
		$model=new $model($config);
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

//strlenofchina
function strlenofchina($str){
	$ch_amont = 0;
	$en_amont = 0;
	$str = preg_replace("/(　| ){1,}/", " ", $str);
	for($i=0;$i<strlen($str);$i++)
	{
		$ord = ord($str{$i});   
		if($ord > 128)
			$ch_amont++;
		else
			$en_amont++;
	}
	return ($ch_amont/3) + $en_amont;
}

function substrofchina($string, $sublen, $start = 0, $code = 'UTF-8'){
	if($code == 'UTF-8')
	{
		 $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		 preg_match_all($pa, $string, $t_string);
		 if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
		 return join('', array_slice($t_string[0], $start, $sublen));
	}
	else
	{
		 $start = $start*2;
		 $sublen = $sublen*2;
		 $strlen = strlen($string);
		 $tmpstr = '';
		 for($i=0; $i<$strlen; $i++)
		 {
		 if($i>=$start && $i<($start+$sublen))
		 {
		 if(ord(substr($string, $i, 1))>129) $tmpstr.= substr($string, $i, 2);
		 else $tmpstr.= substr($string, $i, 1);
		 }
		 if(ord(substr($string, $i, 1))>129) $i++;
		 }
		 if(strlen($tmpstr)<$strlen ) $tmpstr.= "...";
		 return $tmpstr;
	}
}

/**
 * 根据栏目id（pid）获取该栏目，子栏目...
 * 然后用这些获取这些栏目的所有文章（递归）
 * 用在首页，频道页
 * @param int pid
 * @return string level
 * @author Ddz <russellzhi@163.com> 2014-10-15
 */
function get_art_col($pid){
	$level.=$pid;
	$arts=D('column')->field('id,pid')->where(array('pid'=>$pid))->select();
	if($arts){
		foreach($arts as $k2=>$v2){
			$level.=','.get_art_col($v2['id']);
		}
	}
	return $level;
}

/**
 * 根据提供的pid，获取相关文章
 * @param string col
 * @param string order 排序规则
 * @param int    num   获取几篇
 * @return array
 * @author Ddz <russellzhi@163.com> 2014-10-15
 */
function get_art_by_col($col,$order='posttime desc',$num=9){
	$pids=explode(',',$col);
	$articles=D('article')->field('id,title,summary,posttime')->where(array('pid'=>$pids))->order($order)->limit($num)->select();
	foreach($articles as $key=>$val){
		$articles[$key]['posttime']=date('Y-m-d',$val['posttime']);
		$articles[$key]['timestamp']=$val['posttime'];
	}
	return $articles;
}

/**
 * model层返回数据函数
 * @param array data 数据库返回结果
 * @param string msg 当返回数据失败时的错误消息
 * @return array
 * @author Ddz <russellzhi@163.com> 2015-02-03
 */
function model_data($data,$msg=Elf\ErrorCode::errorMessage){
	if($data){
		return array('errNum'=>Elf\ErrorCode::errorSuccess,'data'=>$data);
	}else{
		return array('errNum'=>Elf\ErrorCode::errorFailure,'msg'=>$msg);
	}
}

/**
 * 加载配置文件
 */
function C($file='common'){
	$arr=include PROJECT_PATH.'conf/'.$file.'.php';
	return $arr;
}