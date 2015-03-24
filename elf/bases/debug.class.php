<?php	
/******************************************************************************************

	-----	|		-----		Hello buddy,you are awesome to choose Elf Framework
	|		|		|		It is very light-weight,with high performance,you will like it.
	|----	|		|----	The only pity is it's php 5.3 + only
	|		|		|		Author	:	Bitchzhi
	-----	-----	|		Date	:	Mar. 21, 2014
	
 *****************************************************************************************/
namespace Elf;
class Debug {
	static $includefile=array();
	static $info=array();
	static $sqls=array();
	static $startTime;
	static $stopTime;
	
	static $msg = array(
			 E_WARNING=>'运行时警告',
			 E_NOTICE=>'运行时提醒',
			 E_STRICT=>'编码标准化警告',
			 E_USER_ERROR=>'自定义错误',
			 E_USER_WARNING=>'自定义警告',
			 E_USER_NOTICE=>'自定义提醒',
			 'Unkown '=>'未知错误'
	 );

	static function start(){                       
		self::$startTime = microtime(true);
	}

	static function stop(){
		self::$stopTime= microtime(true);
	}

	static function spent(){
		return round((self::$stopTime - self::$startTime) , 4);
	}

	static function Catcher($errno, $errstr, $errfile, $errline){
		if(!isset(self::$msg[$errno])) 
			$errno='Unkown';

		if($errno==E_NOTICE || $errno==E_USER_NOTICE)
			$color="#000088";
		else
			$color="red";

		$mess='<font color='.$color.'>';
		$mess.='<b>'.self::$msg[$errno]."</b>[在文件 {$errfile} 中,第 $errline 行]:";
		$mess.=$errstr;
		$mess.='</font>'; 		
		self::addMsg($mess);
	}

	static function addmsg($msg,$type=0) {
		if(defined("DEBUG") && DEBUG==1){
			switch($type){
				case 0:
					self::$info[]=$msg;
					break;
				case 1:
					self::$includefile[]=$msg;
					break;
				case 2:
					self::$sqls[]=$msg;
					break;
			}
		}
	}

	static function message(){
		$mess='';
		$mess.= '<div style="float:left;clear:both;text-align:left;font-size:11px;color:#888;width:95%;margin:10px;padding:10px;background:#F5F5F5;border:1px dotted #778855;z-index:100">';
		$mess.= '<div style="float:left;width:100%;"><span style="float:left;width:200px;"><b>Runtime</b>( <font color="red">'.self::spent().' </font>s):</span><span onclick="this.parentNode.parentNode.style.display=\'none\'" style="cursor:pointer;float:right;width:35px;background:#500;border:1px solid #555;color:white">关闭X</span></div><br>';
		$mess.= '<ul style="margin:0px;padding:0 10px 0 10px;list-style:none">';
		if(count(self::$includefile) > 0){
			$mess.= '［Autoload］';
			foreach(self::$includefile as $file){
				$mess.= '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$file.'</li>';
			}		
		}
		if(count(self::$info) > 0 ){
			$mess.= '<br>［System］';
			foreach(self::$info as $info){
				$mess.= '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$info.'</li>';
			}
		}

		if(count(self::$sqls) > 0) {
			$mess.= '<br>［SQL语句］';
			foreach(self::$sqls as $sql){
				$mess.= '<li>&nbsp;&nbsp;&nbsp;&nbsp;'.$sql.'</li>';
			}
		}
		$mess.= '</ul>';
		$mess.= '</div>';	

		return $mess;
	}
}
