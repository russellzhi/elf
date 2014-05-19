<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
	class Prourl {

		static function parseUrl(){
			if (isset($_SERVER['PATH_INFO'])){
				$pathinfo = explode('/', trim($_SERVER['PATH_INFO'], "/"));
       				$_GET['m'] = (!empty($pathinfo[0]) ? $pathinfo[0]."Controller" : 'indexController');
       				array_shift($pathinfo);
       				$_GET['a'] = (!empty($pathinfo[0]) ? $pathinfo[0] : 'index');
				array_shift($pathinfo);

				for($i=0; $i<count($pathinfo); $i+=2){
					$_GET[$pathinfo[$i]]=$pathinfo[$i+1];
				}
			
			}else{	
					
				if(!isset($_GET["m"]))
					$_GET["m"]="indexController";
				else
					$_GET["m"]=$_GET["m"]."Controller";
				if(!isset($_GET["a"]))
					$_GET["a"]="index";	
			}
		}
	}
