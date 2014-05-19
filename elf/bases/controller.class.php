<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
	class Controller extends Mytpl{
		function run(){
			if($this->left_delimiter!="<{")
				parent::__construct();	
			if(method_exists($this, "init")){
				call_user_func(array($this, "init"));		
			}

			$method=$_GET["a"];
			if(method_exists($this, $method)){
				call_user_func(array($this, $method));
			}else{
				Debug::addmsg("<font color='red'>没有{$_GET["a"]}这个操作！</font>");
			}	
		}

		/** 
		 * @param	string	$path
		 * @param	string	$args 
		 * 
		 * $this->redirect("index")  /current controller/index
		 * $this->redirect("user/index") /user/index
		 * $this->redirect("user/index", 'page/5') /user/index/page/5
		 */
		function redirect($path, $args=""){
			$path=trim($path, "/");
			if($args!="")
				$args="/".trim($args, "/");
			if(strstr($path, "/")){
				$url=$path.$args;
			}else{
				//$url=$_GET["m"]."/".$path.$args;
				$url=str_replace("Controller","",$_GET["m"])."/".$path.$args;
			}

			$uri=B_APP.'/'.$url;
			echo '<script>';
			echo 'location="'.$uri.'"';
			echo '</script>';
		}

		function success($mess="WELL DONE!", $timeout=1, $location=""){
			$this->pub($mess, $timeout, $location);
			$this->assign("mark", true);
			$this->display("public/success");
			exit;
		}

		function error($mess="DAMN IT!", $timeout=3, $location=""){
			$this->pub($mess, $timeout, $location);
			$this->assign("mark", false);
			$this->display("public/success");
			exit;
		}

		private function pub($mess, $timeout, $location){	
			$this->caching=0;     //in the template engine used,the caching property doesn't exist
			if($location==""){
				$location="window.history.back();";
			}else{
				$path=trim($location, "/");
			
				if(strstr($path, "/")){
					$url=$path;
				}else{
					$url=str_replace('Controller','',$_GET["m"])."/".$path;
				}
				$location=B_APP.'/'.$url;
				$location="window.location='{$location}'";
			}

			if(is_array($mess))
				$mess = implode('<br>', $mess);

			$this->assign("mess", $mess);
			$this->assign("timeout", $timeout);
			$this->assign("location", $location);
			debug(0);
		}
		
		function load_lang($lang_file="", $app = ""){
			if(empty($lang_file)) {
				Debug::addmsg("<font color='red'>必须为load_lang()函数提供需要加载的语言包名称!");
				return ;
			}
			$app = empty($app)?$_GET["m"]:$app;
			$languagefile = $app=="public"?PROJECT_PATH."language/".LANGUAGE."/".$lang_file."_lang.php":(APP_PATH."language/".LANGUAGE."/".$lang_file."_lang.php");
			if (file_exists($languagefile)) {
				$_lang = $GLOBALS["lang"];
				$lang = array();
				include_once $languagefile;
				$_lang = array_merge($_lang,$lang);
				$GLOBALS["lang"] = $_lang;
				$this->assign("lang",$_lang);
				Debug::addmsg("自定义加载语言包文件: <b>$languagefile</b> ");
			}
		}
		function show($cache){
				if($cache->caching==true){
					ob_start();
					$this->display();
					$output=ob_get_contents();
					ob_end_clean();
					$this->display();
					$cache->put($_SERVER['REQUEST_URI'],$output);
				}else{
					$this->display();
				}
		
		
		}

	}
