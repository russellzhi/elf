<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
class Structure {
	static $mess=array();

	static function touch($fileName, $str){
		if(!file_exists($fileName)){
			if(file_put_contents($fileName, $str)){
				self::$mess[]="创建文件 {$fileName} 成功.";
			}
		}
	}

	static function mkdir($dirs){
		foreach($dirs as $dir){
			if(!file_exists($dir)){
				if(mkdir($dir,0755)){
					self::$mess[]="创建目录 {$dir} 成功.";
				}
			}
		}
	}

	static function runtime(){
		$dirs=array(
				PROJECT_PATH."runtime/cache/",
				PROJECT_PATH."runtime/cache/".ltrim(APP,'./')."/",
				PROJECT_PATH."runtime/cache/".ltrim(APP,'./')."/".TPLSTYLE,
				PROJECT_PATH."runtime/comps/",
				PROJECT_PATH."runtime/comps/".TPLSTYLE,
				PROJECT_PATH."runtime/comps/".TPLSTYLE."/".TMPPATH,
				PROJECT_PATH."runtime/data/",
				PROJECT_PATH."runtime/data/mysql/",
			);
		self::mkdir($dirs);
	}

	static function create(){
		self::mkdir(array(PROJECT_PATH."runtime/"));
		$structFile=PROJECT_PATH."runtime/".str_replace("/","_",$_SERVER["SCRIPT_NAME"]);

		if(!file_exists($structFile)) {	
			$fileName=PROJECT_PATH."config.inc.php";
			$str=<<<st
<?php
	define("DEBUG", 1);
	define("DRIVER","pdo");
	define("CSTART",false);
	define("CTIME", 60*60*24*7);
	define("TPLSUFFIX", "html");
	define("LANGUAGE","zh-cn");
	define("AUTHTABPREFIX","spr_");
	//\$memServers = array("localhost", 11211);
	/*
	\$memServers = array(
			array("www.example_url.net", '11211'),
			array("www.example_url.com", '11211'),
			...
		);
	*/
	//database
	\$database=array(
		'mysql'=>array(
			'one'=>array(
				'host'=>'localhost',
				'user'=>'root',
				'pass'=>'shit',
				'dbname'=>'zhicms',
				'tableprefix'=>'spr_',
			),
			'two'=>array(
				'host'=>'localhost',
				'user'=>'root',
				'pass'=>'shit',
				'dbname'=>'zhicms2',
				'tableprefix'=>'ding_',
			),
		),
	);
st;
			self::touch($fileName, $str);
			if(!defined("DEBUG"))
				include $fileName;
			$dirs=array(
				PROJECT_PATH."classes/",
				PROJECT_PATH."commons/",
				PROJECT_PATH."public",
				PROJECT_PATH."public/uploads/",
				PROJECT_PATH."public/css/",
				PROJECT_PATH."public/js/",
				PROJECT_PATH."public/images/",
				PROJECT_PATH."language/",
				PROJECT_PATH."language/zh-cn/",
				APP_PATH,
				APP_PATH."models/",
				APP_PATH."controllers/",
				APP_PATH."views/",
				APP_PATH."views/".TPLSTYLE,
				APP_PATH."views/".TPLSTYLE."/public/",
				APP_PATH."views/".TPLSTYLE."/resource/",
				APP_PATH."views/".TPLSTYLE."/resource/css/",
				APP_PATH."views/".TPLSTYLE."/resource/js/",
				APP_PATH."views/".TPLSTYLE."/resource/images/",
				APP_PATH."language/",
				APP_PATH."language/zh-cn/",
				APP_PATH."routes/",
			);
		
			self::mkdir($dirs);
			self::touch(PROJECT_PATH."commons/functions.inc.php", "<?php\n\t");
			self::touch(PROJECT_PATH."language/zh-cn/common_lang.php", "<?php\nreturn array(\n\n);");
			self::touch(APP_PATH."language/zh-cn/common_lang.php", "<?php\nreturn array(\n\n);");
			self::touch(APP_PATH."routes/routes.php", "<?php\nreturn array(\n\n);");
			
			$success=APP_PATH."views/".TPLSTYLE."/public/success.".TPLSUFFIX;
			if(!file_exists($success))
				copy(ELF_PATH."commons/success",$success);
			$namespace_name=trim(APP,'./');		
			$str=<<<st
<?php
namespace $namespace_name\Controller;
use Elf;
	class Common extends Elf\Controller {
		function init(){

		}		
	}
st;
			self::touch(APP_PATH."controllers/common.class.php", $str);
			$str=<<<st
<?php
namespace $namespace_name\Controller;
	class IndexController extends Common {
		function index(){
			echo "<b>Hello buddy,welcome to Elf,the structure is: </b><br>";
			echo '<pre>';
			echo file_get_contents('{$structFile}');
			echo '</pre>';
		}
	}
st;
			self::touch(APP_PATH."controllers/indexcontroller.class.php", $str);
			self::touch($structFile, implode("\n", self::$mess));			
		}
		self::runtime();
	}
}
