<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
	header("Content-Type:text/html;charset=utf-8");
	date_default_timezone_set("PRC");
	define("ELF_PATH", str_replace("\\", "/", dirname(__FILE__)).'/');
	define("APP_PATH", rtrim(APP,'/').'/');
	define("PROJECT_PATH", dirname(ELF_PATH).'/');
	define("TMPPATH", str_replace(array(".", "/"), "_", ltrim($_SERVER["SCRIPT_NAME"], '/'))."/");
	
	$config=PROJECT_PATH."config.inc.php";
	if(file_exists($config)){
		include $config;
	}

	if(defined("DEBUG") && DEBUG==1){
		$GLOBALS["debug"]=1;
		error_reporting(E_ALL ^ E_NOTICE);
		include ELF_PATH."bases/debug.class.php";
		Debug::start();
		set_error_handler(array("Elf\Debug", 'Catcher'));
	}else{
		ini_set('display_errors', 'Off');
		ini_set('log_errors', 'On');
		ini_set('error_log', PROJECT_PATH.'runtime/error_log');

	}

	include ELF_PATH.'commons/functions.inc.php';
	$appname=ucfirst(substr(APP,2));
	
	$funfile=PROJECT_PATH."commons/functions.inc.php";
	if(file_exists($funfile))
		include $funfile;

	
	//set include path,  PATH_SEPARATOR ( Linux(:) Windows(;) )
	$include_path=get_include_path();
	$include_path.=PATH_SEPARATOR.ELF_PATH."bases/";
	$include_path.=PATH_SEPARATOR.ELF_PATH."classes/" ;
	$include_path.=PATH_SEPARATOR.PROJECT_PATH."classes/";
	$include_path.=PATH_SEPARATOR.APP_PATH.'controllers/';
	$include_path.=PATH_SEPARATOR.APP_PATH.'models/';
	set_include_path($include_path);
	
	spl_autoload_register('Elf\autoload');
	function autoload($className){	
	$className=substr(strrchr(__NAMESPACE__."\\".$className,"\\"),1);
	
		if($className=="memcache"){
			return;	
		}else{
			include strtolower($className).".class.php";	
		}
		Debug::addmsg("<b> $className </b> class", 1);
	}


	if(CSTART==0){
		Debug::addmsg("<font color='red'>没有开启页面缓存!</font>（但可以使用）"); 
	}else{
		Debug::addmsg("开启页面缓存，实现页面静态化!"); 
	}
	

	if(!empty($memServers)){
		if(extension_loaded("memcache")){
			$mem=new MemcacheModel($memServers);
			if(!$mem->mem_connect_error()){
				Debug::addmsg("<font color='red'>连接memcache服务器失败,请检查!</font>");
			}else{
				define("USEMEM",true);
				Debug::addmsg("启用了Memcache");
			}
		}else{
			Debug::addmsg("<font color='red'>PHP没有安装memcache扩展模块,请先安装!</font>");
		}	
	}else{
		Debug::addmsg("<font color='red'>没有使用Memcache</font>(为程序的运行速度，建议使用Memcache)");
	}

	if(defined("USEMEM")){
		MemSession::start($mem->getMem());
		Debug::addmsg("开启会话Session (使用Memcache保存会话信息)");
	}else{
		session_start();
		Debug::addmsg("<font color='red'>开启会话Session </font>(但没有使用Memcache，开启Memcache后自动使用)");
	}
	Debug::addmsg("会话ID:".session_id());
	
	Structure::create();
	Prourl::parseUrl();
	
	$spath=rtrim(substr(dirname(str_replace("\\", '/', dirname(__FILE__))), strlen(rtrim($_SERVER["DOCUMENT_ROOT"],"/\\"))), '/\\');
	$GLOBALS["root"]=$spath.'/';
	$GLOBALS["public"]=$GLOBALS["root"].'public/';
	$GLOBALS["res"]=rtrim(dirname($_SERVER["SCRIPT_NAME"]),"/\\").'/'.ltrim(APP_PATH, './')."views/".TPLSTYLE."/resource/";
	$GLOBALS["app"]=$_SERVER["SCRIPT_NAME"].'/';	
	$GLOBALS["url"]=$GLOBALS["app"].str_replace('Controller','',$_GET['m']).'/';

	define("B_ROOT", rtrim($GLOBALS["root"], '/'));
	define("B_PUBLIC", rtrim($GLOBALS["public"], '/'));
	define("B_APP", rtrim($GLOBALS["app"], '/'));
	define("B_URL", rtrim($GLOBALS["url"], '/'));
	define("B_RES", rtrim($GLOBALS["res"], '/'));
	
	
	
	
	/**
	*	language
	**/
	$lang = array();
	$languagefile= ELF_PATH."language/".LANGUAGE."/common_lang.php";
	if (file_exists($languagefile)) {
		$lang=include_once $languagefile;
		Debug::addmsg("加载 ELF 底层语言包文件: <b>$languagefile</b> ");
	}	
	$languagefile= PROJECT_PATH."language/".LANGUAGE."/common_lang.php";
	if (file_exists($languagefile)) {
		$lang=array_merge($lang,include_once $languagefile);
		Debug::addmsg("加载当前根目录下的全局语言包文件: <b>$languagefile</b> ");
	}
	$languagefile= APP_PATH."language/".LANGUAGE."/common_lang.php";
	if (file_exists($languagefile)) {
		$lang=array_merge($lang,include_once $languagefile);
		Debug::addmsg("加载项目目录下的全局语言包文件: <b>$languagefile</b> ");
	}
	$languagefile= APP_PATH."language/".LANGUAGE."/".str_replace('Controller','',$_GET['m'])."_lang.php";
	if (file_exists($languagefile)) {
		$lang=array_merge($lang,include_once $languagefile);
		Debug::addmsg("加载项目目录当前模块语言包文件: <b>$languagefile</b> ");
	}
	$languagefile= APP_PATH."language/".LANGUAGE."/".str_replace('Controller','',$_GET['m'])."/".$_GET["a"]."_lang.php";
	if (file_exists($languagefile)) {
		$lang=array_merge($lang,include_once $languagefile);
		Debug::addmsg("加载项目目录当前模块下的当前action语言包文件: <b>$languagefile</b> ");
	}
	$GLOBALS["lang"] = $lang;
	
	$srccontrollerfile=APP_PATH."controllers/".strtolower($_GET["m"]).".class.php";
	Debug::addmsg("当前访问的控制器类在项目应用目录下的: <b>$srccontrollerfile</b> 文件！");
	if(file_exists($srccontrollerfile)){
		$className=$appname."\Controller\\".ucfirst($_GET["m"]);
		$controller=new $className();
		$controller->run();
	}else{
		Debug::addmsg("<font color='red'>对不起!你访问的模块不存在,应该在".APP_PATH."controllers目录下创建文件名为".strtolower($_GET["m"]).".class.php的文件，声明一个类名为".ucfirst($_GET["m"])."的类！</font>");
		
	}

	if(defined("DEBUG") && DEBUG==1 && $GLOBALS["debug"]==1){
		Debug::stop();
		echo Debug::message();
	}