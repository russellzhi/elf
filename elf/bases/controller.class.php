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
	final function show($cache){
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
	
    /**
     * 返回请求使用的方法
     *
     * @return string
     */
    function requestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否是 GET 请求
     *
     * @return boolean
     */
    function isGET()
    {
        return $this->requestMethod() == 'GET';
    }

    /**
     * 是否是 POST 请求
     *
     * @return boolean
     */
    function isPOST()
    {
        return $this->requestMethod() == 'POST';
    }

    /**
     * 是否是 PUT 请求
     *
     * @return boolean
     */
    function isPUT()
    {
        return $this->requestMethod() == 'PUT';
    }

    /**
     * 是否是 DELETE 请求
     *
     * @return boolean
     */
    function isDELETE()
    {
        return $this->requestMethod() == 'DELETE';
    }

    /**
     * 是否是 HEAD 请求
     *
     * @return boolean
     */
    function isHEAD()
    {
        return $this->requestMethod() == 'HEAD';
    }
	/**
     * 返回 HTTP 请求头中的指定信息，如果没有指定参数则返回 false
     *
     * @param string $header 要查询的请求头参数
     *
     * @return string 参数值
     */
    function header($header)
    {
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (!empty($_SERVER[$temp])){
        	return $_SERVER[$temp];
        } 
        if (function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();
            if (!empty($headers[$header])) return $headers[$header];
        }
        return false;
    }
    /**
     * 判断 HTTP 请求是否是通过 XMLHttp 发起的
     *
     * @return boolean
     */
    function isAJAX()
    {
        return strtolower($this->header('X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    /**
     * 判断 HTTP 请求是否是通过 Flash 发起的
     *
     * @return boolean
     */
    function isFlash()
    {
        return strtolower($this->header('USER_AGENT')) == 'shockwave flash';
    }

    /**
     * 返回请求的原始内容
     *
     * @return string
     */
    function requestRawBody()
    {
        $body = file_get_contents('php://input');
        return (strlen(trim($body)) > 0) ? $body : false;
    }

    /**
     * 返回当前请求的参照 URL
     *
     * @return string 当前请求的参照 URL
     */
    function referer()
    {
        return $this->header('REFERER');
    }
	
	function message($msg=''){
		echo '<div id="elf_message">'.$msg.'</div>';
	}

}
